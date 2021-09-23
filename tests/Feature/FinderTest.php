<?php

namespace Backfron\LaravelFinder\Feature\Tests;

use Illuminate\Support\Facades\Config;
use Backfron\LaravelFinder\Finders\Posts\FooPostsFinder;
use Backfron\LaravelFinder\Tests\LaravelFinderTestCase;

class FinderTest extends LaravelFinderTestCase
{

    /** @test */
    public function test_can_apply_filter()
    {
        $posts = FooPostsFinder::filters('foo_title', 'Vue.js')
            ->get();

        $this->assertCount(3, $posts);
    }

    /** @test */
    public function test_can_apply_an_array_of_filters()
    {
        $posts = FooPostsFinder::filters([
            'foo_title' => 'Vue.js',
            'foo_user_role' => 'admin'
        ])->get();

        $this->assertCount(1, $posts);
        $this->assertEquals(6, $posts[0]->id);
        $this->assertEquals(1, $posts[0]->user_id);
    }

    /** @test */
    public function dont_throw_an_exception_if_filter_dont_exists()
    {
        Config::set('laravel-finder.ignore-unexisting-filters', true);
        $posts = FooPostsFinder::filters(['unexisting_filter', 'Vue'])
            ->get();

        $this->assertCount(6, $posts);
    }

    /** @test */
    public function throw_an_exception_if_filter_dont_exists()
    {
        Config::set('laravel-finder.ignore-unexisting-filters', false);
        try {
            FooPostsFinder::filters(['unexisting_filter', 'Vue'])
                ->get();
        } catch (\Throwable $th) {
            if ($th::class == 'Backfron\LaravelFinder\Exceptions\FilterNotFoundException') {
                $this->assertTrue(true);
                return;
            }
        }
        $this->fail("FilterNotFoundException expected when a filter don't exists and config.laravel-finder.ignore-unexisting-filters is set to true.");
    }
}
