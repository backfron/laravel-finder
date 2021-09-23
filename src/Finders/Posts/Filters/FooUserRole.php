<?php

namespace Backfron\LaravelFinder\Finders\Posts\Filters;

use Illuminate\Database\Eloquent\Builder;

class FooUserRole
{
    /**
     * Apply a given search value to the query builder instance.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed $title
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function apply(Builder $query, $role)
    {
        return $query->whereHas('user', fn ($query) => $query->where('role', $role));
    }
}
