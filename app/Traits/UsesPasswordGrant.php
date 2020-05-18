<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait UsesPasswordGrant
{

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return Model
     */
    public function findForPassports($username)
    {
        return $this->where('email', $username)
            ->where('status', true)
            ->first();
    }
}
