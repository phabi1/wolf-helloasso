<?php

namespace Wolf\HelloAsso\Webhook\Handler;

use DateTime;
use Wolf\Core\Helper\StringHelper;
use Wolf\Core\UseCase\UseCaseBus;
use Wolf\Events\Model\CheckoutStatus;

class ReceiveOrderHandler implements WebhookHandlerInterface
{
    private $stringHelper;
    private $useCaseBus;

    public function __construct(
        UseCaseBus $useCaseBus,
        StringHelper $stringHelper
    ) {
        $this->useCaseBus = $useCaseBus;
        $this->stringHelper = $stringHelper;
    }

    public function handle(array $payload = []): void
    {
        $state = $payload['data']['state'] ?? '';
        $externalId = $payload['metadata']['external_id'] ?? '';
        if ($state === 'Authorized' || $state === 'Captured') {
            $externalId = $payload['metadata']['external_id'] ?? '';
            if ($this->stringHelper->startsWith($externalId, 'event:')) {
                $checkoutId = str_replace('event:', '', $externalId);
                $this->useCaseBus->execute('wolf-events.paid_checkout', [
                    'id' => $checkoutId,
                    'status' => CheckoutStatus::PAID,
                ]);
            }
        }
    }
}