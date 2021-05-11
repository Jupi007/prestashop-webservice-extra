<?php

declare(strict_types=1);

namespace Jupi007\PrestashopWebserviceExtra;

use Jupi007\PrestashopWebserviceExtra\PrestashopWebservice;
use Jupi007\PrestashopWebserviceExtra\PrestaShopWebserviceException;

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

    protected function setUrl(string $url): void
    {
        $this->addOption('url', $url);
    }

    protected function checkAllowedActions(array $allowedActions): void
    {
        if ($this->queryAction === null) {
            throw new PrestaShopWebserviceException('You\'re trying to add a query option before defining the query action. The query action must always be defined before any query option.');
        }

        if (!in_array($this->queryAction, $allowedActions)) {
            throw new PrestaShopWebserviceException('This query option can only be used with these actions: ' . implode(", ", $allowedActions) . '. Current one (' . $this->queryAction . ') is forbidden.');
        }
    }

    protected function initQuery(): void
    {
        $this->queryOptions = [];
    }

    public function get(string $resource): self
    {
        $this->initQuery();
        $this->setAction('get');
        $this->setResource($resource);

        return $this;
    }

    public function getUrl(string $url): self
    {
        $this->initQuery();
        $this->setAction('get');
        $this->setUrl($url);

        return $this;
    }

    public function getBlankSchema(string $resource): self
    {
        $this->initQuery();
        $this->setAction('get');
        $this->addOption(
            'url',
            $this->webservice->getUrl() . '/api/' . $resource . '?schema=blank'
        );

        return $this;
    }

    public function add(string $resource): self
    {
        $this->initQuery();
        $this->setAction('add');
        $this->setResource($resource);

        return $this;
    }

    public function addUrl(string $url): self
    {
        $this->initQuery();
        $this->setAction('add');
        $this->setUrl($url);

        return $this;
    }

    public function edit(string $resource): self
    {
        $this->initQuery();
        $this->setAction('edit');
        $this->setResource($resource);

        return $this;
    }

    public function editUrl(string $url): self
    {
        $this->initQuery();
        $this->setAction('edit');
        $this->setUrl($url);

        return $this;
    }

    public function delete(string $resource): self
    {
        $this->initQuery();
        $this->setAction('delete');
        $this->setResource($resource);

        return $this;
    }

    public function deleteUrl(string $url): self
    {
        $this->initQuery();
        $this->setAction('delete');
        $this->setUrl($url);

        return $this;
    }
    
    public function id(int $id): self
    {
        $this->checkAllowedActions(['get', 'edit', 'delete']);
        $this->addOption('id', $id);

        return $this;
    }

    public function addValueFilter(string $field, string $value): self
    {
        $this->checkAllowedActions(['get']);
        $this->addOption(
            'filter[' . $field . ']',
            '[' . $value . ']'
        );

        return $this;
    }

    public function addValuesFilter(string $field, array $values): self
    {
        $this->checkAllowedActions(['get']);

        if (count($values) === 0) throw new PrestaShopWebserviceException('Values array shouldn\'t be empty.');

        $this->addOption(
            'filter[' . $field . ']',
            '[' . implode("|", $values) . ']'
        );

        return $this;
    }

    public function addIntervalFilter(string $field, int $min, int $max): self
    {
        $this->checkAllowedActions(['get']);

        $this->addOption(
            'filter[' . $field . ']',
            '[' . $min . ',' . $max . ']'
        );

        return $this;
    }

    public function addBeginsByFilter(string $field, string $value): self
    {
        $this->checkAllowedActions(['get']);

        $this->addOption(
            'filter[' . $field . ']',
            '[' . $value . ']%'
        );

        return $this;
    }

    public function addEndsByFilter(string $field, string $value): self
    {
        $this->checkAllowedActions(['get']);

        $this->addOption(
            'filter[' . $field . ']',
            '%[' . $value . ']'
        );

        return $this;
    }

    public function addContainsFilter(string $field, string $value): self
    {
        $this->checkAllowedActions(['get']);
        
        $this->addOption(
            'filter[' . $field . ']',
            '%[' . $value . ']%'
        );

        return $this;
    }

    public function display(array $display): self
    {
        $this->checkAllowedActions(['get']);
        
        if (count($display) === 0) throw new PrestaShopWebserviceException('Display values array shouldn\'t be empty.');
        
        $this->addOption(
            'display',
            '[' . implode(",", $display) . ']'
        );

        return $this;
    }

    public function displayFull(): self
    {
        $this->checkAllowedActions(['get']);
        
        $this->addOption(
            'display',
            'full'
        );

        return $this;
    }

    public function sort(array $sortArray): self
    {
        $this->checkAllowedActions(['get']);
        
        if (count($sortArray) === 0) throw new PrestaShopWebserviceException('Sort values array shouldn\'t be empty.');

        $sort = [];
        $allowedOrders = ['ASC', 'DESC'];

        foreach ($sortArray as $field => $order) {
            if (!in_array($order, $allowedOrders)) throw new PrestaShopWebserviceException('Please provide a valide order value (ASC or DESC).');
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
        $this->checkAllowedActions(['get']);
        
        $this->addOption(
            'limit',
            $offset > 0 ? $offset . ',' . $limit : $limit
        );

        return $this;
    }

    public function idShop(int $idShop): self
    {
        $this->addOption(
            'id_shop',
            $idShop
        );

        return $this;
    }

    public function idGroupShop(int $idGroupShop): self
    {
        $this->addOption(
            'id_group_shop',
            $idGroupShop
        );

        return $this;
    }

    public function schema(string $schema): self
    {
        $this->checkAllowedActions(['get']);
        
        $this->addOption(
            'schema',
            $schema
        );

        return $this;
    }

    public function languageFilter(int $languageId): self
    {
        $this->checkAllowedActions(['get']);
        $this->addOption(
            'language',
            $languageId
        );

        return $this;
    }

    public function languagesFilter(array $languagesIds): self
    {
        $this->checkAllowedActions(['get']);

        if (count($languagesIds) === 0) throw new PrestaShopWebserviceException('Languages ids array shouldn\'t be empty.');

        $this->addOption(
            'language',
            '[' . implode("|", $languagesIds) . ']'
        );

        return $this;
    }

    public function languageIntervalFilter(int $minLanguageId, int $maxLanguageId): self
    {
        $this->checkAllowedActions(['get']);

        $this->addOption(
            'language',
            '[' . $minLanguageId . ',' . $maxLanguageId . ']'
        );

        return $this;
    }

    public function sendXml($xml): self
    {
        $this->checkAllowedActions(['add', 'edit']);
        
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

    public function getQueryAction(): string
    {
        return $this->queryAction;
    }

    public function getQueryOptions(): array
    {
        return $this->queryOptions;
    }
}
