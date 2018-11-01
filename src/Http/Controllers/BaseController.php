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
    protected $relation;
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

    protected function getRepository($resourceId = null, $relation = null)
    {
    	if (empty($this->repository)) {
    		$this->repository = $this->app->make($this->repositoryClass);
    	}

        $relation = $relation? : $this->relation;

        if (empty($relation) || empty($resourceId)) {
            return $this->repository;
        }

		return $this->repository->setRelation($relation, $resourceId);
    }
}