<?php

namespace Wolf\HelloAsso\Webhook\Handler;

use Wolf\Core\UseCase\UseCaseBus;

class ReceivePaymentHandler implements WebhookHandlerInterface
{
    private $useCaseBus;

    public function __construct(
        UseCaseBus $useCaseBus
    ) {
        $this->useCaseBus = $useCaseBus;
    }

    public function handle(array $payload = []): void
    {
        if ($payload['data']['state'] !== 'Authorized') {
            return;
        }

        $data = $payload['data'] ?? [];

        $externalId = $payload['metadata']['external_id'] ?? '';
        $orderId = $data['order']['id'] ?? '';
        $amount = $data['amount'] ?? 0;
        $payedAt = !empty($data['date']) ? strtotime($data['date']) : time();
        $installmentNumber = $data['installmentNumber'] ?? 0;

        $this->addPayment($amount, $payedAt, $externalId, [
            'provider' => 'helloasso',
            'order_id' => $orderId,
            'installment_number' => $installmentNumber,
        ]);
    }

    private function addPayment(int $amount, int $payedAt, string $externalId, array $meta = []): void
    {
        $this->useCaseBus->execute('wolf-billing.add_payment', [
            'external_id' => $externalId,
            'type' => 'credit',
            'payment_method' => 'credit_card',
            'amount' => $amount,
            'currency' => 'EUR',
            'payed_at' => $payedAt,
            'meta' => $meta
        ]);
    }
}