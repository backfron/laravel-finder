<?php

namespace Backfron\LaravelFinder\Finders\Posts\Filters;

use Illuminate\Database\Eloquent\Builder;
use Backfron\LaravelFinder\Contracts\FilterContract;

class FooTitle implements FilterContract
{
    /**
     * Apply a given search value to the query builder instance.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed $title
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function apply(Builder $query, $title)
    {
        return $query->where('title', 'like', "%{$title}%");
    }
}
