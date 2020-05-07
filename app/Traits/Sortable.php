<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    /**
     * Get if using multi-sorting.
     *
     * @return array
     */
    public function getMultiSort()
    {
        return property_exists($this, 'multiSort') ? $this->multiSort : false;
    }

    /**
     * Get the sortable parameter used in the query string.
     *
     * @return array
     */
    public function getSortParameterName()
    {
        return property_exists($this, 'sortParameterName') ? $this->sortParameterName : 'sort';
    }

    /**
     * Get the sortable direction used in the query string.
     *
     * @return array
     */
    public function getSortDirectionName()
    {
        return property_exists($this, 'sortDirectionName') ? $this->sortDirectionName : 'direction';
    }

    /**
     * Get the sortable attributes for the model.
     *
     * @return array
     */
    public function getSortable()
    {
        return property_exists($this, 'sortable') ? $this->sortable : array();
    }

    /**
     *  Determine if the given attribute may be sorted on.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isSortable($key)
    {
        return (bool) in_array($key, $this->getSortable());
    }

    /**
     * Sort
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string|null                           $sort    Optional sort string
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeSort(Builder $builder, $sort = null)
    {
        $direction = request()->query($this->getSortDirectionName(), 'desc');

        if ((is_null($sort) || empty($sort)) && request()->query($this->getSortParameterName())) {
            $sort = request()->query($this->getSortParameterName());
        }

        if ($this->getMultiSort()) {
            if (! is_null($sort)) {
                $sort = explode(',', $sort);

                foreach ($sort as $field) {
                    $field = trim($field);
                    $order = 'asc';
                    switch ($field[0]) {
                        case '-':
                            $field = substr($field, 1);
                            $order = 'desc';
                            break;
                        case '+':
                            $field = substr($field, 1);
                            break;
                    }

                    $field = trim($field);

                    if (in_array($field, $this->getSortable())) {
                        $builder->orderBy($field, $order);
                    }
                }
            }
        } else {
            if ($sort && $direction) {
                $builder->orderBy($sort, $direction);
            }
        }
    }
}
