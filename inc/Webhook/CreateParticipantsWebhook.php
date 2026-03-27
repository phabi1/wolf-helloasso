<?php

namespace Wolf\HelloAsso\Webhook;

class CreateParticipantsWebhook implements WebhookHandlerInterface
{
    private $useCaseBus;

    public function __construct($useCaseBus)
    {
        $this->useCaseBus = $useCaseBus;
    }

    public function handle(array $payload = []): void
    {
        $formType = $payload['formType'] ?? null;
        $formSlug = $payload['formSlug'] ?? null;

        $mapping = $this->useCaseBus->execute('wolf-helloasso.get_mapping', [
            'formType' => $formType,
            'formSlug' => $formSlug,
        ]);

        if (!$mapping) {
            error_log("No mapping found for " . $formType . "/" . $formSlug);
            return;
        }

        list($entityType, $entityId) = explode('@', $mapping->name);

        $event = $this->useCaseBus->execute('wolf-events.get_event', [
            'id' => $entityId
        ]);

        if (!$event) {
            error_log("Event with helloasso_event_id " . $formType . '/' . $formSlug . " not found.");
            return;
        }

        $participantsData = [];
        foreach ($payload['items'] ?? [] as $item) {

            $ticketId = null;
            foreach ($mapping->definition->tickets as $t) {
                if ($t->helloasso_tier_id === $item['tierId']) {
                    $ticketId = $t->ticket_id;
                    break;
                }
            }

            $participantsData[] = [
                'firstname' => $item['user']['firstName'] ?? '',
                'lastname' => $item['user']['lastName'] ?? '',
                'fields' => $this->transformParticipantFields($item['customFields'] ?? [], $mapping),
                'ticket_id' => $ticketId
            ];
        }

        $this->useCaseBus->execute('wolf-events.register_to_event', [
            'event_id' => $event->id,
            'registration' => [
                'amount' => $payload['amount']['total'] ?? 0,
                'firstname' => $payload['payer']['firstName'] ?? '',
                'lastname' => $payload['payer']['lastName'] ?? '',
                'email' => $payload['payer']['email'] ?? '',
                'meta' => [
                    'helloasso_payment_id' => $payload['id'] ?? null,
                ]
            ],
            'participants' => $participantsData,
        ]);
    }

    private function transformParticipantFields(array $customFields, $mapping)
    {
        $fields = [];
        foreach ($customFields as $customField) {
            $fieldName = null;
            foreach ($mapping->definition->participant_fields as $f) {
                if ($f->helloasso_field_id === $customField['id']) {
                    $fieldName = $f->name;
                    break;
                }
            }

            if ($fieldName) {
                $fields[$fieldName] = $customField['answer'] ?? null;
            }
        }
        return $fields;
    }
}