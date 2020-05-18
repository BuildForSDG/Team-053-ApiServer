<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BasePolicy
{
    use HandlesAuthorization;
    protected static $datatypes = [];

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle all requested permission checks.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return bool
     */
    public function __call($name, $arguments)
    {
        if (count($arguments) < 2) {
            throw new \InvalidArgumentException('not enough arguments');
        }

        $user = $arguments[0];

        $model = $arguments[1];

        return $this->checkPermission($user, $model, $name);
    }

    /**
     * Determine if the given model can be restored by the user.
     *
     * @param App\Models\User $user
     * @param  $model
     *
     * @return bool
     */
    public function restore(User $user, $model)
    {
        // Can this be restored?
        return $model->deleted_at && $this->checkPermission($user, $model, 'delete');
    }

    /**
     * Determine if the given model can be deleted by the user.
     *
     * @param \App\Models\User $user
     * @param  $model
     *
     * @return bool
     */
    public function delete(User $user, $model)
    {
        // Has this already been deleted?
        $soft_delete = $model->deleted_at && in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($model));

        return !$soft_delete && $this->checkPermission($user, $model, 'delete');
    }

    /**
     * Check if user has an associated permission.
     *
     * @param \App\Models\User $user
     * @param object                      $model
     * @param string                      $action
     *
     * @return bool
     */
    protected function checkPermission(User $user, $model, $action)
    {
        dd($user);
        // if (!isset(self::$datatypes[get_class($model)])) {
        //     self::$datatypes[get_class($model)] = get_class($model);
        // }

        // $dataType = self::$datatypes[get_class($model)];
        $name = $model->getTable();

        return $user->hasPermission($action . '.' . $name);
    }

    /**
     * Checked Before other policies
     *
     * @param  User  $user
     * @param  Class $model
     * @return void
     */
    public function before(User $user, $model)
    {
        if ($user->hasRole('root')) {
            return true;
        }
    }
}
