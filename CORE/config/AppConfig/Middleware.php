<?php

namespace AppConfig;

use MVCME\Config\Middleware as ConfigMiddleware;
use App\Middleware\Auth\Bearer;
use App\Middleware\Auth\CookieToken;
use App\Middleware\Auth\Basic;
use App\Middleware\Auth\Basic_Active;
use App\Middleware\Auth\Bearer_Active;
use App\Middleware\Auth\Bearer_Member_Active;

class Middleware extends ConfigMiddleware
{

    /**
     * Configures aliases for middleware classes to make reading things nicer and simpler.
     *
     * @var array
     * 
     * How to use:
     * - [middleware_name => classname]
     * - [middleware_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'Auth.Basic' => Basic::class,
        'Auth.Basic.Active' => Basic_Active::class,
        'Auth.Bearer' => Bearer::class,
        'Auth.Bearer.Active' => Bearer_Active::class,
        'Auth.Bearer.Member.Active' => Bearer_Member_Active::class,
        'Auth.CookieToken' => CookieToken::class,
    ];

    /**
     * List of middleware aliases that are always applied before and after every request
     */
    public array $globals = [
        'before' => [
            // 'apikey'
            // 'csrf',
        ],
        'after' => [
            // 'secureheaders',
        ],
    ];

    /**
     * List of middleware aliases that should run on any before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     */
    public array $middleware = [];
}
