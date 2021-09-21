<?php

namespace Backfron\LaravelFinder;

use Illuminate\Database\Eloquent\Builder;
use Backfron\LaravelFinder\Exceptions\FilterNotFoundException;

class LaravelFinder
{
    protected static $model;

    /**
     * Apply filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filters(array|string $filters, $value = null)
    {
        if (!is_array($filters)) {
            $filters = [
                $filters => $value,
            ];
        }

        $query = static::newQuery();

        $query = static::applyFilters($filters, $query);

        return $query;
    }

    protected static function newQuery()
    {
        return (new static::$model())->newQuery();
    }

    /**
     * Applies filters to the query
     *
     * @param array $filters
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyFilters(array $filters, Builder $query)
    {
        foreach ($filters as $filterName => $filterValue) {

            $filterClassName = static::getFilterClassName($filterName);

            if (!class_exists($filterClassName) && !config('laravel-finder.ignore-unexisting-filters')) {
                throw new FilterNotFoundException;
            }
            if (class_exists($filterClassName)) {
                $query = $filterClassName::apply($query, $filterValue);
            }
        }

        return $query;
    }

    /**
     * Gets the filter class name based in the filter name
     *
     * @param      string  $urlFilterName  The url filter name
     *
     * @return     string  The filter class name.
     */
    protected static function getFilterClassName($urlFilterName)
    {
        $finder = new \ReflectionClass(static::class);

        return $finder->getNamespaceName() . '\\Filters\\' .
        str_replace(' ', '', ucwords(
            str_replace('_', ' ', $urlFilterName)
        ));
    }
}
