<?php

namespace Wolf\HelloAsso\Webhook;

use DateTime;
use Wolf\Core\Helper\StringHelper;
use Wolf\Core\UseCase\UseCaseBus;

class CreateEventWebhook implements WebhookHandlerInterface
{
    private $useCaseBus;

    private $stringHelper;

    public function __construct(
        UseCaseBus $useCaseBus,
        StringHelper $stringHelper
    ) {
        $this->useCaseBus = $useCaseBus;
        $this->stringHelper = $stringHelper;
    }

    public function handle(array $payload = []): void
    {
        $mappingDefintion = [
            'tickets' => [],
            'participant_fields' => []
        ];

        $participantFieldsRes = $this->tranformCustomFields($payload['tiers'] ?? []);

        $eventData = [
            'title' => $payload['title'] ?? 'Untitled Event',
            'event_start' => $this->convertDateToTimestamp($payload['startDate']),
            'event_end' => $this->convertDateToTimestamp($payload['endDate']),
            'participant_fields' => $participantFieldsRes['fields']
        ];

        $sessionData = [
            'title' => 'Untitled Session',
            'session_start' => $this->convertDateToTimestamp($payload['startDate']),
            'session_end' => $this->convertDateToTimestamp($payload['endDate']),
        ];

        $eventData['sessions'] = [$sessionData];

        $mappingDefintion['participant_fields'] = $participantFieldsRes['mapping'];

        $event = $this->useCaseBus->execute('wolf-events.create_event', $eventData);

        foreach ($payload['tiers'] ?? [] as $tier) {

            $ticketParticipantFields = $this->extractFieldNamesFromTier($tier);

            $ticketData = [
                'title' => $tier['label'] ?? 'Untitled Ticket',
                'amount' => $tier['price'] ?? 0,
                'participant_fields' => $ticketParticipantFields
            ];
            $createdTicket = $this->useCaseBus->execute(
                'wolf-events.create_ticket_for_event',
                [
                    'event_id' => $event->id,
                    'data' => $ticketData
                ]
            );

            $mappingDefintion['tickets'][] = [
                'helloasso_tier_id' => $tier['id'],
                'ticket_id' => $createdTicket->id
            ];
        }

        $this->useCaseBus->execute('wolf-helloasso.create_mapping', [
            'helloasso_id' => $payload['formType'] . '@' . $payload['formSlug'],
            'name' => 'event@' . $event->id,
            'definition' => $mappingDefintion,
            'created_at' => time()
        ]);
    }

    private function tranformCustomFields(array $tiers)
    {
        $fields = [];
        $mapping = [];

        foreach ($tiers as $tier) {
            $customFields = $tier['customFields'] ?? [];
            foreach ($customFields as $field) {

                if (array_key_exists($field['id'], $fields)) {
                    continue;
                }

                switch ($field['type'] ?? 'string') {
                    case 'ChoiceList':
                        $options = $this->transformChoiceListToOptions($field);
                        break;
                    case 'File':
                        $options = $this->transformFileToOptions($field);
                        break;
                    default:
                        $options = [];
                }

                $label = $field['label'] ?? 'Untitled Field';
                $name = $this->stringHelper->slug($label);

                $fields[$field['id']] = [
                    'name' => $name,
                    'label' => $label,
                    'type' => $field['type'] ?? 'string',
                    'required' => $field['isRequired'] ?? false,
                    'options' => $options
                ];

                $mapping[$name] = ['id' => $field['id'], 'type' => $field['type']];
            }
        }
        return [
            'fields' => array_values($fields),
            'mapping' => $mapping
        ];
    }

    private function extractFieldNamesFromTier($tier)
    {
        $fields = [];
        if ($tier['customFields']) {
            foreach ($tier['customFields'] as $field) {
                $fields[] = $this->stringHelper->slug($field['label'] ?? 'Untitled Field');
            }
        }
        return $fields;
    }

    private function transformChoiceListToOptions($field)
    {
        $options = [];
        if (isset($field['choices']) && is_array($field['choices'])) {
            foreach ($field['choices'] as $choice) {
                $options[] = [
                    'label' => $choice,
                    'value' => $choice
                ];
            }
        }
        return $options;
    }

    private function transformFileToOptions($field)
    {
        return [];
    }

    private function convertDateToTimestamp($value)
    {
        $date = new DateTime($value);
        return $date->getTimestamp();
    }
}