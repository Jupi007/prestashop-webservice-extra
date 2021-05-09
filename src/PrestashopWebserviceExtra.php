<?php

declare(strict_types=1);

namespace Jupi007\PrestashopWebserviceExtra;

use Jupi007\PrestashopWebserviceExtra\PrestashopWebservice;
use PrestaShopWebserviceException;

class PrestashopWebserviceExtra
{
    protected PrestashopWebservice $webservice;

    protected ?string $queryAction = null;
    protected ?array $queryOptions = null;

    public function __construct(PrestashopWebservice $webservice)
    {
        $this->webservice = $webservice;
    }

    protected function setAction(string $action): void
    {
        if ($this->queryAction !== null) {
            throw new PrestaShopWebserviceException('You\'re trying to overwrite the webservice query action. Only one query action should be used.');
        }

        $this->queryAction = $action;
    }

    protected function addOption(string $name, $option): void
    {
        if (isset($this->queryOptions[$name])) {
            throw new PrestaShopWebserviceException('You\'re trying to overwrite a webservice query option. Each query option should be defined only once.');
        }

        $this->queryOptions[$name] = $option;
    }

    protected function setResource(string $resource): void
    {
        $this->addOption('resource', $resource);
    }

    public function initQuery(): self
    {
        $this->queryOptions = [];
        
        return $this;
    }

    public function get(string $resource): self
    {
        $this->setAction('get');
        $this->setResource($resource);

        return $this;
    }

    public function getBlankSchema(string $resource): self
    {
        $this->setAction('get');
        $this->addOption(
            'url',
            $this->webservice->getUrl() . '/api/' . $resource . '?schema=blank'
        );

        return $this;
    }

    public function add(string $resource): self
    {
        $this->setAction('add');
        $this->setResource($resource);

        return $this;
    }

    public function edit(string $resource): self
    {
        $this->setAction('edit');
        $this->setResource($resource);

        return $this;
    }

    public function delete(string $resource): self
    {
        $this->setAction('delete');
        $this->setResource($resource);

        return $this;
    }
    
    public function id(int $id): self
    {
        $this->addOption('id', $id);

        return $this;
    }

    public function addValueFilter(string $field, string $value): self
    {
        $this->addOption(
            'filter[' . $field . ']',
            '[' . $value . ']'
        );

        return $this;
    }

    public function addValuesFilter(string $field, array $values): self
    {
        if (count($values) === 0) return $this;

        $this->addOption(
            'filter[' . $field . ']',
            '[' . implode("|", $values) . ']'
        );

        return $this;
    }

    public function addIntervalFilter(string $field, int $min, int $max): self
    {
        $this->addOption(
            'filter[' . $field . ']',
            '[' . $min . ',' . $max . ']'
        );

        return $this;
    }

    public function addBeginsByFilter(string $field, string $value): self
    {
        $this->addOption(
            'filter[' . $field . ']',
            '[' . $value . ']%'
        );

        return $this;
    }

    public function addEndsByFilter(string $field, string $value): self
    {
        $this->addOption(
            'filter[' . $field . ']',
            '%[' . $value . ']'
        );

        return $this;
    }

    public function addContainsFilter(string $field, string $value): self
    {
        $this->addOption(
            'filter[' . $field . ']',
            '%[' . $value . ']%'
        );

        return $this;
    }

    public function display(array $display): self
    {
        if (count($display) === 0) return $this;
        
        $this->addOption(
            'display',
            '[' . implode(",", $display) . ']'
        );

        return $this;
    }

    public function displayFull(): self
    {
        $this->addOption(
            'display',
            'full'
        );

        return $this;
    }

    public function sort(array $sortArray): self
    {
        if (count($sortArray) === 0) return $this;

        $sort = [];

        foreach ($sortArray as $field => $order) {
            $sort[] = $field . '_' . $order;
        }

        $this->addOption(
            'sort',
            '[' . implode(",", $sort) . ']'
        );

        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->addOption(
            'limit',
            $offset > 0 ? $offset . ',' . $limit : $limit
        );

        return $this;
    }

    public function idShop(string $idShop): self
    {
        $this->addOption(
            'id_shop',
            $idShop
        );

        return $this;
    }

    public function idGroupShop(string $idGroupShop): self
    {
        $this->addOption(
            'id_group_shop',
            $idGroupShop
        );

        return $this;
    }

    public function schema(string $schema): self
    {
        $this->addOption(
            'schema',
            $schema
        );

        return $this;
    }

    public function language(string $language): self
    {
        $this->addOption(
            'language',
            $language
        );

        return $this;
    }

    public function sendXml($xml): self
    {
        $this->addOption(
            $this->queryAction === 'add' ? 'postXml' : 'putXml',
            $xml
        );

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
