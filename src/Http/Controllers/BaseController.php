<?php
namespace Reinforcement\Http\Controllers;
use Illuminate\Routing\Controller AS IlluminateController;

abstract class BaseController extends IlluminateController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}