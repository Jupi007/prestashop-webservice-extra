<?php

use PHPUnit\Framework\TestCase;
use Jupi007\PrestaShopWebserviceExtra\PrestaShopWebserviceExtra;
use Jupi007\PrestaShopWebserviceExtra\Libraries\PrestaShopWebserviceException;

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
            ->sendXml('xml data');

        $this->assertEquals($webservice->getQueryAction(), 'add');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'postXml' => 'xml data'
        ]);
    }

    public function testSendXmlOptionWithEditAction()
    {
        $webservice = $this->getWebserviceInstance();
        $webservice
            ->edit('products')
            ->id(3)
            ->sendXml('xml data');

        $this->assertEquals($webservice->getQueryAction(), 'edit');
        $this->assertEquals($webservice->getQueryOptions(), [
            'resource' => 'products',
            'id' => 3,
            'putXml' => 'xml data'
        ]);
    }

    private function getWebserviceInstance(): PrestaShopWebserviceExtra
    {
        return new PrestaShopWebserviceExtra('https://shop.com', 'APIKEY123456789', false);
    }
}
