<?php
namespace Reinforcement\Http\Controllers;

use Reinforcement\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Reinforcement\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller AS IlluminateController;

abstract class BaseController extends IlluminateController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	protected $repository;
	protected $usedInstances = [];

	public function __construct(Container $container)
    {
        $this->app = $container;
        $this->request = $container->make($this->requestClass);
    }

    public function getValidator()
    {
        return $this->app->make($this->validatorClass);
    }

    public function validateRequest($removedReqired = false)
    {
        return $this->validate($this->validatorClass, $removedReqired);
    }

    protected function getRepository(string $class = null)
    {
    	if ($class) {
    		if (empty($this->usedInstances[$class])) {
	    		$this->usedInstances[$class] = $this->app->make($class);
			}
		    return $this->usedInstances[$class];
    	}

    	if (empty($this->repository)) {
    		$this->repository = $this->app->make($this->repositoryClass);
    	}

		return $this->repository;
    }
}