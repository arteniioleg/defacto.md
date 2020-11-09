<?php

namespace App\Repository;

use App\Entity\PromiseAction;
use App\Entity\Power;
use App\Entity\Promise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class PromiseActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromiseAction::class);
    }

    public function getAdminList(Request $request)
    {
        return $this->findBy([], ['occurredTime' => 'DESC']);
    }

    public function getAdminListByPromise(Promise $promise)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.promiseUpdates', 'pu', 'WITH', 'pu.promise = :promise')
            ->orderBy('a.occurredTime', 'DESC')
            ->setParameter('promise', $promise)
            ->getQuery()
            ->getResult();
    }

    public function getAdminOrphanList()
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.promiseUpdates', 'pu')
            ->where('pu.promise IS NULL')
            ->orderBy('a.occurredTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getAdminPowerChoices(PromiseAction $action)
    {
        $choices = [];

        foreach (
            $action->getMandate()->getInstitutionTitle()->getTitle()->getPowers()
            as $power /** @var Power $power */
        ) {
            $choices[ $power->getName() ] = $power;
        }

        return $choices;
    }

    public function hasConnections(string $id) : bool
    {
        return false;
    }
}
