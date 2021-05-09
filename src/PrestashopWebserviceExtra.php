<?php

declare(strict_types=1);

namespace Jupi007\PrestashopWebserviceExtra;

use Jupi007\PrestashopWebserviceExtra\PrestashopWebservice;

class PrestashopWebserviceExtra
{
    protected PrestashopWebservice $webservice;

    protected ?string $queryAction = null;
    protected ?array $queryOptions = null;

    public function __construct(PrestashopWebservice $webservice)
    {
        $this->webservice = $webservice;
    }

    public function initQuery(): self
    {
        $this->queryOptions = [];
        
        return $this;
    }

    public function get(string $resource): self
    {
        $this->queryAction = 'get';
        $this->queryOptions['resource'] = $resource;

        return $this;
    }
    
    public function id(int $id): self
    {
        $this->queryOptions['id'] = $id;

        return $this;
    }

    public function addValueFilter(string $field, string $value): self
    {
        $this->queryOptions['filter[' . $field . ']'] = '[' . $value . ']';

        return $this;
    }

    public function addValuesFilter(string $field, array $values): self
    {
        $this->queryOptions['filter[' . $field . ']'] = '[' . implode("|", $values) . ']';

        return $this;
    }

    public function addIntervalFilter(string $field, int $min, int $max): self
    {
        $this->queryOptions['filter[' . $field . ']'] = '[' . $min . ',' . $max;

        return $this;
    }

    public function addBeginsByFilter(string $field, string $value): self
    {
        $this->queryOptions['filter[' . $field . ']'] = '[' . $value . ']%';

        return $this;
    }

    public function addEndsByFilter(string $field, string $value): self
    {
        $this->queryOptions['filter[' . $field . ']'] = '%[' . $value . ']';

        return $this;
    }

    public function addContainsFilter(string $field, string $value): self
    {
        $this->queryOptions['filter[' . $field . ']'] = '%[' . $value . ']%';

        return $this;
    }

    public function display(array $display): self
    {
        if (count($display) === 0) return $this;

        $this->queryOptions['display'] = '[' . implode(",", $display) . ']';

        return $this;
    }

    public function displayFull(): self
    {
        $this->queryOptions['display'] = 'full';

        return $this;
    }

    public function sort(array $sortArray): self
    {
        if (count($sortArray) === 0) return $this;

        $sort = [];

        foreach ($sortArray as $field => $order) {
            $sort[] = $field . '_' . $order;
        }

        $this->queryOptions['sort'] = '[' . implode(",", $sort) . ']';

        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->queryOptions['limit'] = $offset > 0 ? $offset . ',' . $limit : $limit;

        return $this;
    }

    public function idShop(string $idShop): self
    {
        $this->queryOptions['id_shop'] = $idShop;

        return $this;
    }

    public function idGroupShop(string $idGroupShop): self
    {
        $this->queryOptions['id_group_shop'] = $idGroupShop;

        return $this;
    }

    public function schema(string $schema): self
    {
        $this->queryOptions['schema'] = $schema;

        return $this;
    }

    public function language(string $language): self
    {
        $this->queryOptions['language'] = $language;

        return $this;
    }

    public function getBlankSchema(string $resource): self
    {
        $this->queryAction = 'get';
        $this->queryOptions['url'] = $this->webservice->getUrl() . '/api/' . $resource . '?schema=blank';

        return $this;
    }

    public function executeQuery()
    {
        $action = $this->queryAction;

        if (!$action) {
            return null;
        }

        $data = $this->webservice->$action($this->queryOptions);

        $this->queryAction = null;
        $this->queryOptions = null;

        return $data;
    }
}
