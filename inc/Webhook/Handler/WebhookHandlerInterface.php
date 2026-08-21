<?php

namespace Wolf\HelloAsso\Webhook\Handler;

interface WebhookHandlerInterface
{
    public function handle(array $payload = []): void;
}