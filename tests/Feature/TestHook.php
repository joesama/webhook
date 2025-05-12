<?php

namespace Joesama\Webhook\Tests\Feature;

use GuzzleHttp\RequestOptions;
use Joesama\Webhook\Tests\AbstractTestCase;
use Joesama\Webhook\Web\Hook;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

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

    #[Test]
    #[TestDox('Hook can be initiated without parameter passed')]
    public function initiateHookWithOutParameter()
    {
        $hook = new Hook();

        $this->configsIsEqual($hook, []);

        $this->hooksIsEqual($hook, []);

        $this->connectorIsEqual($hook, null);

        $this->connectorIdIsEqual($hook, null);
    }

    #[Test]
    #[TestDox('Hook can be initiated with empty array parameter passed')]
    public function initiateHookWithEmptyArray()
    {
        $hook = new Hook([]);

        $this->configsIsEqual($hook, []);

        $this->hooksIsEqual($hook, []);

        $this->connectorIsEqual($hook, null);

        $this->connectorIdIsEqual($hook, null);
    }

    #[Test]
    #[TestDox('Hook can be initiated with array parameter passed')]
    public function initiateHookWithArray()
    {
        $config = [
            'base_uri' => 'https://www.google.com',
        ];

        $hook = new Hook($config);

        $this->configsIsEqual($hook, $config);

        $this->hooksIsEqual($hook, []);

        $this->connectorIsEqual($hook, null);

        $this->connectorIdIsEqual($hook, null);
    }

    #[Test]
    #[TestDox('set webhook configurable')]
    public function setHookConfigurable()
    {
        $config = [
            'base_uri' => 'https://www.google.com',
        ];

        $hook = new Hook();

        $hook->configurable($config);

        $this->configsIsEqual($hook, $config);
    }

    #[Test]
    #[TestDox('set webhook request header')]
    public function setHookRequestHeader()
    {
        $config = [
            'base_uri' => 'https://www.google.com',
        ];

        $hook = new Hook();

        $hook->setRequestHeader($config);

        $this->configsIsEqual($hook, ['headers' => $config]);
    }

    #[Test]
    #[TestDox('Set webhook array request body. Array is not valid request body parameter')]
    public function setHookArrayRequestBody()
    {
        $config = [
            'base_uri' => 'https://www.google.com',
        ];

        $hook = new Hook();

        $hook->setRequestBody($config, 'array');

        $this->configsIsEqual($hook, []);

        $this->optionIsEqual($hook, ['array' => $config]);
    }

    #[Test]
    #[TestDox('set webhook json request body')]
    public function setHookJsonRequestBody()
    {
        $config = [
            'base_uri' => 'https://www.google.com',
        ];

        $hook = new Hook();

        $hook->setRequestBody($config, RequestOptions::JSON);

        $this->configsIsEqual(
            $hook,
            [
                'headers' => ['Content-type' => 'application/json'],
            ]
        );

        $this->optionIsEqual($hook, [RequestOptions::JSON => $config]);
    }

    #[Test]
    #[TestDox('set webhook multi part request body')]
    public function setHookMultiPartRequestBody()
    {
        $config = [
            'base_uri' => 'https://www.google.com',
        ];

        $hook = new Hook();

        $hook->setRequestBody($config, RequestOptions::MULTIPART);

        $this->configsIsEqual(
            $hook,
            [
                'headers' => ['Content-type' => 'multipart/form-data'],
            ]
        );

        $this->optionIsEqual($hook, [RequestOptions::MULTIPART => $config]);
    }

    #[Test]
    #[TestDox('set webhook form params request body')]
    public function setHookFormParamsRequestBody()
    {
        $config = [
            'base_uri' => 'https://www.google.com',
        ];

        $hook = new Hook();

        $hook->setRequestBody($config, RequestOptions::FORM_PARAMS);

        $this->configsIsEqual(
            $hook,
            [
                'headers' => ['Content-type' => 'application/x-www-form-urlencoded'],
            ]
        );

        $this->optionIsEqual($hook, [RequestOptions::FORM_PARAMS => $config]);
    }

    #[Test]
    #[TestDox('get webhook response')]
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
