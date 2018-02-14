<?php
namespace Reinforcement\Http\Controllers;
use Illuminate\Container\Container;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller AS IlluminateController;
use Reinforcement\Http\Request;

abstract class BaseController extends IlluminateController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function __construct(Container $container)
    {
        $this->app = $container;
        $this->request = $container->make($this->requestClass);
    }
}