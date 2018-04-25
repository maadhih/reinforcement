<?php

namespace Reinforcement\Repositories;

/**
* Base repository
*/
trait FilteringTrait
{
    public function buildFilteredQuery($query, $requestFilters, $filteringMap)
    {
        if (isset($filteringMap['query'])) {
            $query = $this->buildSelectForFullText($query, $requestFilters, $filteringMap);
        }
        foreach ($requestFilters as $key => $term) {
            $columns = array_get($filteringMap, $key);
            $columns = $this->getColumnNames($columns, $key);
            $searchTerm = trim($term);
            if($key === 'query') {
                $query = $this->filterByColumns($query, $columns, $searchTerm, 'LIKE');
            } else {
                $column = array_shift($columns);
                $query = $this->filterByColumn($query, $column, (strpos($searchTerm, '-') === 0 ? '!=':'='), $searchTerm, 'AND');
            }

        }

        return $query;
    }

    protected function getColumnNames($columns, $key)
    {
        if (empty($columns)) {
            if ($key === 'query') {
                throw new FilteringException("Provided filter term '{$key}' can not  be found in filtering parameters.");
            }
        return [$key];
        }

        return (is_array($columns) ? $columns : explode(' ', $columns));
    }

    protected function filterByColumns($query, array $columns, $term, $operation = '=')
    {
        $table = null;
        $term = strtolower($operation) === 'like' ? '%'.$term.'%' : $term;
        if(method_exists($query, 'getTable')) {
            $table = $query->getTable();
        }
        return $query->where(function($query) use ($term, $columns, $operation, $table) {
            foreach ($columns as $key => $column) {
              if (!is_array($column)) {
                  if (strpos($column, 'pivot.') === 0) {
                      $column = $table .'.'. substr($column, 6);
                  }
                  $query = $this->filterByColumn($query, $column, $operation, $term, 'OR', strpos($column, 'fulltext.') === 0);
              } else {
                  $query = $query->orWhereHas($key, function ($query) use ($column, $term, $key)
                  {
                  foreach ($column as $index => $relationField) {
                    if ($index == 0) {
                        $query->where($relationField, 'LIKE', $term);
                    } else {
                        $query->orWhere($relationField, 'LIKE', $term);
                    }
                  }
                  });
              }
            }
            return $query;
        });
    }


    protected function filterByColumn($query, $column, $operation, $term, $andOr = 'OR', $fulltext = false)
    {
        // Relation Search
        if (str_contains($column, '.')) {
            $columnSegments = explode('.', $column);
            $column = array_pop($columnSegments);
            $relation = implode('.', $columnSegments);


            return $query->whereHas($relation, function ($q) use ($column, $operation, $term, $andOr) {
                return $this->buildWhereClause($q, $column, $operation, $term, $andOr);
            });

        }

        if (isset($query->getModel()->casts) && isset($query->getModel()->casts[$column])) {
            $term = $this->castAttribute($query->getModel()->casts[$column], $term);
        }
        if ($fulltext) {
            $column = substr($column, 9);
            $query->orWhereRaw("MATCH($column) AGAINST(?)", array($term));
        }
        return $this->buildWhereClause($query, $column, $operation, $term, $andOr);
    }


    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        if (is_null($value)) {
            return $value;
        }
        $key = strtolower($key);
        switch ($key) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            default:
                return $value;
        }
    }

    protected function buildWhereClause($query, $column, $operation, $term, $andOr = 'OR')
    {
        $negate = false;
        if (strpos($term, '-') === 0) {
            $negate = true;
            $term = trim($term, '-');
        }
        if ($negate) {
            if ($term === '\0') { // this mean treat is as a null
                $query->whereNotNull($column);
            } elseif (str_contains($term, ',')) {
                $query->whereNotIn($column, explode(',', $term));
            } else {
                $query = $this->generateGenericWhere($query, $column, $operation, $term, $andOr);
            }
        } else {
            if ($term === '\0') { // this mean treat is as a null
                $query->whereNull($column);
            } elseif (str_contains($term, ',')) {
                $query->whereIn($column, explode(',', $term));
            } elseif (str_contains($term, '..')) {
               $range = explode('..', $term);
               if ($this->checkDates($range)) {
                   $query = $this->filterByDateRange($query, $column, $range[0], $range[1]);
               } else {
                    $query->whereBetween($column, $range);
               }
            }else {
                $query = $this->generateGenericWhere($query, $column, $operation, $term, $andOr);
            }
        }
        return $query;
    }


    protected function checkDates($dates, $format = 'Y-m-d') {
        foreach ((array) $dates as $date) {
            if(!empty($date) && !\DateTime::createFromFormat($format, $date))
                return false;
        }
        return true;
    }

    protected function filterByDateRange($query, $column, $start, $end) {
        if (!empty($start)) $query->whereDate($column, '>=', $start);
        if (!empty($end)) $query->whereDate($column, '<=', $end);
        return $query;
    }

    protected function generateGenericWhere($query, $column, $operation, $term, $andOr) {
        $dateTime = \DateTime::createFromFormat('Y-m-d', $term);
        $isDate = $dateTime && $dateTime->format('Y-m-d') === $term;
        if ($isDate) {
            return $query->whereDate($column, $operation, $term, $andOr);
        } else {
            return $this->buildSimpleWhere($query, $column, $operation, $term, $andOr);
        }
    }

    protected function buildSimpleWhere($query, $column, $operation, $term, $andOr = 'OR')
    {
        return $query->where($column, $operation, $term, $andOr);
    }

    protected function buildSelectForFullText($query, $filtering, $filteringColumns)
    {
        if (!isset($filtering['query'])) {
            return $query;
        }
        if(! is_array($filteringColumns['query'])) {
            $filteringColumns['query'] = explode(' ', $filteringColumns['query']);
        }
        $relevence = array_where($filteringColumns['query'], function($key, $value) {
            return strpos($value, 'fulltext.') === 0;
        });

        if (empty($relevence)) {
            return $query;
        }

        $searchTerm = trim($filtering['query']);
        $column = array_shift($relevence);
        $column = substr($column, 9);
        return $query->selectRaw('*, MATCH('. $column .') AGAINST("'. $searchTerm .'") AS relevance');
    }
}
