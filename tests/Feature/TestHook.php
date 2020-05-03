<?php

namespace Joesama\Webhook\Tests\Feature;

use Joesama\Webhook\Web\Hook;
use GuzzleHttp\RequestOptions;
use Joesama\Webhook\Tests\AbstractTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestHook extends AbstractTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(Hook::class)->shouldReceive('__contructor');
    }

    /**
     * @testdox Hook can be initiated without parameter passed.
     *
     * @test
     *
     * @return void
     */
    public function initiateHookWithOutParameter()
    {
        $hook = new Hook();

        $this->configsIsEqual($hook, []);

        $this->hooksIsEqual($hook, null);

        $this->connectorIsEqual($hook, null);

        $this->connectorIdIsEqual($hook, null);
    }

    /**
     * @testdox Hook can be initiated with empty array parameter passed.
     *
     * @test
     *
     * @return void
     */
    public function initiateHookWithEmptyArray()
    {
        $hook = new Hook([]);

        $this->configsIsEqual($hook, []);

        $this->hooksIsEqual($hook, null);

        $this->connectorIsEqual($hook, null);

        $this->connectorIdIsEqual($hook, null);
    }

    /**
     * @testdox Hook can be initiated with array parameter passed.
     *
     * @test
     *
     * @return void
     */
    public function initiateHookWithArray()
    {
        $config = [
            'base_uri' => 'https://www.google.com'
        ];

        $hook = new Hook($config);

        $this->configsIsEqual($hook, $config);

        $this->hooksIsEqual($hook, null);

        $this->connectorIsEqual($hook, null);

        $this->connectorIdIsEqual($hook, null);
    }

    /**
     * @testdox set webhook configurable.
     *
     * @test
     *
     * @return void
     */
    public function setHookConfigurable()
    {
        $config = [
            'base_uri' => 'https://www.google.com'
        ];

        $hook = new Hook();

        $hook->configurable($config);

        $this->configsIsEqual($hook, $config);
    }

    /**
     * @testdox set webhook request header.
     *
     * @test
     *
     * @return void
     */
    public function setHookRequestHeader()
    {
        $config = [
            'base_uri' => 'https://www.google.com'
        ];

        $hook = new Hook();

        $hook->setRequestHeader($config);

        $this->configsIsEqual($hook, [ 'headers' => $config ]);
    }

    /**
     * @testdox set webhook array request body.
     * Array is not valid request body parameter.
     *
     * @test
     *
     * @return void
     */
    public function setHookArrayRequestBody()
    {
        $config = [
            'base_uri' => 'https://www.google.com'
        ];

        $hook = new Hook();

        $hook->setRequestBody($config, 'array');

        $this->configsIsEqual($hook, []);

        $this->optionIsEqual($hook, ['array' => $config ]);
    }

    /**
     * @testdox set webhook json request body.
     *
     * @test
     *
     * @return void
     */
    public function setHookJsonRequestBody()
    {
        $config = [
            'base_uri' => 'https://www.google.com'
        ];

        $hook = new Hook();

        $hook->setRequestBody($config, RequestOptions::JSON);

        $this->configsIsEqual(
            $hook,
            [
                'headers' => ['Content-type' => 'application/json']
            ]
        );

        $this->optionIsEqual($hook, [RequestOptions::JSON => $config ]);
    }

    /**
     * @testdox set webhook multi part request body.
     *
     * @test
     *
     * @return void
     */
    public function setHookMultiPartRequestBody()
    {
        $config = [
            'base_uri' => 'https://www.google.com'
        ];

        $hook = new Hook();

        $hook->setRequestBody($config, RequestOptions::MULTIPART);

        $this->configsIsEqual(
            $hook,
            [
                'headers' => ['Content-type' => 'multipart/form-data']
            ]
        );

        $this->optionIsEqual($hook, [RequestOptions::MULTIPART => $config ]);
    }

    /**
     * @testdox set webhook form params request body.
     *
     * @test
     *
     * @return void
     */
    public function setHookFormParamsRequestBody()
    {
        $config = [
            'base_uri' => 'https://www.google.com'
        ];

        $hook = new Hook();

        $hook->setRequestBody($config, RequestOptions::FORM_PARAMS);

        $this->configsIsEqual(
            $hook,
            [
                'headers' => ['Content-type' => 'application/x-www-form-urlencoded']
            ]
        );

        $this->optionIsEqual($hook, [RequestOptions::FORM_PARAMS => $config ]);
    }

    /**
     * @testdox get webhook response.
     *
     * @test
     *
     * @return void
     */
    public function getHookResponse()
    {
        $this->expectException('Joesama\Webhook\Exceptions\WebHookException');

        $hook = new Hook();

        $hook->setRequestBody([]);

        $hook->getResponse('http://www.google.com', 'GET');
    }

    /** Validate configs */
    protected function configsIsEqual($hook, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->getPropertyValue($hook, 'configs')
        );
    }

    /** Validate hooks */
    protected function hooksIsEqual($hook, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->getPropertyValue($hook, 'hooks')
        );
    }

    /** Validate connector */
    protected function connectorIsEqual($hook, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->getPropertyValue($hook, 'connector')
        );
    }

    /** Validate connector id */
    protected function connectorIdIsEqual($hook, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->getPropertyValue($hook, 'connectorId')
        );
    }

    /** Validate option */
    protected function optionIsEqual($hook, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->getPropertyValue($hook, 'options')
        );
    }

    /** Validate method */
    protected function methodIsEqual($hook, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->getPropertyValue($hook, 'method')
        );
    }

    /** Validate path uri */
    protected function pathIsEqual($hook, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->getPropertyValue($hook, 'pathUri')
        );
    }
}
