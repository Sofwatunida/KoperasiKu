<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'username.unique' => __('users.username_taken'),
            'email.unique' => __('settings.email_taken'),
            'email.email' => __('settings.invalid_email'),
            'password.min' => __('settings.password_min'),
            'password.confirmed' => __('settings.password_mismatch'),
        ]);

        // Role tidak pernah diubah pada halaman ini. Perubahan email/password
        // wajib dikonfirmasi dengan password saat ini.
        $emailChanged = $validated['email'] !== $user->email;
        $passwordChanged = ! empty($validated['password']);

        if ($emailChanged || $passwordChanged) {
            if (empty($validated['current_password']) || ! Hash::check($validated['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => __('settings.current_password_invalid'),
                ]);
            }
        }

        $user->fill([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
        ]);

        if ($passwordChanged) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('settings.index')->with('success', __('settings.updated'));
    }

    /**
     * Reset seluruh data operasional: riwayat transaksi dan produk dikosongkan
     * sehingga koperasi mulai dari awal. Akun pengguna tidak dihapus.
     * Hanya dapat dijalankan oleh super admin (lihat middleware super_admin).
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'confirmation' => ['required', 'string', 'in:RESET'],
        ], [
            'confirmation.required' => __('settings.reset_confirmation_required'),
            'confirmation.in' => __('settings.reset_confirmation_invalid'),
        ]);

        $deleted = DB::transaction(function (): array {
            $transactions = Transaction::query()->count();
            $products = Product::query()->count();

            // Urutan penting: detail dulu, lalu transaksi, terakhir produk
            // agar tidak melanggar foreign key.
            TransactionDetail::query()->delete();
            Transaction::query()->delete();
            Product::query()->delete();

            return ['transactions' => $transactions, 'products' => $products];
        });

        return redirect()->route('settings.index')->with(
            'success',
            __('settings.reset_success', $deleted)
        );
    }

    /**
     * Unduh backup seluruh data (pengguna, produk, transaksi + detail) sebagai
     * berkas JSON. Struktur berkas sama dengan yang diterima oleh import().
     */
    public function backup(): Response
    {
        $json = json_encode($this->backupPayload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $filename = 'backup-koperasiku-'.now()->format('Ymd-His').'.json';

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($json),
        ]);
    }

    /**
     * Pulihkan data dari berkas backup JSON. Seluruh data operasional saat ini
     * (pengguna, produk, transaksi, detail) digantikan oleh isi berkas,
     * dijalankan dalam satu transaksi database agar tidak setengah jadi.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'max:10240', 'mimes:json,txt'],
        ], [
            'backup_file.required' => __('import.file_required'),
            'backup_file.file' => __('import.file_required'),
            'backup_file.mimes' => __('import.invalid'),
        ]);

        $contents = (string) file_get_contents($request->file('backup_file')->getRealPath());
        $payload = json_decode($contents, true);

        if (! is_array($payload)) {
            throw ValidationException::withMessages([
                'backup_file' => __('import.invalid'),
            ]);
        }

        $data = $this->validateBackupPayload($payload);

        $counts = DB::transaction(function () use ($data): array {
            // Urutan penting: detail dulu, lalu transaksi, produk, terakhir
            // pengguna agar tidak melanggar foreign key.
            TransactionDetail::query()->delete();
            Transaction::query()->delete();
            Product::query()->delete();
            User::query()->delete();

            foreach ($data['users'] as $user) {
                User::query()->insert($user);
            }

            foreach ($data['products'] as $product) {
                Product::query()->insert($product);
            }

            foreach ($data['transactions'] as $transaction) {
                $details = $transaction['details'];
                unset($transaction['details']);

                $transactionId = Transaction::query()->insertGetId($transaction);

                foreach ($details as $detail) {
                    TransactionDetail::query()->insert($detail + ['transaction_id' => $transactionId]);
                }
            }

            return [
                'users' => count($data['users']),
                'products' => count($data['products']),
                'transactions' => count($data['transactions']),
            ];
        });

        // Sesi login tetap valid selama akun yang masuk ikut dipulihkan dari
        // berkas backup. Jika tidak ada, paksa keluar agar tidak error 500.
        if (! User::query()->whereKey(Auth::id())->exists()) {
            Auth::logout();
        }

        return redirect()->route('settings.index')->with(
            'success',
            __('settings.import_success', $counts)
        );
    }

    /**
     * Susunan data backup (dipakai bersama oleh backup dan import).
     *
     * @return array<string, mixed>
     */
    private function backupPayload(): array
    {
        return [
            'app' => 'KoperasiKu',
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'users' => User::query()
                ->orderBy('id')
                ->get()
                ->map(fn (User $user) => [
                    // Ambil atribut mentah agar password (yang biasanya
                    // disembunyikan) ikut terbackup dan bisa dipulihkan.
                    ...$user->getAttributes(),
                    'created_at' => $user->created_at?->toDateTimeString(),
                    'updated_at' => $user->updated_at?->toDateTimeString(),
                ])
                ->all(),
            'products' => Product::query()
                ->orderBy('id')
                ->get()
                ->map(fn (Product $product) => $product->toArray())
                ->all(),
            'transactions' => Transaction::query()
                ->with('details')
                ->orderBy('id')
                ->get()
                ->map(fn (Transaction $transaction) => [
                    'transaction_code' => $transaction->transaction_code,
                    'user_id' => $transaction->user_id,
                    'total' => $transaction->total,
                    'paid' => $transaction->paid,
                    'change' => $transaction->change,
                    'transaction_date' => $transaction->transaction_date?->toDateTimeString(),
                    'created_at' => $transaction->created_at?->toDateTimeString(),
                    'updated_at' => $transaction->updated_at?->toDateTimeString(),
                    'details' => $transaction->details
                        ->map(fn (TransactionDetail $detail) => [
                            'product_id' => $detail->product_id,
                            'quantity' => $detail->quantity,
                            'price' => $detail->price,
                            'subtotal' => $detail->subtotal,
                            'created_at' => $detail->created_at?->toDateTimeString(),
                            'updated_at' => $detail->updated_at?->toDateTimeString(),
                        ])
                        ->all(),
                ])
                ->all(),
        ];
    }

    /**
     * Validasi isi berkas backup lalu rapikan baris-barisnya menjadi array
     * siap insert (kolom + timestamp saja, tanpa id agar urutannya aman
     * untuk semua driver database).
     *
     * @param  array<string, mixed>  $payload
     * @return array{users: array<int, array<string, mixed>>, products: array<int, array<string, mixed>>, transactions: array<int, array<string, mixed>>}
     */
    private function validateBackupPayload(array $payload): array
    {
        foreach (['users', 'products', 'transactions'] as $section) {
            if (! isset($payload[$section]) || ! is_array($payload[$section])) {
                throw ValidationException::withMessages([
                    'backup_file' => __('import.invalid_structure'),
                ]);
            }
        }

        $users = [];

        foreach (array_values($payload['users']) as $index => $row) {
            if (! is_array($row) || ! isset($row['id'], $row['name'], $row['username'], $row['email'], $row['password'], $row['role'])) {
                throw ValidationException::withMessages([
                    'backup_file' => __('import.invalid_structure').' (users #'.($index + 1).')',
                ]);
            }

            $users[] = [
                'id' => (int) $row['id'],
                'name' => (string) $row['name'],
                'username' => (string) $row['username'],
                'email' => (string) $row['email'],
                'password' => (string) $row['password'],
                'role' => in_array($row['role'], User::ROLES, true) ? (string) $row['role'] : User::ROLE_KASIR,
                'created_at' => $row['created_at'] ?? now(),
                'updated_at' => $row['updated_at'] ?? now(),
            ];
        }

        $products = [];

        foreach (array_values($payload['products']) as $index => $row) {
            if (! is_array($row) || ! isset($row['id'], $row['code'], $row['name'], $row['selling_price'], $row['stock'])) {
                throw ValidationException::withMessages([
                    'backup_file' => __('import.invalid_structure').' (products #'.($index + 1).')',
                ]);
            }

            $products[] = [
                'id' => (int) $row['id'],
                'code' => (string) $row['code'],
                'name' => (string) $row['name'],
                'purchase_price' => (int) ($row['purchase_price'] ?? 0),
                'selling_price' => (int) $row['selling_price'],
                'stock' => (int) $row['stock'],
                'unit' => in_array($row['unit'] ?? null, ProductController::SATUAN, true) ? (string) $row['unit'] : 'pcs',
                'created_at' => $row['created_at'] ?? now(),
                'updated_at' => $row['updated_at'] ?? now(),
            ];
        }

        $userIds = array_column($users, 'id');
        $productIds = array_column($products, 'id');
        $transactions = [];

        foreach (array_values($payload['transactions']) as $index => $row) {
            if (! is_array($row) || ! isset($row['transaction_code'], $row['total'], $row['paid']) || ! isset($row['details']) || ! is_array($row['details'])) {
                throw ValidationException::withMessages([
                    'backup_file' => __('import.invalid_structure').' (transactions #'.($index + 1).')',
                ]);
            }

            $userId = $row['user_id'] ?? null;

            if ($userId !== null && ! in_array($userId, $userIds, true)) {
                $userId = null;
            }

            $details = [];

            foreach (array_values($row['details']) as $detailIndex => $detail) {
                if (! is_array($detail) || ! isset($detail['product_id'], $detail['quantity'], $detail['price'], $detail['subtotal'])) {
                    throw ValidationException::withMessages([
                        'backup_file' => __('import.invalid_structure').' (transactions #'.($index + 1).', details #'.($detailIndex + 1).')',
                    ]);
                }

                $details[] = [
                    'product_id' => (int) $detail['product_id'],
                    'quantity' => (int) $detail['quantity'],
                    'price' => (int) $detail['price'],
                    'subtotal' => (int) $detail['subtotal'],
                    'created_at' => $detail['created_at'] ?? now(),
                    'updated_at' => $detail['updated_at'] ?? now(),
                ];
            }

            $transactions[] = [
                'transaction_code' => (string) $row['transaction_code'],
                'user_id' => $userId !== null ? (int) $userId : null,
                'total' => (int) $row['total'],
                'paid' => (int) $row['paid'],
                'change' => (int) ($row['change'] ?? 0),
                'transaction_date' => $row['transaction_date'] ?? now(),
                'created_at' => $row['created_at'] ?? now(),
                'updated_at' => $row['updated_at'] ?? now(),
                'details' => $details,
                '_product_ids' => $productIds,
            ];
        }

        // Rapikan: pastikan product_id pada detail memang ada di daftar produk.
        foreach ($transactions as &$transaction) {
            foreach ($transaction['details'] as &$detail) {
                if (! in_array($detail['product_id'], $transaction['_product_ids'], true)) {
                    throw new RuntimeException(__('import.invalid_structure'));
                }
            }
            unset($detail);

            unset($transaction['_product_ids']);
        }
        unset($transaction);

        return [
            'users' => $users,
            'products' => $products,
            'transactions' => $transactions,
        ];
    }
}
