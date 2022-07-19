<?php

use PHPUnit\Framework\TestCase;
use Jupi\PrestaShopWebserviceExtra\PrestaShopWebserviceExtra;
use Jupi\PrestaShopWebserviceExtra\Libraries\PrestaShopWebserviceException;

class PrestaShopWebserviceExtraTest extends TestCase
{
    public function testCheckAllowedActionsWithNull()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice->id(2);
    }

    public function testCheckAllowedActionsWithUnallowed()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice
            ->add('products')
            ->id(2);
    }

    public function testGetAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->get('products');

        $this->assertEquals($webservice->getQueryAction(), 'get');
    }

    public function testGetUrlAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->getUrl('http://shop.com/api/products');

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), ['url' => 'http://shop.com/api/products']);
    }

    public function testGetBlankSchemaAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->getBlankSchema('products');

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), ['url' => 'https://shop.com/api/products?schema=blank']);
    }

    public function testGetSynopsisSchemaAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->getSynopsisSchema('products');

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), ['url' => 'https://shop.com/api/products?schema=synopsis']);
    }

    public function testAddAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->add('products');

        $this->assertEquals($webservice->getQueryAction(), 'add');
    }

    public function testAddUrlAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->addUrl('http://shop.com/api/products');

        $this->assertEquals($webservice->getQueryAction(), 'add');
        $this->assertEquals($webservice->getQueryOptions(), ['url' => 'http://shop.com/api/products']);
    }

    public function testEditAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->edit('products');

        $this->assertEquals($webservice->getQueryAction(), 'edit');
    }

    public function testEditUrlAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->editUrl('http://shop.com/api/products/2');

        $this->assertEquals($webservice->getQueryAction(), 'edit');
        $this->assertEquals($webservice->getQueryOptions(), ['url' => 'http://shop.com/api/products/2']);
    }

    public function testDeleteAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->delete('products');

        $this->assertEquals($webservice->getQueryAction(), 'delete');
    }

    public function testDeleteUrlAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->deleteUrl('http://shop.com/api/products/2');

        $this->assertEquals($webservice->getQueryAction(), 'delete');
        $this->assertEquals($webservice->getQueryOptions(), ['url' => 'http://shop.com/api/products/2']);
    }

    public function testSetAction()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->get('products');
    }

    public function testAddOption()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->id(2)
            ->id(3);
    }

    public function testIdOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->id(2);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'id' => 2
        ]);
    }

    public function testValueFilterOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addValueFilter('field', 'value');

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'filter[field]' => '[value]'
        ]);
    }

    public function testValuesFilterOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addValuesFilter('field', ['value1', 'value2']);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'filter[field]' => '[value1|value2]'
        ]);
    }

    public function testValuesFilterOptionWithEmptyArray()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addValuesFilter('field', []);
    }

    public function testIntervalFilterOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addIntervalFilter('field', 1, 5);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'filter[field]' => '[1,5]'
        ]);
    }

    public function testBeginsByFilterOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addBeginsByFilter('field', 'begin');

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'filter[field]' => '[begin]%'
        ]);
    }

    public function testEndsByFilterOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addEndsByFilter('field', 'end');

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'filter[field]' => '%[end]'
        ]);
    }

    public function testContainsFilterOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addContainsFilter('field', 'end');

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'filter[field]' => '%[end]%'
        ]);
    }

    public function testAddPriceParameterWithWrongAction()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice
            ->add('product')
            ->addCountryPriceParameter('field', 1);
    }

    public function testAddPriceParameterWithWrongResource()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('category')
            ->addCountryPriceParameter('field', 1);
    }

    public function testAddCountryPriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addCountryPriceParameter('field', 1);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][country]' => 1
        ]);
    }

    public function testAddStatePriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addStatePriceParameter('field', 1);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][state]' => 1
        ]);
    }

    public function testAddPostcodePriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addPostcodePriceParameter('field', 43000);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][postcode]' => 43000
        ]);
    }

    public function testAddCurrencyPriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addCurrencyPriceParameter('field', 1);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][currency]' => 1
        ]);
    }

    public function testAddGroupPriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addGroupPriceParameter('field', 1);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][group]' => 1
        ]);
    }

    public function testAddQuantityPriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addQuantityPriceParameter('field', 12);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][quantity]' => 12
        ]);
    }

    public function testAddProductAttributePriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addProductAttributePriceParameter('field', 1);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][product_attribute]' => 1
        ]);
    }

    public function testAddDecimalsPriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addDecimalsPriceParameter('field', 2);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][decimals]' => 2
        ]);
    }

    public function testAddUseTaxPriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addUseTaxPriceParameter('field', true);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][use_tax]' => true
        ]);
    }

    public function testAddUseReductionPriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addUseReductionPriceParameter('field', true);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][use_reduction]' => true
        ]);
    }

    public function testAddOnlyReductionPriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addOnlyReductionPriceParameter('field', true);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][only_reduction]' => true
        ]);
    }

    public function testAddUseEcotaxPriceParameter()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->addUseEcotaxPriceParameter('field', true);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'price[field][use_ecotax]' => true
        ]);
    }

    public function testDisplayOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->display(['field1', 'field2']);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'display' => '[field1,field2]'
        ]);
    }

    public function testDisplayFullOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->displayFull();

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'display' => 'full'
        ]);
    }

    public function testDisplayOptionWithEmptyArray()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->display([]);
    }

    public function testSortOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->sort([
                'field1' => 'ASC',
                'field2' => 'DESC'
            ]);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'sort' => '[field1_ASC,field2_DESC]'
        ]);
    }

    public function testSortOptionWithDate()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->sort([
                'date_add' => 'ASC'
            ]);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'sort' => '[date_add_ASC]',
            'date' => 1
        ]);
    }

    public function testSortOptionWithEmptyArray()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->sort([]);
    }

    public function testSortOptionWithWrongOrder()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->sort([
                'field1' => 'DESC',
                'filed2' => 'ACS'
            ]);
    }

    public function testLimitOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->limit(5);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'limit' => 5
        ]);
    }

    public function testLimitOptionWithOffset()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->limit(5, 3);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'limit' => '3,5'
        ]);
    }

    public function testIdShopOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->idShop(2);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'id_shop' => 2
        ]);
    }

    public function testIdShopGroupOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->idGroupShop(2);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'id_group_shop' => 2
        ]);
    }

    public function testSchemaOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->schema('schema');

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'schema' => 'schema'
        ]);
    }

    public function testGetSynopsisSchema()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->getSynopsisSchema('products');

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'url' => 'https://shop.com/api/products?schema=synopsis'
        ]);
    }

    public function testGetBlankSchema()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice->getBlankSchema('products');

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'url' => 'https://shop.com/api/products?schema=blank'
        ]);
    }

    public function testLanguageFilterOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->languageFilter(2);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'language' => '2'
        ]);
    }

    public function testLanguagesFilterOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->languagesFilter([2, 3]);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'language' => '[2|3]'
        ]);
    }

    public function testLanguagesFilterOptionWithEmptyArray()
    {
        $this->expectException(PrestaShopWebserviceException::class);

        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->languagesFilter([]);
    }

    public function testLanguageIntervalFilterOption()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->get('products')
            ->languageIntervalFilter(1, 5);

        $this->assertEquals($webservice->getQueryAction(), 'get');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'language' => '[1,5]'
        ]);
    }

    public function testSendXmlOptionWithAddAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->add('products')
            ->sendXml(new \SimpleXMLElement('<settings/>'));

        $this->assertEquals($webservice->getQueryAction(), 'add');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'postXml' => (new \SimpleXMLElement('<settings/>'))->asXML()
        ]);
    }

    public function testSendXmlOptionWithEditAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->edit('products')
            ->id(3)
            ->sendXml(new \SimpleXMLElement('<settings/>'));

        $this->assertEquals($webservice->getQueryAction(), 'edit');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'id' => 3,
            'putXml' => (new \SimpleXMLElement('<settings/>'))->asXML()
        ]);
    }

    private function getWebserviceInstance(): PrestaShopWebserviceExtra
    {
        return new PrestaShopWebserviceExtra('https://shop.com', 'APIKEY123456789', false);
    }
}
