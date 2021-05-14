<?php

declare(strict_types=1);

namespace Jupi007\PrestaShopWebserviceExtra;

use Jupi007\PrestaShopWebserviceExtra\Libraries\PrestaShopWebservice;
use Jupi007\PrestaShopWebserviceExtra\Libraries\PrestaShopWebserviceException;

/**
 * 
*/
class PrestaShopWebserviceExtra
{
    protected string $url;

    protected PrestaShopWebservice $webservice;

    protected ?string $queryAction = null;
    protected ?array $queryOptions = null;

    /**
     * Initialize the webservice class
     *
     * @param string $url Root URL for the shop
     * @param string $key Authentication key
     * @param mixed $debug Debug mode Activated (true) or deactivated (false)
     *
     * @throws PrestaShopWebserviceException if curl is not loaded
     */
    public function __construct(string $url, string $key, bool $debug = false): void
    {
        $this->url = $url;
        $this->webservice = new PrestaShopWebservice($url, $key, $debug);
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

    /**
     * Use the "get" action for the current request
     *
     * @param string $resource Name of the resource to get
     * @return self
     */
    public function get(string $resource): self
    {
        $this->initQuery();
        $this->setAction('get');
        $this->setResource($resource);

        return $this;
    }

    /**
     * Use the "get" action with an URL
     *
     * @param string $url URL of the resource to get
     * @return self
     */
    public function getUrl(string $url): self
    {
        $this->initQuery();
        $this->setAction('get');
        $this->setUrl($url);

        return $this;
    }

    /**
     * Get the blank shema of a resource
     *
     * @param string $resource Name of the resource blank shema to get
     * @return self
     */
    public function getBlankSchema(string $resource): self
    {
        $this->initQuery();
        $this->setAction('get');
        $this->addOption(
            'url',
            $this->url . '/api/' . $resource . '?schema=blank'
        );

        return $this;
    }

    /**
     * Use the "add" action for the current request
     *
     * @param string $resource Name of the resource to add
     * @return self
     */
    public function add(string $resource): self
    {
        $this->initQuery();
        $this->setAction('add');
        $this->setResource($resource);

        return $this;
    }

    /**
     * Use the "add" action with an URL
     *
     * @param string $url URL of the resource to add
     * @return self
     */
    public function addUrl(string $url): self
    {
        $this->initQuery();
        $this->setAction('add');
        $this->setUrl($url);

        return $this;
    }

    /**
     * Use the "edit" action for the current request
     *
     * @param string $resource Name of the resource to edit
     * @return self
     */
    public function edit(string $resource): self
    {
        $this->initQuery();
        $this->setAction('edit');
        $this->setResource($resource);

        return $this;
    }

    /**
     * Use the "edit" action with an URL
     *
     * @param string $url URL of the resource to edit
     * @return self
     */
    public function editUrl(string $url): self
    {
        $this->initQuery();
        $this->setAction('edit');
        $this->setUrl($url);

        return $this;
    }

    /**
     * Use the "delete" action for the current request
     *
     * @param string $resource Name of the resource to delete
     * @return self
     */
    public function delete(string $resource): self
    {
        $this->initQuery();
        $this->setAction('delete');
        $this->setResource($resource);

        return $this;
    }

    /**
     * Use the "delete" action with an URL
     *
     * @param string $url URL of the resource to delete
     * @return self
     */
    public function deleteUrl(string $url): self
    {
        $this->initQuery();
        $this->setAction('delete');
        $this->setUrl($url);

        return $this;
    }
    
    /**
     * Add the "id" option to the query to target a specific resource
     * Only usable with "get", "edit" and "delete" actions
     *
     * @param integer $id ID of the resource to target
     * @return self
     */
    public function id(int $id): self
    {
        $this->checkAllowedActions(['get', 'edit', 'delete']);
        $this->addOption('id', $id);

        return $this;
    }

    /**
     * Add a new field filter with a single value
     * Only usable with "get" action
     * 
     * @param string $field Field to filter
     * @param string $value Value to include
     * @return self
     */
    public function addValueFilter(string $field, string $value): self
    {
        $this->checkAllowedActions(['get']);
        $this->addOption(
            'filter[' . $field . ']',
            '[' . $value . ']'
        );

        return $this;
    }

    /**
     * Add a new field filter with multiple values
     * Only usable with "get" action
     *
     * @param string $field Field to filter
     * @param array $values Values to include
     * @return self
     */
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

    /**
     * Add a new field filter based on an interval of values
     * Only usable with "get" action
     *
     * @param string $field Field to filter
     * @param integer $min Min value to include
     * @param integer $max Max value to include
     * @return self
     */
    public function addIntervalFilter(string $field, int $min, int $max): self
    {
        $this->checkAllowedActions(['get']);

        $this->addOption(
            'filter[' . $field . ']',
            '[' . $min . ',' . $max . ']'
        );

        return $this;
    }

    /**
     * Add a new field filter who target all values starting with a certain value
     * Only usable with "get" action
     *
     * @param string $field Field to filter
     * @param string $value Beginning of values to include
     * @return self
     */
    public function addBeginsByFilter(string $field, string $value): self
    {
        $this->checkAllowedActions(['get']);

        $this->addOption(
            'filter[' . $field . ']',
            '[' . $value . ']%'
        );

        return $this;
    }

    /**
     * Add a new field filter who target all values ending with a certain value
     * Only usable with "get" action
     *
     * @param string $field Field to filter
     * @param string $value Endding of values to include
     * @return self
     */
    public function addEndsByFilter(string $field, string $value): self
    {
        $this->checkAllowedActions(['get']);

        $this->addOption(
            'filter[' . $field . ']',
            '%[' . $value . ']'
        );

        return $this;
    }

    /**
     * Add a new field filter who target all values which contain a certain value
     * Only usable with "get" action
     *
     * @param string $field Field to filter
     * @param string $value Containing value to include
     * @return self
     */
    public function addContainsFilter(string $field, string $value): self
    {
        $this->checkAllowedActions(['get']);
        
        $this->addOption(
            'filter[' . $field . ']',
            '%[' . $value . ']%'
        );

        return $this;
    }

    /**
     * Display only given fields
     * Only usable with "get" action
     *
     * @param array $display Fields to display
     * @return self
     */
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

    /**
     * Display all fields
     * Only usable with "get" action
     *
     * @return self
     */
    public function displayFull(): self
    {
        $this->checkAllowedActions(['get']);
        
        $this->addOption(
            'display',
            'full'
        );

        return $this;
    }

    /**
     * Sort query results by fields
     * Only usable with "get" action
     * 
     * The sort array must have this shape:
     * ['field' => 'sort direction']
     * Possible values for the sort direction are 'ASC' and 'DESC' only
     *
     * @param array $sortArray Associative array of fields to sort
     * @return self
     */
    public function sort(array $sortArray): self
    {
        $this->checkAllowedActions(['get']);
        
        if (count($sortArray) === 0) throw new PrestaShopWebserviceException('Sort values array shouldn\'t be empty.');

        $sort = [];
        $allowedOrders = ['ASC', 'DESC'];
        $dateFields = ['date_add', 'date_upd'];
        $allowDateSorting = false;

        foreach ($sortArray as $field => $order) {
            if (!in_array($order, $allowedOrders)) throw new PrestaShopWebserviceException('Please provide a valide order value (ASC or DESC).');

            if (in_array($field, $dateFields)) $allowDateSorting = true;

            $sort[] = $field . '_' . $order;
        }

        if ($allowDateSorting) {
            $this->addOption('date', 1);
        }

        $this->addOption(
            'sort',
            '[' . implode(",", $sort) . ']'
        );

        return $this;
    }

    /**
     * Limit the number of results, you can also define an offset
     * Only usable with "get" action
     *
     * @param integer $limit Number of results to include
     * @param integer $offset Offset of the first result (default is no offset)
     * @return self
     */
    public function limit(int $limit, int $offset = 0): self
    {
        $this->checkAllowedActions(['get']);
        
        $this->addOption(
            'limit',
            $offset > 0 ? $offset . ',' . $limit : $limit
        );

        return $this;
    }

    /**
     * Define the shop to be used as a context for the current query
     *
     * @param integer $idShop ID of the shop to be used
     * @return self
     */
    public function idShop(int $idShop): self
    {
        $this->addOption(
            'id_shop',
            $idShop
        );

        return $this;
    }

    /**
     * Define the group shop to be used as a context for the current query
     *
     * @param integer $idGroupShop ID of the shop group to be used
     * @return self
     */
    public function idGroupShop(int $idGroupShop): self
    {
        $this->addOption(
            'id_group_shop',
            $idGroupShop
        );

        return $this;
    }

    /**
     * Select the kind of resource's shema you want to retrieve 
     * Only usable with "get" action
     *
     * @param string $schema kind of shema
     * @return self
     */
    public function schema(string $schema): self
    {
        $this->checkAllowedActions(['get']);
        
        $this->addOption(
            'schema',
            $schema
        );

        return $this;
    }

    /**
     * Only display localized fields in one language
     * Only usable with "get" action
     *
     * @param integer $languageId Language ID
     * @return self
     */
    public function languageFilter(int $languageId): self
    {
        $this->checkAllowedActions(['get']);
        $this->addOption(
            'language',
            $languageId
        );

        return $this;
    }

    /**
     * Display localized fields for specified list of languages
     * Only usable with "get" action
     *
     * @param array $languagesIds Array of languages IDs
     * @return self
     */
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

    /**
     * Display localized fields for an interval of languages
     * Only usable with "get" action
     *
     * @param integer $minLanguageId Languages min ID
     * @param integer $maxLanguageId Languages max ID
     * @return self
     */
    public function languageIntervalFilter(int $minLanguageId, int $maxLanguageId): self
    {
        $this->checkAllowedActions(['get']);

        $this->addOption(
            'language',
            '[' . $minLanguageId . ',' . $maxLanguageId . ']'
        );

        return $this;
    }

    /**
     * Fill the query with XML data
     * Only usable with "add" and "edit" actions
     *
     * @param SimpleXMLElement $xml XML data you want to send
     * @return self
     */
    public function sendXml(\SimpleXMLElement $xml): self
    {
        $this->checkAllowedActions(['add', 'edit']);
        
        $this->addOption(
            $this->queryAction === 'add' ? 'postXml' : 'putXml',
            $xml
        );

        return $this;
    }

    /**
     * Execute the previously built query and return XML data
     *
     * @return SimpleXMLElement
     */
    public function executeQuery(): \SimpleXMLElement
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

    /**
     * Getter for the queryAction property
     *
     * @return string
     */
    public function getQueryAction(): string
    {
        return $this->queryAction;
    }

    /**
     * Getter for the queryOptions property
     *
     * @return array
     */
    public function getQueryOptions(): array
    {
        return $this->queryOptions;
    }
}
