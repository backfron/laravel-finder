<?php

namespace Backfron\LaravelFinder\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterContract
{
    /**
     * Apply a given search value to the query builder instance.
     *
     * @param Builder $builder
     * @param mixed $value
     * @return Builder $builder
     */
    public static function apply(Builder $query, $value);
}
