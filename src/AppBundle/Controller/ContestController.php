<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Contest\Competitor;
use AppBundle\Domain\Entity\Contest\Contest;
use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Entity\Contest\Round;
use AppBundle\Domain\Service\Register\GenerateTokenInterface;
use AppBundle\Domain\Service\Register\ValidateCompetitorInterface;
use AppBundle\Domain\Service\Register\ValidationResults;
use AppBundle\Entity\Competitor as CompetitorEntity;
use AppBundle\Entity\Contest as ContestEntity;
use AppBundle\Entity\Game as GameEntity;
use AppBundle\Form\RegisterCompetitor\CompetitorEntity as CompetitorFormEntity;
use AppBundle\Form\RegisterCompetitor\CompetitorForm;
use AppBundle\Repository\CompetitorRepository;
use AppBundle\Repository\ContestRepository;
use AppBundle\Repository\GameRepository;
use AppBundle\Repository\MatchRepository;
use AppBundle\Repository\RoundRepository;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contest controller
 *
 * @package AppBundle\Controller
 * @Route("/contest")
 */
class ContestController extends Controller
{
    /**
     * Page to show all the content of the contest
     *
     * @Route("/{uuid}/view", name="contest_view",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function viewAction(string $uuid) : Response
    {
        /** @var ContestRepository $contestRepo */
        $contestRepo = $this->getContestDoctrineRepository();

        /** @var CompetitorRepository $competitorRepo */
        $competitorRepo = $this->getCompetitorDoctrineRepository();

        /** @var RoundRepository $roundRepo */
        $roundRepo = $this->getRoundDoctrineRepository();

        /** @var MatchRepository $matchRepo */
        $matchRepo = $this->getMatchDoctrineRepository();

        /** @var GameRepository $gameRepo */
        $gameRepo = $this->getGameDoctrineRepository();

        /** @var ContestEntity $contestEntity */
        $contestEntity = $contestRepo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (null === $contestEntity) {
            throw new NotFoundHttpException();
        }

        /** @var Contest $contest */
        $contest = $contestEntity->toDomainEntity();

        /** @var CompetitorEntity[] $competitorEntities */
        $competitorEntities = $competitorRepo->findBy([ 'contestUuid' => $contest->uuid() ]);

        /** @var Competitor[] $competitors */
        $competitors = [];
        foreach ($competitorEntities as $competitorEntity) {
            $competitors[] = $competitorEntity->toDomainEntity();
        }
        $contest->setCountCompetitors(count($competitors));

        /** @var Round[] $rounds */
        $rounds = $roundRepo->readRounds($contest->uuid());

        /** @var Match[] $matchs */
        $matches = [];
        foreach ($rounds as $round) {
            $matches = array_merge($matches, $matchRepo->readMatches($round->uuid()));
        }

        foreach ($matches as $match) {
            /** @var GameEntity $game */
            $game = $gameRepo->findOneBy([ 'matchUuid' => $match->uuid() ]);
            if (null !== $game) {
                $match->setGame($game->toDomainEntity());
            }
        }

        return $this->render('contest/view.html.twig', [
            'contest'     => $contest,
            'competitors' => $competitors,
            'rounds'      => $rounds,
            'matches'     => $matches
        ]);
    }

    /**
     * Page to register a competitor to a contest
     *
     * @Route("/register", name="contest_register")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function registerAction(Request $request) : Response
    {
        // Create competitor data entity
        $formEntity = new CompetitorFormEntity();

        // Create the competitor data form
        $form = $this->createForm(CompetitorForm::class, $formEntity, [
            'action' => $this->generateUrl('contest_register')
        ]);

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $competitor = $formEntity->toDomainEntity();
            $contest = $formEntity->getContest()->toDomainEntity();

            $competitorCounts = $this->getCompetitorDoctrineRepository()
                ->countPerContest([ $contest ]);

            foreach ($competitorCounts as $competitorCount) {
                if ($competitorCount['contestUuid'] == $contest->uuid()) {
                    $contest->setCountCompetitors($competitorCount['competitorCount']);
                }
            }

            $maxCompetitors = $contest->maxCompetitors();
            if (null !== $maxCompetitors && $contest->countCompetitors() >= $maxCompetitors) {
                $form->addError(new FormError($this->get('translator')->trans('app.error-messages.max-competitors', [
                    '%name%' => $contest->name()
                ])));
            } else {

                /** @var GenerateTokenInterface $generator */
                $generator = $this->get('app.contest.register.generate_token');
                $generator->addToken($competitor);

                /** @var ValidateCompetitorInterface $validator */
                $validator = $this->get('app.contest.register.validate_competitor_ex');
                $result = $validator->validate($competitor, $contest);

                if (ValidationResults::STATUS_ERROR == $result->status()) {
                    foreach ($result->result() as $field => $messages) {
                        try {
                            $control = $form->get($field);
                        } catch (\OutOfBoundsException $exc) {
                            $control = $form;
                        }
                        foreach ($messages as $message) {
                            $error = new FormError($message);
                            $control->addError($error);
                        }
                    }
                } else {
                    $em = $this->getDoctrine()->getManager();
                    $entities = $this->getCompetitorDoctrineRepository()
                        ->findBy([
                            'contestUuid' => $competitor->contest(),
                            'email' => $competitor->email()
                        ]);

                    /** @var CompetitorEntity $entity */
                    foreach ($entities as $entity) {
                        $em->remove($entity);
                    }

                    $entity = new CompetitorEntity($competitor);
                    $em->persist($entity);
                    $em->flush();

                    // TODO: Send email to the user

                    return $this->redirectToRoute('contest_registered', [
                        'uuid' => $competitor->contest()
                    ]);
                }
            }
        }

        return $this->render('contest/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Registered to a contest page
     *
     * @Route("/{uuid}/registered", name="contest_registered",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function registeredAction(string $uuid) : Response
    {
        /** @var ContestRepository $contestRepo */
        $contestRepo = $this->getContestDoctrineRepository();

        /** @var ContestEntity $contestEntity */
        $contestEntity = $contestRepo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (null === $contestEntity) {
            throw new NotFoundHttpException();
        }

        return $this->render('contest/registered.html.twig', [
            'contest' => $contestEntity->toDomainEntity()
        ]);
    }

    /**
     * Validate a competitor using the sent token
     *
     * @Route("/validate/{token}", name="contest_validate",
     *     requirements={"token": "[0-9a-f]{64}"})
     *
     * @param string $token
     * @return Response
     * @throws \Exception
     */
    public function validateAction(string $token) : Response
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var CompetitorEntity $competitorEntity */
        $competitorEntity = $this->getCompetitorDoctrineRepository()->findOneBy(array(
            'token' => $token
        ));

        if (null === $competitorEntity) {
            throw new NotFoundHttpException();
        }

        /** @var Competitor $competitor */
        $competitor = $competitorEntity->toDomainEntity();

        /** @var ContestEntity $contestEntity */
        $contestEntity = $this->getContestDoctrineRepository()->findOneBy([
            'uuid' => $competitor->contest()
        ]);

        /** @var Contest $contest */
        $contest = $contestEntity->toDomainEntity();

        $validated = false;
        $now = new \DateTime();
        if ($now >= $contest->registrationStartDate() && $now <= $contest->registrationEndDate()) {
            $competitor->setValidated();
            $competitorEntity->fromDomainEntity($competitor);
            $em->persist($competitorEntity);
            $em->flush();
            $validated = true;
        }

        return $this->render('contest/validate.html.twig', [
            'contest'    => $contest,
            'competitor' => $competitor,
            'validated'  => $validated
        ]);
    }

    /**
     * Return the repository object to Contest entity
     *
     * @return ContestRepository
     */
    private function getContestDoctrineRepository() : ContestRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Contest');
    }

    /**
     * Return the repository object to Competitor entity
     *
     * @return CompetitorRepository
     */
    private function getCompetitorDoctrineRepository() : CompetitorRepository
    {
        return $this->getDoctrine()->getRepository('AppBundle:Competitor');
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
