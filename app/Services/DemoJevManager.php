<?php

declare(strict_types=1);

namespace App\Services;

use Priyanshu\LaravelJev\Contracts\Jev as JevContract;
use Priyanshu\LaravelJev\JevManager;
use Priyanshu\LaravelJev\Testing\JevFake;

class DemoJevManager extends JevManager
{
    /**
     * Swap the manager with the enhanced demo fake for offline simulation.
     */
    public function fake(array $expectations = []): JevFake
    {
        $this->fake = new DemoJevFake($expectations);

        $this->app->instance('jev', $this->fake);
        $this->app->instance(JevContract::class, $this->fake);

        return $this->fake;
    }
}
