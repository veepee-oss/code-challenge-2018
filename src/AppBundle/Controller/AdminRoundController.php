<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Entity\Contest\Round;
use AppBundle\Domain\Service\Contest\ScoreCalculatorInterface;
use AppBundle\Entity\Round as RoundEntity;
use AppBundle\Repository\MatchRepository;
use AppBundle\Repository\RoundRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Round admin controller
 *
 * @package AppBundle\Controller
 * @Route("/admin/round")
 */
class AdminRoundController extends Controller
{
    /**
     * Validate the scores of a round
     *
     * @Route("/{uuid}/validate", name="admin_round_validate",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function validateAction(string $uuid) : Response
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        /** @var RoundRepository $roundRepo */
        $roundRepo = $this->getRoundDoctrineRepository();

        /** @var RoundEntity $roundEntity */
        $roundEntity = $roundRepo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$roundEntity) {
            throw new NotFoundHttpException();
        }

        /** @var Round $round */
        $round = $roundEntity->toDomainEntity();

        /** @var MatchRepository $matchRepo */
        $matchRepo = $this->getMatchDoctrineRepository();

        /** @var Match[] $matches */
        $matches = $matchRepo->readMatches($round->uuid());

        /** @var ScoreCalculatorInterface $scoreCalculator */
        $scoreCalculator = $this->get('app.contest.score-calculator');
        $scoreCalculator->calculateRoundScore($round, $matches);

        $matchRepo->persistMatches($matches, false);
        $roundEntity->fromDomainEntity($round);
        $em->persist($roundEntity);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Remove a round and all its matches
     *
     * @Route("/{uuid}/remove", name="admin_round_remove",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function removeAction(string $uuid) : Response
    {
        /** @var RoundRepository $repo */
        $repo = $this->getRoundDoctrineRepository();

        /** @var RoundEntity $roundEntity */
        $roundEntity = $repo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$roundEntity) {
            throw new NotFoundHttpException();
        }

        // Remove the entity and its relations
        $repo->removeRound($roundEntity);

        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Return the repository object to Round entity
     *
     * @return RoundRepository
     */
    private function getRoundDoctrineRepository() : RoundRepository
    {
        /** @var RoundRepository $repo */
        $repo = $this->getDoctrine()->getRepository('AppBundle:Round');
        return $repo;
    }

    /**
     * Return the repository object to Match entity
     *
     * @return MatchRepository
     */
    private function getMatchDoctrineRepository() : MatchRepository
    {
        /** @var MatchRepository $repo */
        $repo = $this->getDoctrine()->getRepository('AppBundle:Match');
        return $repo;
    }
}
