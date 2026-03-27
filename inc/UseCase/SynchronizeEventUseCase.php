<?php

namespace Wolf\HelloAsso\UseCase;

use Wolf\Core\Entity\EntityManager;
use Wolf\Core\Helper\StringHelper;
use Wolf\Core\UseCase\UseCaseInterface;
use Wolf\HelloAsso\Sdk\Client;

class SynchronizeEventUseCase implements UseCaseInterface
{
    private $useCaseBus;

    private $mappingRepository;

    private $client;

    private $stringHelper;

    public function __construct($useCaseBus, EntityManager $entityManager, Client $client, StringHelper $stringHelper)
    {
        $this->useCaseBus = $useCaseBus;
        $this->mappingRepository = $entityManager->getRepository('wolf-helloasso.mapping');
        $this->client = $client;
        $this->stringHelper = $stringHelper;
    }

    public function execute(array $params = [])
    {
        if (empty($params['formType']) || empty($params['formSlug'])) {
            throw new \InvalidArgumentException('formType and formSlug are required');
        }

        if (empty($params['eventId'])) {
            throw new \InvalidArgumentException('eventId is required');
        }

        $helloassoId = $params['formType'] . '@' . $params['formSlug'];
        $eventId = 'event@' . $params['eventId'];

        $mapping = $this->mappingRepository->findOne(['helloasso_id' => ['eq' => $helloassoId]]);

        $event = $this->useCaseBus->execute('wolf-events.get_event', [
            'id' => $params['eventId']
        ]);
        $helloassoEvent = $this->client->getForms()->item($params['formType'], $params['formSlug']);

        $errors = [];

        // Check tickets and participant fields in the mapping definition, and update them if they differ from the HelloAsso event data
        $resParticipantFields = $this->updateParticipantFields($helloassoEvent, $event);

        if (!empty($resParticipantFields['errors'])) {
            $errors = array_merge($errors, $resParticipantFields['errors']);
        }

        $resTickets = $this->updateTickets($helloassoEvent, $event);

        if (!empty($resTickets['errors'])) {
            $errors = array_merge($errors, $resTickets['errors']);
        }

        // Transform the HelloAsso event data into the mapping definition format
        $mappingDefintion = [
            'tickets' => $resTickets['mapping'],
            'participant_fields' => $resParticipantFields['mapping']
        ];

        // Update the mapping with the new definition
        if ($mapping) {
            $this->mappingRepository->update($mapping->id, ['definition' => $mappingDefintion]);
        } else {
            $this->mappingRepository->insert([
                'helloasso_id' => $helloassoId,
                'name' => $eventId,
                'definition' => $mappingDefintion,
                'created_at' => time()
            ]);
        }

        return [
            'success' => empty($errors),
            'errors' => $errors
        ];

    }

    private function updateTickets($helloassoEvent, $event)
    {
        $helloassoTiers = $helloassoEvent['tiers'] ?? [];

        $errors = [];
        $newMapping = [];
        foreach ($helloassoTiers as $tier) {
            $existingTicket = $this->findExistingTicket($tier, $event->tickets ?? []);

            if ($existingTicket) {
                $newMapping[] = [
                    'ticket_id' => $existingTicket->id,
                    'helloasso_tier_id' => $tier['id']
                ];
            } else {
                $errors[] = "Ticket '{$tier['label']}' not found in event";
            }
        }

        return [
            'tickets' => $helloassoTiers,
            'mapping' => $newMapping,
            'errors' => $errors
        ];
    }

    private function updateParticipantFields($helloassoEvent, $event)
    {
        $helloassoFields = [];
        $errors = [];
        $newMapping = [];
        foreach ($helloassoEvent['tiers'] as $tier) {
            $fields = $this->extractFieldsFromTier($tier);
            $helloassoFields = $helloassoFields + $fields;
        }

        foreach ($helloassoFields as $fieldId => $field) {
            $existingField = $this->findExistingField($field, $event->participant_fields ?? []);

            if ($existingField) {
                $newMapping[] = [
                    'name' => $existingField->name,
                    'helloasso_field_id' => $fieldId
                ];
            } else {
                $errors[] = "Field '{$field['name']}' not found in event";
            }
        }

        return [
            'fields' => $helloassoFields,
            'mapping' => $newMapping,
            'errors' => $errors
        ];
    }

    private function findExistingTicket($tier, $eventTickets)
    {
        $tierLabelSlug = $this->stringHelper->slug($tier['label'] ?? '');
        foreach ($eventTickets as $ticket) {
            if ($tierLabelSlug === $this->stringHelper->slug($ticket->title ?? '')) {
                return $ticket;
            }
        }
        return null;
    }

    private function findExistingField($field, $eventFields)
    {
        foreach ($eventFields as $eventField) {
            if ($eventField->name === $field['name']) {
                return $eventField;
            }
        }
        return null;
    }

    private function extractFieldsFromTier($tier)
    {
        $fields = [];
        $customFields = $tier['customFields'] ?? [];
        foreach ($customFields as $field) {

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
}