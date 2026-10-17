<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public const ROLES = User::ROLES;

    public function index(Request $request): View
    {
        $keyword = $request->string('search')->trim()->toString();

        $users = User::query()
            ->when($keyword !== '', fn ($query) => $query->where(function ($query) use ($keyword): void {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('username', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            }))
            ->orderByRaw("CASE role WHEN 'super_admin' THEN 0 WHEN 'admin' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'search' => $keyword,
            'totalSuperAdmin' => User::query()->where('role', User::ROLE_SUPER_ADMIN)->count(),
            'totalAdmin' => User::query()->where('role', User::ROLE_ADMIN)->count(),
            'totalKasir' => User::query()->where('role', User::ROLE_KASIR)->count(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('users.create', [
            'roles' => $this->availableRoles($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules($request->user()));

        User::create($data);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->ensureCanManage($request->user(), $user);

        return view('users.edit', [
            'user' => $user,
            'roles' => $this->availableRoles($request->user()),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManage($request->user(), $user);

        $data = $request->validate($this->rules($request->user(), $user));

        if ($user->isAdmin() && $data['role'] !== User::ROLE_ADMIN && User::isLastAdmin($user)) {
            return redirect()->route('users.edit', $user)->with('error', 'Minimal harus ada satu admin yang aktif.');
        }

        if ($user->isSuperAdmin() && $data['role'] !== User::ROLE_SUPER_ADMIN && User::isLastSuperAdmin($user)) {
            return redirect()->route('users.edit', $user)->with('error', 'Minimal harus ada satu super admin yang aktif.');
        }

        $user->fill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManage($request->user(), $user);

        if ($user->is($request->user())) {
            return redirect()->route('users.index')->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        if ($user->isAdmin() && User::isLastAdmin($user)) {
            return redirect()->route('users.index')->with('error', 'Minimal harus ada satu admin yang aktif.');
        }

        if ($user->isSuperAdmin() && User::isLastSuperAdmin($user)) {
            return redirect()->route('users.index')->with('error', 'Minimal harus ada satu super admin yang aktif.');
        }

        if ($user->transactions()->exists()) {
            return redirect()->route('users.index')->with('error', 'Akun tidak bisa dihapus karena memiliki riwayat transaksi.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Role yang boleh ditetapkan oleh pengelola yang sedang masuk.
     *
     * @return array<int, string>
     */
    private function availableRoles(User $actor): array
    {
        if ($actor->isSuperAdmin()) {
            return self::ROLES;
        }

        return [User::ROLE_ADMIN, User::ROLE_KASIR];
    }

    /**
     * Hanya super admin yang boleh mengelola akun super admin.
     */
    private function ensureCanManage(User $actor, User $target): void
    {
        if ($target->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            abort(403, 'Akun super admin hanya dapat dikelola oleh super admin.');
        }
    }

    /**
     * Aturan validasi form pengguna (dipakai bersama oleh store dan update).
     *
     * @return array<string, array<int, string|Rule>>
     */
    private function rules(User $actor, ?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'max:50',
                Rule::unique('users', 'username')->ignore($user?->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'role' => ['required', 'string', Rule::in($this->availableRoles($actor))],
            'password' => $user === null
                ? ['required', 'string', 'min:8', 'confirmed']
                : ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
