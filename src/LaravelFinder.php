<?php

namespace Backfron\LaravelFinder;

use Closure;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Backfron\LaravelFinder\Exceptions\FilterNotFoundException;

class LaravelFinder
{
    protected $model;

    protected $filters = [];

    protected $globalFilters = [];

    protected $query;

    protected static $swapMethods = [
        'filters' => 'addFilters',
        'global' => 'addGlobalFilters',
    ];

    public function __construct()
    {
        $this->query = $this->newQuery();
    }

    /**
     * Add filters
     *
     * @param array $filters
     * @param mixed $value
     * @return self
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

    /**
     * Add global filters
     *
     * @param Closure $filters
     * @return self
     */
    public function addGlobalFilters(Closure|array|string $filters, $value = "")
    {

        if ($filters instanceof Closure) {
            $filters = (array) $filters;
        }

        if (is_string($filters)) {
            $filters = [
                [$filters, $value]
            ];
        }

        $this->globalFilters = $filters;

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
                $filterClassName::apply($this->query, $filterValue);
            }
        }

        return $this;
    }

    /**
     * Applies global filters to the query
     *
     * @return self
     */
    protected function applyGlobalFilters()
    {
        foreach ($this->globalFilters as $filter) {

            if ($filter instanceof Closure) {
                $filter($this->query);
            }

            if (is_array($filter)) {
                $filterClass = new $filter[0];
                $filterClass::apply($this->query, $filter[1]);
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
        if (array_key_exists($method, static::$swapMethods)) {
            $method = static::$swapMethods[$method];
            return (new static)->$method(...$parameters);
        }

        $message = 'Method "%s" not found.';

        throw new BadMethodCallException(sprintf($message, $method));
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->model, $method) OR method_exists(QueryBuilder::class, $method)) {

            $this->applyFilters()
                ->applyGlobalFilters();

            if (empty($parameters)) {
                return call_user_func([$this->query, $method]);
            }

            return call_user_func([$this->query, $method], ...$parameters);
        }

        if (array_key_exists($method, static::$swapMethods)) {
            $method = static::$swapMethods[$method];
            return $this->$method(...$parameters);
        }

        $message = 'Method "%s" not found.';

        throw new BadMethodCallException(sprintf($message, $method));
    }
}
