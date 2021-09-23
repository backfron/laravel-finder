<?php

namespace Backfron\LaravelFinder\Models;

use Illuminate\Database\Eloquent\Model;


class FooPost extends Model
{
    protected $table = 'posts';

    public function user()
    {
        return $this->belongsTo(FooUser::class);
    }
}
