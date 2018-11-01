<?php
namespace Reinforcement\Acl\Tests\Http;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Orchestra\Testbench\Http\Kernel as OrchestraKernel;

class Kernel extends OrchestraKernel
{
    protected $routeMiddleware = [
        'auth'       => Authenticate::class
    ];
}
