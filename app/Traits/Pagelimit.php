<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Input;

trait Pagelimit
{
    /**
     * Set Page Limit from Query String
     *
     * @return void
     */
    public function scopeSetLimit() {
        $this->perPage = getPageLimit();
    }

    /**
     * Set Page Limit and paginate from Query String
     *
     * @return void
     */
    public function scopeLimitAndPaginate(Builder $query) {
        if (request()->has('per_page') && request()->query('per_page') === 'all') {
            $this->all();
        } else {
            $this->perPage = getPageLimit();
            $query->paginate();
        }
    }

    // /**
    //  * Set Page Limit from Query String
    //  *
    //  * @return void
    //  */
    // public function scopeNoLimit() {
    //     $this->perPage = -1;
    // }
}
