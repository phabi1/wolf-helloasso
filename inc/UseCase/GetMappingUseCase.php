<?php

namespace Wolf\HelloAsso\UseCase;

use Wolf\Core\Entity\EntityManager;
use Wolf\Core\Entity\EntityRepository;
use Wolf\Core\UseCase\UseCaseInterface;

class GetMappingUseCase implements UseCaseInterface
{
    /**
     * @var EntityRepository 
     */
    private $mappingRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->mappingRepository = $entityManager->getRepository('wolf-helloasso.mapping');
    }

    public function execute(array $data = [])
    {
        $formType = $data['formType'] ?? null;
        $formSlug = $data['formSlug'] ?? null;

        if (!$formType) {
            throw new \InvalidArgumentException("Form type is required");
        }

        return $this->mappingRepository->findOne(['formType' => ['eq' => $formType], 'formSlug' => ['eq' => $formSlug]]);
    }
}