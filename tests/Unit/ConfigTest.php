<?php

namespace Joesama\Webhook\Tests\Unit;

use TypeError;
use GuzzleHttp\RequestOptions;
use Joesama\Webhook\Web\Config;
use Joesama\Webhook\Tests\AbstractTestCase;

class TestConfig extends AbstractTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(Config::class)->shouldReceive('__contructor');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('webhooks.test', [
            'base_uri' => 'test.com',
            RequestOptions::BODY => [ 'content' => RequestOptions::BODY],
            RequestOptions::FORM_PARAMS => [ 'content' =>  RequestOptions::FORM_PARAMS],
            RequestOptions::JSON => [ 'content' =>  RequestOptions::JSON],
            RequestOptions::MULTIPART => [ 'content' =>  RequestOptions::MULTIPART],
            RequestOptions::QUERY => [ 'content' =>  RequestOptions::QUERY],
            Config::REQUEST_URI  => [ 'content' =>  Config::REQUEST_URI]
        ]);
    }

    /**
     * @testdox Config can be initiated when array parameter passed.
     *
     * @test
     *
     * @return void
     */
    public function initiateConfigWithArrayParameter()
    {
        $array = [
            RequestOptions::BODY => [ 'content' => RequestOptions::BODY],
            RequestOptions::FORM_PARAMS => [ 'content' =>  RequestOptions::FORM_PARAMS],
            RequestOptions::JSON => [ 'content' =>  RequestOptions::JSON],
            RequestOptions::MULTIPART => [ 'content' =>  RequestOptions::MULTIPART],
            RequestOptions::QUERY => [ 'content' =>  RequestOptions::QUERY],
            Config::REQUEST_URI  => [ 'content' =>  Config::REQUEST_URI]
        ];

        $config = new Config($array);

        $this->assertNotEquals($array, $config->configs);

        $this->assertEmpty($config->configs);

        $this->assertIsArray($config->toArray());

        $this->assertEquals($array, $config->toArray());
    }

    /**
     * @testdox Config can be initiated when string parameter passed.
     *
     * @test
     *
     * @return void
     */
    public function initiateConfigWithStringParameter()
    {
        $this->expectException(TypeError::class);

        $config = new Config('example');
    }
    /**
     * @testdox Config can be initiated when config file name passed as parameter.
     *
     * @test
     *
     * @return void
     */
    public function initiateConfigWithConfigFileNameAsParameter()
    {
        $config = new Config('test');

        $this->assertNotEmpty($config->hooks);

        $this->assertNotEmpty($config->configs);

        $this->assertEquals(config('webhooks.test'), $config->toArray());
    }
}
