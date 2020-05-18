<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Role extends BaseModel
{
    protected $guarded = [];

    protected $fillable = [
        'name', 'display_name'
    ];

    /**
     * The attributes that can be ordered on
     *
     * @var array
     */
    protected $sortable = ['name', 'display_name', 'created_at'];

    /**
     * Get Role Users
     *
     * @return Model
     */
    public function users()
    {
        $userModel = User::class;

        return $this->belongsToMany($userModel, 'user_roles')
            ->select(app($userModel)->getTable() . '.*')
            ->union($this->hasMany($userModel))->getQuery();
    }

    /**
     * Get Role Permissions
     *
     * @return Model
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Check Permission
     *
     * @param string $name
     * @return boolean
     */
    public function hasPermission($name)
    {
        $_permissions = $this->permissions()
            ->pluck('name')->unique()->toArray();

        return in_array($name, $_permissions);
    }

    public function scopeIsRoot($query, $user)
    {
        return $user->hasRole('root') ? $query : $query->where('name', '!=', 'root');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::slug($value, '-');
    }

    public function setDisplayNameAttribute($value)
    {
        $this->attributes['display_name'] = Str::title($value);
    }
}
