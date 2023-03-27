<?php

namespace Joesama\Webhook\Tests\Feature;

use Joesama\Webhook\Examples\ExamplesConnector;
use Joesama\Webhook\Web\Hook;

class HookConnectorTest extends TestHook
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(Hook::class)->shouldReceive('__contructor')->withArgs([
            ExamplesConnector::class,
        ]);
    }

    /**
     * @testdox Hook can be initiated connector passed.
     *
     * @test
     *
     * @return void
     */
    public function initiateHookWithConnector()
    {
        $connector = new ExamplesConnector();

        $hook = new Hook($connector);

        $this->configsIsEqual($hook, [
            'headers' => [
                'Content-type' => 'application/json',
            ],
        ]);

        $this->hooksIsEqual($hook, null);

        $this->connectorIsEqual($hook, $connector);

        $this->optionIsEqual(
            $hook,
            ['json' => []]
        );

        $this->connectorIdIsEqual($hook, class_basename(ExamplesConnector::class));
    }
}
