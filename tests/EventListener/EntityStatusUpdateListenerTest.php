<?php

namespace App\Tests\Service;

use App\Entity\Promise;
use App\Entity\StatusUpdate;
use App\Repository\ActionRepository;
use App\Repository\PromiseRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntityStatusUpdateListenerTest extends KernelTestCase
{
    public function testStatusUpdates()
    {
        /** @var ObjectManager $em */
        $em = self::bootKernel()->getContainer()->get('doctrine.orm.default_entity_manager');
        /** @var PromiseRepository $promiseRepo */
        $promiseRepo = $em->getRepository('App:Promise');
        /** @var ActionRepository $actionsRepo */
        $actionsRepo = $em->getRepository('App:Action');

        $promise = new Promise();
        $promise
            ->setName('Test')
            ->setSlug('test')
            ->setDescription('Test')
            ->setMadeTime(new \DateTime())
            ->setStatus(null)
            ->setMandate($em->getRepository('App:Mandate')->findOneBy([]));
        $em->persist($promise);

        $em->flush();

        $this->assertEquals(null, $promiseRepo->find($promise->getId())->getStatus());

        $actions = $actionsRepo->findBy([], null, 2);
        $this->assertCount(2, $actions);

        $statusUpdate = new StatusUpdate();
        $statusUpdate
            ->setPromise($promise)
            ->setAction($actions[0])
            ->setStatus($em->getRepository('App:Status')->findOneBy([]));
        $em->persist($statusUpdate);

        $em->flush();

        $this->assertEquals(
            $statusUpdate->getStatus()->getId(),
            $promiseRepo->find($promise->getId())->getStatus()->getId()
        );

        $statusUpdate->setStatus(
            $em->getRepository('App:Status')
                ->createQueryBuilder('s')
                ->where('s.id != :status')
                ->setParameter('status', $statusUpdate->getStatus())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
        );

        $em->flush();

        $this->assertEquals(
            $statusUpdate->getStatus()->getId(),
            $promiseRepo->find($promise->getId())->getStatus()->getId()
        );

        $statusUpdate2 = new StatusUpdate();
        $statusUpdate2
            ->setPromise($promise)
            ->setAction($actions[1])
            ->setStatus(null);
        $em->persist($statusUpdate2);

        $em->flush();

        $this->assertEquals(
            $statusUpdate->getStatus()->getId(),
            $promiseRepo->find($promise->getId())->getStatus()->getId()
        );

        $em->remove($statusUpdate2);
        $em->flush();

        $em->remove($statusUpdate);
        $em->flush();

        $this->assertEquals(null, $promiseRepo->find($promise->getId())->getStatus());

        $em->remove($promise);
        $em->flush();
    }
}
