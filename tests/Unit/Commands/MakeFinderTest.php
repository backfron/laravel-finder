<?php

namespace Backfron\LaravelFinder\Tests\Unit\Commands;

use Illuminate\Support\Facades\File;
use Backfron\LaravelFinder\Tests\LaravelFinderTestCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MakeFinderTest extends LaravelFinderTestCase
{
    /** @test */
    public function can_create_a_finder_specifing_model()
    {
        // destination path of the PostFinder class
        $postFinder = app_path('Finders/Posts/PostFinder.php');
        $postModel = app_path('Models/Post.php');

        // make sure we're starting from a clean state
        if (File::exists($postFinder)) {
            unlink($postFinder);
        }
        if (!File::exists($postModel)) {
            $this->artisan('make:model Post');
        }

        $this->assertFalse(File::exists($postFinder));
        $this->assertTrue(File::exists($postModel));

        $this->artisan('make:finder PostFinder --model=Post');

        $this->assertTrue(File::exists($postFinder));
        // Assert the file contains the right contents
        $expectedContents = <<<CLASS
<?php

namespace App\Finders\Posts;

use App\Models\Post;
use Backfron\LaravelFinder\LaravelFinder;

class PostFinder extends LaravelFinder
{
    protected static \$model = Post::class;

}

CLASS;

        $this->assertEquals($expectedContents, file_get_contents($postFinder));
    }

    /** @test */
    public function can_create_a_finder_without_specifing_model()
    {
        // destination path of the ClientFinder class
        $clientFinder = app_path('Finders/Clients/ClientFinder.php');
        $clientModel = app_path('Models/Client.php');

        // make sure we're starting from a clean state
        if (File::exists($clientFinder)) {
            unlink($clientFinder);
        }
        if (!File::exists($clientModel)) {
            $this->artisan('make:model Client');
        }

        $this->assertFalse(File::exists($clientFinder));
        $this->assertTrue(File::exists($clientModel));

        $this->artisan('make:finder ClientFinder');

        $this->assertTrue(File::exists($clientFinder));
        // Assert the file contains the right contents
        $expectedContents = <<<CLASS
<?php

namespace App\Finders\Clients;

use App\Models\Client;
use Backfron\LaravelFinder\LaravelFinder;

class ClientFinder extends LaravelFinder
{
    protected static \$model = Client::class;

}

CLASS;

        $this->assertEquals($expectedContents, file_get_contents($clientFinder));
    }

    /** @test */
    public function can_create_a_finder_for_an_unspecified_and_unexisting_model()
    {
        // destination path of the CompanyFinder class
        $companyFinder = app_path('Finders/Companies/CompanyFinder.php');
        $clientModel = app_path('Models/Company.php');

        // make sure we're starting from a clean state
        if (File::exists($companyFinder)) {
            unlink($companyFinder);
        }

        $this->assertFalse(File::exists($companyFinder));
        $this->assertFalse(File::exists($clientModel));

        $this->artisan('make:finder CompanyFinder');

        $this->assertTrue(File::exists($companyFinder));
        // Assert the file contains the right contents
        $expectedContents = <<<CLASS
<?php

namespace App\Finders\Companies;

use App\Models\Model;
use Backfron\LaravelFinder\LaravelFinder;

class CompanyFinder extends LaravelFinder
{
    protected static \$model = Model::class;

}

CLASS;

        $this->assertEquals($expectedContents, file_get_contents($companyFinder));
    }

    /** @test */
    public function stop_command_if_specified_model_do_not_exists()
    {
        // destination path of the CompanyFinder class
        $companyFinder = app_path('Finders/Companies/CompanyFinder.php');
        $clientModel = app_path('Models/Company.php');

        // make sure we're starting from a clean state
        if (File::exists($companyFinder)) {
            unlink($companyFinder);
        }

        $this->assertFalse(File::exists($companyFinder));
        $this->assertFalse(File::exists($clientModel));

        $response = $this->artisan('make:finder CompanyFinder --model=Company');

        $response->expectsOutput("The specified model 'Company' was not found.");
        $this->assertFalse(File::exists($companyFinder));
    }

    /** @test */
    public function can_create_a_finder_specifing_filters()
    {
        // destination path of the PostFinder class
        $postFinder = app_path('Finders/Posts/PostFinder.php');
        $postModel = app_path('Models/Post.php');
        $titleFilter = app_path('Finders/Posts/Filters/Title.php');
        $statusFilter = app_path('Finders/Posts/Filters/Status.php');

        // make sure we're starting from a clean state
        if (File::exists($postFinder)) {
            unlink($postFinder);
        }
        if (!File::exists($postModel)) {
            $this->artisan('make:model Post');
        }

        $this->assertFalse(File::exists($postFinder));
        $this->assertTrue(File::exists($postModel));
// $this->withoutMockingConsoleOutput();
        $this->artisan('make:finder PostFinder --filter=Title --filter=Status');

        $this->assertTrue(File::exists($postFinder));
        $this->assertTrue(File::exists($titleFilter));
        $this->assertTrue(File::exists($statusFilter));

    }

    /** @test */
    public function can_create_a_finder_specifing_filters_and_model()
    {
        // destination path of the PostFinder class
        $postFinder = app_path('Finders/Posts/PostFinder.php');
        $postModel = app_path('Models/Post.php');
        $titleFilter = app_path('Finders/Posts/Filters/Title.php');
        $statusFilter = app_path('Finders/Posts/Filters/Status.php');

        // make sure we're starting from a clean state
        if (File::exists($postFinder)) {
            unlink($postFinder);
        }
        if (!File::exists($postModel)) {
            $this->artisan('make:model Post');
        }

        $this->assertFalse(File::exists($postFinder));
        $this->assertTrue(File::exists($postModel));
        // $this->withoutMockingConsoleOutput();
        $this->artisan('make:finder PostFinder --model=Post --filter=Title --filter=Status');

        $this->assertTrue(File::exists($postFinder));
        $this->assertTrue(File::exists($titleFilter));
        $this->assertTrue(File::exists($statusFilter));
    }
}
