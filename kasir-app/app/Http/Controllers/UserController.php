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
        $actor = $request->user();
        $keyword = $request->string('search')->trim()->toString();

        $users = User::query()
            ->when(! $actor->isSuperAdmin(), fn ($query) => $query->where('role', User::ROLE_KASIR))
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

        return redirect()->route('users.index')->with('success', __('users.created'));
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
            return redirect()->route('users.edit', $user)->with('error', __('users.last_admin'));
        }

        if ($user->isSuperAdmin() && $data['role'] !== User::ROLE_SUPER_ADMIN && User::isLastSuperAdmin($user)) {
            return redirect()->route('users.edit', $user)->with('error', __('users.last_super_admin'));
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

        return redirect()->route('users.index')->with('success', __('users.updated'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManage($request->user(), $user);

        if ($user->is($request->user())) {
            return redirect()->route('users.index')->with('error', __('users.cannot_delete_self'));
        }

        if ($user->isAdmin() && User::isLastAdmin($user)) {
            return redirect()->route('users.index')->with('error', __('users.last_admin'));
        }

        if ($user->isSuperAdmin() && User::isLastSuperAdmin($user)) {
            return redirect()->route('users.index')->with('error', __('users.last_super_admin'));
        }

        if ($user->transactions()->exists()) {
            return redirect()->route('users.index')->with('error', __('users.has_transactions'));
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', __('users.deleted'));
    }

    /**
     * Role yang boleh ditetapkan oleh pengelola yang sedang masuk.
     *
     * Admin hanya boleh mengelola akun kasir. Super admin dapat mengelola semua role.
     *
     * @return array<int, string>
     */
    private function availableRoles(User $actor): array
    {
        if ($actor->isSuperAdmin()) {
            return self::ROLES;
        }

        return [User::ROLE_KASIR];
    }

    /**
     * Super admin boleh mengelola semua akun. Admin hanya boleh mengelola akun kasir.
     */
    private function ensureCanManage(User $actor, User $target): void
    {
        if ($actor->isSuperAdmin()) {
            return;
        }

        if ($target->isSuperAdmin()) {
            abort(403, __('users.super_admin_only'));
        }

        if (! $target->isKasir()) {
            abort(403, __('users.admin_manages_kasir_only'));
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
