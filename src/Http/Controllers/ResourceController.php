<?php
namespace Reinforcement\Http\Controllers;
use Reinforcement\Http\Controllers\BaseController;

abstract class ResourceController extends BaseController
{
    protected $repositoryClass;
    protected $validatorClass;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = null )
    {
        return $this->getRepository($id)->getPaginatedCollection($this->request->getParameters());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store($id = null)
    {
        $model = $this->getRepository($id)->create($this->validateRequest());
        return response($model, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $relationId = null)
    {
        $resourceId = null;

        if ($relationId) {
            $resourceId = $id;
            $id = $relationId;
        }

        $model = $this->getRepository($resourceId)->getItem($id, $this->request->getParameters());
        return $model;
    }

    /**
     * Update the specified resource in database.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, $relationId = null)
    {
        $resourceId = null;

        if ($relationId) {
            $resourceId = $id;
            $id = $relationId;
        }

        $model = $this->getRepository($resourceId)->update($id, $this->validateRequest(true));
        return $model;
    }

    /**
     * Remove the specified resource from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $relationId = null)
    {
        $resourceId = null;

        if ($relationId) {
            $resourceId = $id;
            $id = $relationId;
        }

        $this->getRepository($resourceId)->delete($id);
        return response('', 204);
    }

}