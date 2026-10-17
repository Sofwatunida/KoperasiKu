<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Transaksi yang dilakukan oleh petugas ini.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_KASIR = 'kasir';

    /**
     * Role yang berlaku di sistem. Super admin adalah role tertinggi.
     *
     * @var array<int, string>
     */
    public const ROLES = [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_KASIR];

    /**
     * Label tampilan untuk setiap role.
     *
     * @var array<string, string>
     */
    public const ROLE_LABELS = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_KASIR => 'Kasir',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isKasir(): bool
    {
        return $this->role === self::ROLE_KASIR;
    }

    /**
     * Role yang boleh mengakses halaman pengelolaan (admin + super admin).
     */
    public function hasAdminAccess(): bool
    {
        return $this->isAdmin() || $this->isSuperAdmin();
    }

    /**
     * Label role untuk ditampilkan pada antarmuka.
     */
    public function getRoleLabelAttribute(): string
    {
        return self::ROLE_LABELS[$this->role] ?? ucfirst((string) $this->role);
    }

    /**
     * Cek apakah user admin ini adalah satu-satunya admin yang tersisa.
     */
    public static function isLastAdmin(User $user): bool
    {
        return $user->isAdmin()
            && self::query()->where('role', self::ROLE_ADMIN)->whereKeyNot($user->id)->doesntExist();
    }

    /**
     * Cek apakah user ini satu-satunya super admin yang tersisa.
     */
    public static function isLastSuperAdmin(User $user): bool
    {
        return $user->isSuperAdmin()
            && self::query()->where('role', self::ROLE_SUPER_ADMIN)->whereKeyNot($user->id)->doesntExist();
    }

    /**
     * Inisial untuk avatar pada header.
     */
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        return strtoupper(collect($parts)->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode(''));
    }
}
