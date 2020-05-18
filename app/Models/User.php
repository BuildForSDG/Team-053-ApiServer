<?php

namespace App\Models;

use App\Traits\Sortable;
use App\Traits\Pagelimit;
use App\Traits\UsesPasswordGrant;
use Laravel\Passport\HasApiTokens;
use App\Traits\HasRolesPermissions;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,
        Notifiable,
        HasRolesPermissions,
        UsesPasswordGrant,
        Sortable,
        Pagelimit;

    /**
     * The sort parameter used in the query string
     *
     * @var array
     */
    protected $sortParameterName = 'sort';

    /**
     * The sort direction used in the query string
     *
     * @var array
     */
    protected $sortDirectionName = 'direction';

    /**
     * The attributes that can be ordered on
     *
     * @var array
     */
    protected $sortable = ['name', 'email', 'status', 'created_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'status', 'avatar', 'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set default attributes values.
     *
     * @var array
     */
    protected $attributes = [
        'avatar' => 'users/default.png',
    ];

    public function loadPermissions()
    {
        return $this->loadPermissionsRelations();
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public static function withRoles()
    {
        return self::with([
            'role' => function ($rQ) {
                $rQ->select('id', 'name', 'display_name');
            },
            'roles' => function ($rQ) {
                $rQ->select('id', 'name', 'display_name');
            }
        ]);
    }

    public function scopeCheckRoot($query, $user)
    {
        $rootRole = Role::where('name', 'root')->get()->first();

        return $user->hasRole('root') ? $query :
            $query->where('role_id', '!=', $rootRole->id ?? 1);
    }

}
