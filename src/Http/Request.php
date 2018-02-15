<?php
namespace Reinforcement\Http;

use Illuminate\Http\Request as IlluminateRequest;
use Reinforcement\Exceptions\BadRequestException;

class Request extends IlluminateRequest
{
    protected $allowedFilters = [];
    protected $allowedIncludes = [];
    protected $allowedSorts = [];
    protected $allowedPageParams = ['size', 'number'];
	protected $allowedColumnSet = ['*'];

	function initializeFromRequest(IlluminateRequest $request )
    {
        $objValues = get_object_vars($request);
        foreach($objValues as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public function getParameters()
    {
        return [
            'filters' => $this->getFilteringParameters(),
            'includes' => $this->getIncludeParameters(),
            'sorts' => $this->getSortParameters(),
            'paging' => $this->getPagingParameters(),
            'columns' => $this->getColumnSet(),
        ];
    }

    public function getFilteringParameters(array $allowedFilters = [])
    {
        if (empty($this->all()['filter'])) return [];

        $allowedFilters = !empty($allowedFilters)? $allowedFilters : $this->allowedFilters;
        $filters = $this->all()['filter'];
        $message = 'Request contains filters that are not allowed';
        return $this->checkAllowedParams($allowedFilters, $filters, $message);
    }

    public function getIncludeParameters(array $allowedIncludes = [])
    {
        if (empty($this->all()['include'])) return [];

        $allowedIncludes = !empty($allowedIncludes)? $allowedIncludes : $this->allowedIncludes;
        $includes = explode(',', $this->all()['include']);
        $message = 'Request contains includes that are not allowed';
        return $this->checkAllowedParams($allowedIncludes, $includes, $message);
    }

    public function getSortParameters(array $allowedSorts = [])
    {
    	if (empty($this->all()['sort'])) return [];

        $allowedSorts = !empty($allowedSorts)? $allowedSorts : $this->allowedSorts;
        $sorts =  explode(',', $this->all()['sort']);

        $sortsToCheck =  [];
        $sortsFormatted =  [];
        foreach ($sorts as $value) {
            if (starts_with($value, '-')) {
                $value = ltrim($value, '-');
                $sortsFormatted[] = ['field' => $value, 'direction' => 'desc'];
                $sortsToCheck[] = $value;

            } else {
                $sortsFormatted[] = ['field' => $value, 'direction' => 'asc'];
                $sortsToCheck[] = $value;
            }
        }

        $message = 'Request contains sorts that are not allowed';
        $this->checkAllowedParams($allowedSorts, $sortsToCheck, $message);
        return $sortsFormatted;
    }

    public function getPagingParameters(array $allowedPageParams = [])
    {
        if (empty($this->all()['page'])) return [];

        $allowedPageParams = !empty($allowedPageParams)? $allowedPageParams : $this->allowedPageParams;

        $pageParams = $this->all()['page'];
        $message = 'Request contains page params that are not allowed';
        return $this->checkAllowedParams($allowedPageParams, $pageParams, $message);
    }

    public function getColumnSet(array $allowedColumns = [])
    {
        if (empty($this->all()['columns'])) return [];

        $allowedColumns = !empty($allowedColumns)? $allowedColumns : $this->allowedColumnSet;
        $columns =  explode(',', $this->all()['columns']);
        $message = 'Request contains columns that are not allowed';
        return $this->checkAllowedParams($allowedColumns, $columns, $message);
    }


    protected function checkAllowedParams($allowedParams, $requestParams, $errorMessage)
    {
        $isAssoc = is_array_assoc($requestParams);
        if ($isAssoc) {
            $notallowed = array_diff_key($requestParams, array_flip($allowedParams));

        } else {
            $notallowed = array_diff($requestParams, $allowedParams);
        }

        if (empty($notallowed)) {
            return $requestParams;
        }

        $notallowedResponse = [];

        foreach ($notallowed as $key => $value) {
            if ($isAssoc) {
                $notallowedResponse[] = [
                    'param' => $key,
                    'value' => $value
                ];
            } else {
                $notallowedResponse[] = [
                    'param' => $value
                ];
            }
        };

        throw new BadRequestException($errorMessage, $notallowedResponse);
    }
}