<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
    ];

    // Role constants
    public const ROLE_ADMIN = 'admin';
    public const ROLE_KASIR = 'kasir';
    public const ROLE_BOARDING = 'boarding';

    // Role helper methods
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isKasir()
    {
        return $this->role === self::ROLE_KASIR;
    }

    public function isBoarding()
    {
        return $this->role === self::ROLE_BOARDING;
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function canAccessRoute($routeName)
    {
        $adminRoutes = ['destinations.*', 'schedules.*', 'users.*', 'reports.*'];
        $kasirRoutes = ['destinations.index', 'schedules.index', 'transactions.*', 'tickets.*'];
        $boardingRoutes = ['schedules.index', 'tickets.validate', 'boarding.*'];

        switch ($this->role) {
            case self::ROLE_ADMIN:
                return true; // Admin can access everything
            case self::ROLE_KASIR:
                return $this->matchesRoutes($routeName, $kasirRoutes);
            case self::ROLE_BOARDING:
                return $this->matchesRoutes($routeName, $boardingRoutes);
            default:
                return false;
        }
    }

    private function matchesRoutes($routeName, $allowedRoutes)
    {
        foreach ($allowedRoutes as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }
        return false;
    }

    public function getRoleDisplayName()
    {
        switch($this->role) {
            case self::ROLE_ADMIN:
                return 'Administrator';
            case self::ROLE_KASIR:
                return 'Kasir';
            case self::ROLE_BOARDING:
                return 'Boarding Officer';
            default:
                return ucfirst($this->role);
        }
    }
}
