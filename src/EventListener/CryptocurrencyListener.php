<?php

namespace App\EventListener;

use App\Entity\Cryptocurrency;
use App\Service\CryptocurrencyService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(Events::prePersist)]
class CryptocurrencyListener
{
    public function __construct(
        private readonly CryptocurrencyService $cryptocurrencyService)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if(!$entity instanceof Cryptocurrency || $entity->getLibelle() !== null) {
            return;
        }

        $this->cryptocurrencyService->updateDatas($entity);
    }
}