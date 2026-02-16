<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'landlord';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'two_factor_secret',
        'two_factor_enabled',
        'allowed_ips',
        'ip_restriction_enabled',
        'permissions',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'ip_restriction_enabled' => 'boolean',
            'allowed_ips' => 'array',
            'permissions' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'admin_site_access');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AdminLog::class);
    }

    public function isMaster(): bool
    {
        return $this->role === 'master';
    }

    public function canAccessSite(int $siteId): bool
    {
        if ($this->isMaster()) {
            return true;
        }

        return $this->sites()->where('sites.id', $siteId)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isMaster()) {
            return true;
        }

        return in_array($permission, $this->permissions ?? [], true);
    }
}
