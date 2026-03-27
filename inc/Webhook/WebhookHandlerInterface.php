<?php

namespace Wolf\HelloAsso\Webhook;

interface WebhookHandlerInterface
{
    public function handle(array $payload = []): void;
}