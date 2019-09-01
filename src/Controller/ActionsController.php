<?php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\Politician;
use App\Repository\ElectionRepository;
use App\Repository\MandateRepository;
use App\Repository\PoliticianRepository;
use App\Repository\ActionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActionsController extends AbstractController
{
    private $paginator;
    private $actionRepository;
    private $mandateRepository;
    private $politicianRepository;
    private $electionRepository;

    public function __construct(
        PaginatorInterface $paginator,
        ActionRepository $actionRepository,
        MandateRepository $mandateRepository,
        PoliticianRepository $politicianRepository,
        ElectionRepository $electionRepository
    )
    {
        $this->paginator = $paginator;
        $this->actionRepository = $actionRepository;
        $this->politicianRepository = $politicianRepository;
        $this->electionRepository = $electionRepository;
        $this->mandateRepository = $mandateRepository;
    }

    /**
     * @Route(
     *     path="/actions/{politicianSlug}/{electionSlug}",
     *     name="actions",
     *     methods={"GET"},
     *     defaults={"politicianSlug"="*", "electionSlug"="*"}
     * )
     */
    public function indexAction(Request $request, string $politicianSlug, string $electionSlug)
    {
        $actionsQuery = $this->actionRepository->createQueryBuilder('a')->orderBy('a.occurredTime', 'DESC');
        $actionsQuery
            ->andWhere('a.published = :published')->setParameter('published', true)
            ->andWhere('a.promiseUpdates IS NOT EMPTY');

        $politician = null;
        if ($politicianSlug !== '*') {
            $politician = $this->politicianRepository->findOneBy(['slug' => $politicianSlug]); /** @var Politician $politician */
            if (!$politician) {
                throw $this->createNotFoundException();
            }
        }

        $election = null;
        if ($electionSlug !== '*') {
            $election = $this->electionRepository->findOneBy(['slug' => $electionSlug]); /** @var Election $election */
            if (!$election) {
                throw $this->createNotFoundException();
            }
        }

        if ($politician && $election) {
            $actionsQuery->innerJoin(
                'a.mandate', 'am', 'WITH',
                'am.politician = :politician AND am.election = :election'
            )->setParameter('politician', $politician)->setParameter('election', $election);
        } elseif ($politician) {
            $actionsQuery->innerJoin(
                'a.mandate', 'am', 'WITH', 'am.politician = :politician'
            )->setParameter('politician', $politician);
        } elseif ($election) {
            $actionsQuery->innerJoin(
                'a.mandate', 'am', 'WITH', 'am.election = :election'
            )->setParameter('election', $election);
        }

        $actions = $this->paginator->paginate(
            $actionsQuery,
            $request->query->getInt('page', 1), // fixme: hardcode
            9 // fixme: hardcode
        );

        return $this->render('app/page/actions.html.twig', [
            'actions' => $actions,
            'politician' => $politician,
            'election' => $election,
        ]);
    }
}