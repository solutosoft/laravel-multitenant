<?php

namespace Solutosoft\MultiTenant\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Solutosoft\MultiTenant\MultiTenant;

class Post extends Model
{
    use MultiTenant;

    protected static $disableTenantScope = true;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'content'
    ];

    public $timestamps = false;
}
