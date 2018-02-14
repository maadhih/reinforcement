<?php
namespace Reinforcement\Http;

use Illuminate\Http\Request as IlluminateRequest;
use Reinforcement\Exceptions\BadRequestException;

class Request extends IlluminateRequest
{
	protected $allowedFilters = [];

	function initializeFromRequest(IlluminateRequest $request )
    {
        $objValues = get_object_vars($request);
        foreach($objValues as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public function getFilteringParameters()
    {
        return $this->checkFilters();
    }

    public function getIncludeParameters()
    {
    	return $this->all()['include'];
    }

    public function getSortParameters()
    {
    	return $this->all()['sort'];
    }

    protected function checkFilters()
    {
    	$filters = $this->all()['filter'];
        $notallowed = array_diff_key($filters, array_flip($this->allowedFilters));
        if (empty($notallowed)) {
            return $filters;
        }

        $notallowedResponse = [];

        foreach ($notallowed as $key => $value) {
            $notallowedResponse[] = [
                'filter' => $key,
                'value' => $value
            ];
        };

        throw new BadRequestException('Request contains filters that are not allowed', $notallowedResponse);
    }
}