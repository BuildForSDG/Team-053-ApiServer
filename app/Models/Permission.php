<?php

namespace App\Models;

use Illuminate\Support\Str;

class Permission extends BaseModel
{
    protected $guarded = [];

    protected $fillable = [
        'name', 'display_name', 'table_name',
    ];

    /**
     * The attributes that can be ordered on
     *
     * @var array
     */
    protected $sortable = ['name', 'created_at', 'display_name'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public static function generateFor($table_name)
    {
        self::firstOrCreate(
            [
                'name' => 'browse.' . $table_name,
                'display_name' => 'Browse ' . Str::title($table_name),
                'table_name' => $table_name
            ]
        );
        self::firstOrCreate(
            [
                'name' => 'read.' . $table_name,
                'display_name' => 'Read ' . Str::title($table_name),
                'table_name' => $table_name
            ]
        );
        self::firstOrCreate(
            [
                'name' => 'edit.' . $table_name,
                'display_name' => 'Edit ' . Str::title($table_name),
                'table_name' => $table_name
            ]
        );
        self::firstOrCreate(
            [
                'name' => 'add.' . $table_name,
                'display_name' => 'Add ' . Str::title($table_name),
                'table_name' => $table_name
            ]
        );
        self::firstOrCreate(
            [
                'name' => 'delete.' . $table_name,
                'display_name' => 'Delete ' . Str::title($table_name),
                'table_name' => $table_name
            ]
        );
    }

    public static function removeFrom($table_name)
    {
        self::where('name', 'LIKE', '%.' . $table_name)->delete();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::lower(Str::slug($value, '-'));
    }

    public function setDisplayNameAttribute($value)
    {
        $this->attributes['display_name'] = Str::title($value);
    }

    public function setTableNameAttribute($value)
    {
        $this->attributes['table_name'] = Str::lower($value);
    }
}
