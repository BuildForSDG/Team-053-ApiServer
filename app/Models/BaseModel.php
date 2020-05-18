<?php

namespace App\Models;

use App\Traits\Pagelimit;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use Sortable, Pagelimit;

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
    protected $sortable = ['created_at'];

}
