<?php

namespace AppBundle\Controller;


use AppBundle\Domain\Entity\Contest\Competitor;
use AppBundle\Domain\Entity\Contest\Contest;
use AppBundle\Domain\Entity\Contest\Participant;
use AppBundle\Domain\Entity\Contest\Round;
use AppBundle\Entity\Competitor as CompetitorEntity;
use AppBundle\Entity\Contest as ContestEntity;
use AppBundle\Entity\Game as GameEntity;
use AppBundle\Entity\Match as MatchEntity;
use AppBundle\Entity\Round as RoundEntity;
use AppBundle\Form\CreateContest\ContestEntity as ContestFormEntity;
use AppBundle\Form\CreateContest\ContestForm;
use AppBundle\Form\CreateRound\RoundEntity as RoundFormEntity;
use AppBundle\Form\CreateRound\RoundForm;
use AppBundle\Form\RegisterCompetitor\CompetitorEntity as CompetitorFormEntity;
use AppBundle\Form\RegisterCompetitor\CompetitorForm;
use AppBundle\Repository\CompetitorRepository;
use AppBundle\Repository\ContestRepository;
use AppBundle\Repository\GameRepository;
use AppBundle\Repository\MatchRepository;
use AppBundle\Repository\RoundRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
        /** @var RoundEntity $roundEntity */
        $roundEntity = $this->getRoundDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$roundEntity) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();

        /** @var MatchEntity[] $matchEntities */
        $matchEntities = $this->getMatchDoctrineRepository()->findBy([
            'roundUuid' => $roundEntity->getUuid()
        ]);

        /** @var MatchEntity $matchEntity */
        foreach ($matchEntities as $matchEntity) {
            /** @var GameEntity $gameEntity */
            $gameEntity = $this->getGameDoctrineRepository()->findOneBy([
                'uuid' => $matchEntity->getGameUuid()
            ]);
            $em->remove($gameEntity);
            $em->remove($matchEntity);
        }

        $em->remove($roundEntity);
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
        return $this->getDoctrine()->getRepository('AppBundle:Round');
    }

    /**
     * Return the repository object to Match entity
     *
     * @return MatchRepository
     */
    private function getMatchDoctrineRepository() : MatchRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Match');
    }

    /**
     * Return the repository object to Game entity
     *
     * @return GameRepository
     */
    private function getGameDoctrineRepository() : GameRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Game');
    }
}
