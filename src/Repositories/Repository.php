<?php

namespace Reinforcement\Repositories;

use App\JsonApi\Errors\ErrorCollection;
use App\Repositories\RepositoryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Query\QueryParametersParserInterface as QP;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\I18n\Translator as T;
use Reinforcement\Database\Eloquent\Model;
use Reinforcement\Exceptions\BadRequestException;
use Reinforcement\Repositories\FilteringTrait;
use Reinforcement\Repositories\RepositoryInterface;

/**
* Base repository
*/
abstract class Repository
{
    use FilteringTrait;

    const PARAM_PAGING_SIZE = 'size';
    const PARAM_PAGING_NUMBER = 'number';
    const DEFAULT_PAGE_SIZE = 15;
    const MAX_PAGE_SIZE = 30;

    protected $model;
    protected $query;
    protected $connection;
    protected $container;
    protected $parameters;
    protected $resourceId = null;
    protected $relation = null;

    // public function __construct(Model $model)
    // {
    //     $this->model = $model;
    // }

    public function getCollection(EncodingParametersInterface $parameters, callable $callback = null)
    {
        $query = $this->with($parameters);
        if ($callback) {
            $query = $callback($query);
        }

        return $query->get($this->getFieldSets($parameters));
    }

    protected function with($parameters = null, Builder $builder = null)
    {
        $with = !empty($parameters)  ? $parameters['includes'] : [];
        if (is_null($this->relation) ) {
            return  (is_null($builder)) ? $this->getModel()->with($with) : $builder->with($with);
        } else {
            if (is_null($builder)) {
                return $this->getModel()->{$this->relation}()->with($with);
            } else {
                return $builder->with($with);
            }
        }
    }

    public function load(EncodingParametersInterface $parameters = null, Model $model)
    {
        $with = (!is_null($parameters) && $parameters->getIncludePaths()) ? $parameters->getIncludePaths() : array();
        $model->load($with);
        return $model;
    }

    protected function getFilteringColumns(array $filtering = array())
    {
        //get the available filtering columns to be filtered
        //filtering columns for parent will get getFiltering
        //for the relationship filtering it will be get{relation}Filtring
        //

        if ($this->relation) {
            $method = 'get' . ucfirst($this->relation) . 'Filtering';
            $availableFilterings = $this->$method();
        } else {
            $availableFilterings =  is_array($this->getFiltering()) ? $this->getFiltering() : [];
        }

        // $array = array_diff(array_keys($filtering), array_keys($availableFilterings));
        // if (!empty($array)) {
        //     throw new RepositoryException('\'' . implode(',', $array) . '\' is not available for the filtering.');

        // }

        return $availableFilterings;

    }

    protected function getSorting($parameters)
    {
        return $parameters['sorts'];
    }

    protected function buildSorting($query, array $sorting = array(), $relation = null)
    {
        if (empty($sorting)) {
            return $query;
        }
        $model = $this->relation ? $this->model->{$this->relation}()->getModel() : $this->model;
        // $mappings = call_user_func_array([$model, 'mappings'], []);
        foreach ($sorting as $sort) {
            $field = $sort['field'];
            // if (is_array($mappings) && $dbField = array_search($field, $mappings)) {
            //     $field = $dbField;
            // }

            $query = $query->orderBy($field, $sort['direction']);
        }

        return $query;
    }

    public function getPaginatedCollection($parameters, callable $callback = null)
    {
        $query = $this->with($parameters);
        if ($callback) {
            $query = $callback($query);
        }

        $filtering = $parameters['filters'];
        if (!empty($filtering)) {
            $filteringMap = $this->getFilteringColumns($filtering);
            $query = $this->buildFilteredQuery($query, $filtering, $filteringMap);
        }


        $query = $this->buildSorting($query, $this->getSorting($parameters));
        return $this->paginateBuilder($query, $parameters);
    }



    /**
     * @param Builder                     $builder
     * @param EncodingParametersInterface $parameters
     *
     * @return PagedDataInterface
     */
    protected function paginateBuilder($builder, $parameters)
    {
        //paging
        $builder = $builder->latest();
        return $builder->paginate($this->getPageSize($parameters), $this->getFieldSets($parameters), 'page[number]', $this->getPageNumber($parameters));
    }

    public function getItem($id, EncodingParametersInterface $parameters = null, callable $callback = null)
    {
        $query = $this->with($parameters);

        if (! is_null($callback)) {
            $query = $callback($query);
        }

        return $query->findOrFail($id);
    }

    public function create(array $data = array())
    {
        $relation = $this->relation;

        if (!$relation) {
            return $this->getModel()->create($data);
        }
        $relation = $this->getModel()->{$relation}();

        if (method_exists($relation, 'withPivot')) {
            //get the key
            $otherkey = explode('.', $relation->getQualifiedRelatedKeyName());
            $key = array_pop($otherkey);
            if (!array_key_exists($key, $data)) {
                throw new \Exception("The otherkey value does not match the pivot relation otherkey. Please check the keys at validator.");
            }
            $keyId = array_pull($data, $key);
            $relation->attach($keyId, $data);
            $model = $relation->where($key, $keyId)->first();
        } else {
            $relationModel = $relation->getRelated()->fill($data);
            if (method_exists($relation, 'save')) {
                $model = $relation->save($relationModel);
            } else {
                $relationModel->save();
                $relation->associate($relationModel->id);
                $relation->save();
                $model = $relation->get();
            }
        }

        return $model;
    }

    public function update($id, array $data = array(), callable $callback = null)
    {
        $relation = $this->relation;
        if (!$relation) {
            $model = $this->getItem($id);
            $model->fill($data);
            if ($model->isDirty()) {
                $model->save();
            }

            return $model;
        }

        $relation = $this->model->{$relation}();
        $model = $relation->find($id);
        if (method_exists($relation, 'withPivot')) {
            $model->pivot->fill($data);
            if ($model->pivot->isDirty()) {
                $model->pivot->save();
            }

        } else {
            $model->fill($data);
            if ($model->isDirty()) {
                $model->save();
            }
        }

        return $model;
    }

    public function delete($id, callable $callback = null)
    {
        $relation = $this->relation;
        if (!$relation) {
            $model = $this->getItem($id);
            return $model->delete();
        }

        $model = $this->model->{$relation}();
        if (method_exists($model, 'withPivot')) {
            return $model->detach($id);
        } else {
            return $model->findOrFail($id)->delete();
        }
    }

    public function getModel()
    {
        if (!empty($this->model)) return $this->model;
        return app()->make($this->modelClass);
    }

    /**
     *
     * @param  array  $parameters
     * @return string
     */
    protected function getPageSize(array $parameters)
    {
        $paging = $parameters['paging'];
        $size = isset($paging[self::PARAM_PAGING_SIZE]) ? $paging[self::PARAM_PAGING_SIZE] : self::DEFAULT_PAGE_SIZE;
        if($size > static::MAX_PAGE_SIZE) {
            throw new BadRequestException(
            'Page size of more than ' . static::MAX_PAGE_SIZE . ' records is not allowed', $paging);
        }
        return $size;
    }

    protected function getErrorCollection()
    {
        return new ErrorCollection();
    }

    /**
     * @param EncodingParametersInterface|null $parameters
     *
     * @return int|null
     */
    protected function getPageNumber($parameters)
    {
        $paging = $parameters['paging'];
        return isset($paging[self::PARAM_PAGING_NUMBER]) ? $paging[self::PARAM_PAGING_NUMBER] : null;
    }

    protected function getFieldSets(array $parameters)
    {
        return !empty($parameters['columns']) ? $parameters['columns']: ['*'];
    }

    protected function throwException(ErrorCollection $errors, $status = Response::HTTP_BAD_REQUEST)
    {
        throw new JsonApiException($errors, $status);
    }

    public function setRelation($relation)
    {
        $this->relation = $relation;
        return $this;
    }

    public function setResource($resourceId)
    {
        $this->resourceId = $resourceId;
        $this->model = $this->model->newQuery()->findOrFail($resourceId);
        return $this;
    }

    public function findBy(array $conditions, array $with = array())
    {
        return $this->model->with($with)->where($conditions)->firstOrFail();
    }

    protected function setConnection()
    {
        $this->connection = $this->getModel()->getConnection();
    }

    public function getConnection()
    {
        if (!$this->connection) {
            $this->setConnection();
        }

        return $this->connection;
    }

    public function beginTransaction()
    {
        $this->connection = $this->getConnection();
        $this->connection->beginTransaction();
    }

    public function commit()
    {
        $this->connection->commit();
    }

    public function rollback()
    {
        $this->connection->rollback();
    }
}
