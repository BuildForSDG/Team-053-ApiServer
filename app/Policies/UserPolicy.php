<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy extends BasePolicy
{
    /**
     * Determine if the given model can be viewed by the user.
     *
     * @param \Admin\Models\User $user
     * @param  $model
     *
     * @return bool
     */
    public function read(User $user, $model)
    {
        // Does this record belong to the current user?
        $current = $user->id === $model->id;

        return $current || $this->checkPermission($user, $model, 'read');
    }

    /**
     * Determine if the given model can be edited by the user.
     *
     * @param \Admin\Models\User $user
     * @param  $model
     *
     * @return bool
     */
    public function edit(User $user, $model)
    {
        // Does this record belong to the current user?
        $current = $user->id === $model->id;

        return $current || $this->checkPermission($user, $model, 'edit');
    }

    /**
     * Determine if the given model can be deleted by the user.
     *
     * @param \Admin\Models\User $user
     * @param  $model
     *
     * @return bool
     */
    public function delete(User $user, $model)
    {
        // Does this record belong to the current user?
        $current = $user->id === $model->id;

        if ($current) {
            return Response::deny();
        }

        return $this->checkPermission($user, $model, 'delete') || $user->hasRole('root');
    }

    /**
     * Determine if the given user can change a user a role.
     *
     * @param \Admin\Models\User $user
     * @param  $model
     *
     * @return bool
     */
    public function editRoles(User $user, $model)
    {
        // Does this record belong to another user?
        $another = $user->id != $model->id;

        return $another && $user->hasPermission('edit.users');
    }

    /**
     * Checked Before other policies
     *
     * @param  User  $user
     * @param  String $action
     *
     * @return void
     */
    public function before(User $user, $action)
    {
        if ($action !== 'delete') {
            if ($user->hasRole('root')) {
                return true;
            }
        }
    }
}
