<?php

namespace Wolf\HelloAsso;

use Wolf\Core\Di\ContainerAwareInterface;
use Wolf\Core\Di\ContainerAwareTrait;
use Wolf\Core\Di\Locator;

class WebhookBus implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private $locator;

    public function execute($event, array $payload = []): void
    {
        if (!$this->locator) {
            $this->locator = new Locator('wolf-helloasso.webhook_handler');
            $this->locator->setContainer($this->container);
        }

        $handler = $this->locator->get($event);

        $handler->handle($payload);
    }
}