<?php

namespace Backfron\LaravelFinder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Backfron\LaravelFinder\Exceptions\FilterNotFoundException;
use BadMethodCallException;

class LaravelFinder
{
    protected $model;

    protected $filters = [];

    protected $query;

    protected static $swapMethods = [
        'filters' => 'addFilters',
    ];

    public function __construct()
    {
        $this->query = $this->newQuery();
    }

    /**
     * Apply filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function addFilters(array|string $filters, $value = null)
    {
        if (!is_array($filters)) {
            $this->filters = [
                $filters => $value,
            ];
        } else {
            $this->filters = $filters;
        }

        return $this;
    }

    protected function newQuery()
    {
        return (new $this->model)->newQuery();
    }

    /**
     * Applies filters to the query
     *
     * @return self
     */
    protected function applyFilters()
    {
        foreach ($this->filters as $filterName => $filterValue) {

            $filterClassName = $this->getFilterClassName($filterName);

            if (!class_exists($filterClassName) && !config('laravel-finder.ignore-unexisting-filters')) {
                throw new FilterNotFoundException;
            }
            if (class_exists($filterClassName)) {
                $this->query = $filterClassName::apply($this->query, $filterValue);
            }
        }

        return $this;
    }

    /**
     * Gets the filter class name based in the filter name
     *
     * @param      string  $urlFilterName  The url filter name
     *
     * @return     string  The filter class name.
     */
    protected function getFilterClassName($urlFilterName)
    {
        $finder = new \ReflectionClass(static::class);

        return $finder->getNamespaceName() . '\\Filters\\' .
        str_replace(' ', '', ucwords(
            str_replace('_', ' ', $urlFilterName)
        ));
    }

    public static function __callStatic($method, $parameters)
    {
        $method = static::$swapMethods[$method];
        return (new static)->$method(...$parameters);
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->model, $method) OR method_exists(QueryBuilder::class, $method)) {

            $this->applyFilters();

            if (empty($parameters)) {
                return call_user_func([$this->query, $method]);
            }

            return call_user_func([$this->query, $method], ...$parameters);
        }

        $message = 'Method "%s" not found.';

        throw new BadMethodCallException(sprintf($message, $method));
    }
}
