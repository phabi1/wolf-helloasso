<?php

namespace Wolf\HelloAsso\UseCase;

use Wolf\Core\Entity\EntityManager;
use Wolf\Core\UseCase\UseCaseInterface;

class CreateMappingUseCase implements UseCaseInterface
{
    private $mappingRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->mappingRepository = $entityManager->getRepository('wolf-helloasso.mapping');
    }

    public function execute(array $params = [])
    {
        $this->mappingRepository->insert($params);
    }
}