<?php

namespace Backfron\LaravelFinder\Tests\Unit\Commands;

use Illuminate\Support\Facades\File;
use Backfron\LaravelFinder\Tests\LaravelFinderTestCase;

class MakeFilterTest extends LaravelFinderTestCase
{
    /** @test */
    public function can_create_a_filter()
    {
        $postModel = app_path('Models/Post.php');
        $postFinder = app_path('Finders/Posts/PostFinder.php');
        // destination path of the Name filter class
        $nameFilter = app_path('Finders/Posts/Filters/Name.php');

        // make sure we're starting from a clean state
        if (File::exists($nameFilter)) {
            unlink($nameFilter);
        }
        if (!File::exists($postModel)) {
            $this->artisan('make:model Post');
        }
        if (!File::exists($postFinder)) {
            $this->artisan('make:finder PostFinder --model=Post');
        }

        $this->assertFalse(File::exists($nameFilter));
        $this->assertTrue(File::exists($postModel));
        $this->assertTrue(File::exists($postFinder));

        $this->artisan('make:filter Name --model=Post');

        $this->assertTrue(File::exists($nameFilter));
        // Assert the file contains the right contents
        $expectedContents = <<<CLASS
<?php

namespace App\Finders\Posts\Filters;

use Backfron\LaravelFinder\Contracts\FilterContract;
use Illuminate\Database\Eloquent\Builder;

class Name implements FilterContract
{
    /**
     * Apply a filter to the query builder instance.
     *
     * @param Builder \$builder
     * @param mixed \$name
     * @return Builder \$builder
     */
    public static function apply(Builder \$query, \$name)
    {
        return \$query->where('name', \$name);
    }
}

CLASS;

        $this->assertEquals($expectedContents, file_get_contents($nameFilter));
    }

    /** @test */
    public function can_create_a_filter_with_composed_name()
    {
        $postModel = app_path('Models/Post.php');
        $postFinder = app_path('Finders/Posts/PostFinder.php');
        // destination path of the Name filter class
        $nameFilter = app_path('Finders/Posts/Filters/LastName.php');

        // make sure we're starting from a clean state
        if (File::exists($nameFilter)) {
            unlink($nameFilter);
        }
        if (!File::exists($postModel)) {
            $this->artisan('make:model Post');
        }
        if (!File::exists($postFinder)) {
            $this->artisan('make:finder PostFinder --model=Post');
        }

        $this->assertFalse(File::exists($nameFilter));
        $this->assertTrue(File::exists($postModel));
        $this->assertTrue(File::exists($postFinder));

        $this->artisan('make:filter LastName --model=Post');

        $this->assertTrue(File::exists($nameFilter));
        // Assert the file contains the right contents
        $expectedContents = <<<CLASS
<?php

namespace App\Finders\Posts\Filters;

use Backfron\LaravelFinder\Contracts\FilterContract;
use Illuminate\Database\Eloquent\Builder;

class LastName implements FilterContract
{
    /**
     * Apply a filter to the query builder instance.
     *
     * @param Builder \$builder
     * @param mixed \$lastName
     * @return Builder \$builder
     */
    public static function apply(Builder \$query, \$lastName)
    {
        return \$query->where('last_name', \$lastName);
    }
}

CLASS;

        $this->assertEquals($expectedContents, file_get_contents($nameFilter));
    }

    /** @test */
    public function if_model_is_not_specified_stop_command()
    {
        $postModel = app_path('Models/Post.php');
        $postFinder = app_path('Finders/Posts/PostFinder.php');
        // destination path of the Name filter class
        $nameFilter = app_path('Finders/Posts/Filters/Name.php');

        // make sure we're starting from a clean state
        if (File::exists($nameFilter)) {
            unlink($nameFilter);
        }
        if (!File::exists($postModel)) {
            $this->artisan('make:model Post');
        }
        if (!File::exists($postFinder)) {
            $this->artisan('make:finder PostFinder --model=Post');
        }

        $this->assertFalse(File::exists($nameFilter));
        $this->assertTrue(File::exists($postModel));
        $this->assertTrue(File::exists($postFinder));

        $response = $this->artisan('make:filter Name');

        $response->expectsOutput('You should specify the --model option.');
        $this->assertFalse(File::exists($nameFilter));
    }

    /** @test */
    public function if_existing_model_is_specified_but_finder_folder_do_not_exists_stop_command()
    {
        $postModel = app_path('Models/Post.php');
        $postFinder = app_path('Finders/Posts/PostFinder.php');
        $postFinderDirectory = app_path('Finders/Posts');
        // destination path of the Name filter class
        $nameFilter = app_path('Finders/Posts/Filters/Name.php');

        // make sure we're starting from a clean state
        if (File::exists($nameFilter)) {
            unlink($nameFilter);
        }
        if (!File::exists($postModel)) {
            $this->artisan('make:model Post');
        }

        if (File::isDirectory($postFinderDirectory)) {
            File::deleteDirectory($postFinderDirectory);
        }

        $this->assertFalse(File::exists($nameFilter));
        $this->assertTrue(File::exists($postModel));
        $this->assertFalse(File::exists($postFinder));
        $this->assertFalse(File::isDirectory($postFinderDirectory));

        $response = $this->artisan('make:filter Name --model=Post');

        $response->expectsOutput("Posts finder not found. Please create a finder first runnig 'php artisan make:finder PostsFinder --model=Post'.");
        $this->assertFalse(File::exists($nameFilter));
    }
}
