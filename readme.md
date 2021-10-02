# LaravelFinder

`backfron/laravel-finder` provides an easy way to build complex database searchs in a scalable way using ELoquent. If you have a form with a bunch of fields by which you must filter your data, LaravelFinder maybe suit your needs.

```php

// Instead of something like these

$companies = (new App\Models\User)->newQuery();

if (request()->has('city')) {
    $companies->where('city', request('city'));
}

if (request()->has('status')) {
    $companies->where('status', request('status'));
}

if (request()->has('employess_number')) {
    $companies->where('employess_number', '<=' ,request('employess_number'));
}

// Now you can handle it like these

$clients = CompanyFinder::filters([
    'city' => request('city'),
    'status' => request('status'),
    'employees_number' => request('employees_number'),
])->get();
```

These package was inspired by Amo Chohan's [article](https://m.dotdev.co/writing-advanced-eloquent-search-query-filters-de8b6c2598db).

## Installation

Via Composer

``` bash
$ composer require backfron/laravel-finder
```

## Usage

### Finders
Lets imagine that we need to build a search feature for tasks. You have a `Task` model and want to perform a query by one or many conditions like: status, creation date, owner, finish date, etc. To To do so, you may create a **Finder**. A Finder is basicaly a class where we can define the model for which we will be building the search. LaravelFinder offers an artisan command for quickly scaffold Finders.

```bash
php artisan make:finder TaskFinder --model=Task
```

These command will create a new Finder located at `app/Finders/Tasks/TaskFinder.php`. If you inspect the file you will see that is a very short class that basically stores the model with which we will build the query.

```php
namespace App\Finders\Tasks;

use App\Models\Task;
use Backfron\LaravelFinder\LaravelFinder;

class TaskFinder extends LaravelFinder
{
    protected static $model = Task::class;

}
```

### Filters

Next we need to create the **Filters**. Filters are where we will write the logic for our query. Each filter should handle a single field condition. For example, if we need to search a Task by their status, we just need to create a filter for that purpouse. LaravelFinder offers an artisan command for quickly scaffold your filters. You must specify the name of the Filter and the model to which it will be applied. The name of the filter idealy should match with the field name in your database table.

```bash
php artisan make:filter Status --model=Task
```
These command will create a file located at `app/Finders/Tasks/Filters/Status.php`. These file contains a class and a method called `apply` that receives an instance of the Eloquent query builder *(\Illuminate\Database\Eloquent\Builder)* and the value with which the search will be performed.

```php
namespace App\Finders\Tasks\Filters;

use Backfron\LaravelFinder\Contracts\FilterContract;
use Illuminate\Database\Eloquent\Builder;

class Status implements FilterContract
{
    /**
     * Apply a filter to the query builder instance.
     *
     * @param Builder $builder
     * @param mixed $status
     * @return Builder $builder
     */
    public static function apply(Builder $query, $status)
    {
        return $query->where('status', $status);
    }
}
```

Of course, you can modify the query to fit your needs. For example:

```php
public static function apply(Builder $query, $status)
{
    return $query->where('status', 'LIKE', "{$status}%");
}
```

You can create as many Filters as you need. So for example imagine we created the filters: Status, UserOwnerId and FinishedAt. Then in your controller you can receive a request that includes those fileds in snake_case version, and pass it to the `filters` static method of your Finder.

```php
/*
IMAGINE THIS INCOMING REQUEST
[
    status => 'completed',
    user_owner_id => 123,
    finished_at => '2021-10-01',
]
*/

use App\Finders\Tasks\TaskFinder;

$tasks = TaskFinder::filters([
    'status' => request('status'),
    'user_owner_id' => request('user_owner_id'),
    'finished_at' => request('finished_at'),
])->get();

```

Under the hood, LaravelFinder will take the field names and will call the appropriate Filter. For `status` field the `Status` filter will be called, for `user_owner_id` field the UserOwnerId filter will be called, for the `finished_at` field the `FinishedAt` field will be called an so on.

The static `filters` method will return an `Illuminate\Database\Eloquent\Builder` instance, that means you can continue chaining Eloquent methods after your filters are applied.

```php
use App\Finders\Tasks\TaskFinder;
use Illuminate\Support\Facades\Auth;

$tasks = TaskFinder::filters([
    'status' => request('status'),
    'user_owner_id' => request('user_owner_id'),
    'finished_at' => request('finished_at'),
])
->where('user_id', Auth::id())
->get();
```

What happen if a field don't have a match Filter? No worries, by default LaravelFinder will ignore any field that don't match with any of the available filters. You can change these behavior by publishing the config file laravel-finder.php like these:

```bash
php artisan vendor:publish --tag=laravel-finder.config
```
And once the config file is published at `config/laravel-finder.php`, you may set to `false` the `ignore-unexisting-filters` key. When set to `false` LaravelFinder will throw an `Backfron\LaravelFinder\Exceptions\FilterNotFoundException` exception if you include a filter that don't exists yet.

You even can create your Finder and your filters in one command call.

```bash
php artisan make:finder TaskFinder --model=Task --filter=Status --filter=UserOwnerId --filter=FinishedAt
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email jago86@gmail.com instead of using the issue tracker.

## Credits

- [Jairo Ushiña][link-author]
- [All Contributors][link-contributors]

## License

Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/backfron/laravel-finder.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/backfron/laravel-finder.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/backfron/laravel-finder/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/backfron/laravel-finder
[link-downloads]: https://packagist.org/packages/backfron/laravel-finder
[link-travis]: https://travis-ci.org/backfron/laravel-finder
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://backfron.com
[link-contributors]: ../../contributors
