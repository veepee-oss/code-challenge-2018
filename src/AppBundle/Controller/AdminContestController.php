<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Contest\Competitor;
use AppBundle\Domain\Entity\Contest\Contest;
use AppBundle\Domain\Entity\Contest\Match;
use AppBundle\Domain\Entity\Contest\Round;
use AppBundle\Domain\Service\Register\GenerateTokenInterface;
use AppBundle\Entity\Competitor as CompetitorEntity;
use AppBundle\Entity\Contest as ContestEntity;
use AppBundle\Entity\Game as GameEntity;
use AppBundle\Entity\Round as RoundEntity;
use AppBundle\Entity\Match as MatchEntity;
use AppBundle\Form\CreateContest\ContestEntity as ContestFormEntity;
use AppBundle\Form\CreateContest\ContestForm;
use AppBundle\Form\CreateRound\RoundEntity as RoundFormEntity;
use AppBundle\Form\CreateRound\RoundForm;
use AppBundle\Form\RegisterCompetitor\CompetitorEntity as CompetitorFormEntity;
use AppBundle\Form\RegisterCompetitor\CompetitorForm;
use AppBundle\Repository\CompetitorRepository;
use AppBundle\Repository\ContestRepository;
use AppBundle\Repository\MatchRepository;
use AppBundle\Repository\RoundRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contest admin controller
 *
 * @package AppBundle\Controller
 * @Route("/admin/contest")
 */
class AdminContestController extends Controller
{
    /**
     * Create new contest
     *
     * @Route("/", name="admin_contest_index")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request) : Response
    {
        $limit = $request->query->get('limit', 200);
        $start = $request->query->get('start', 0);

        /** @var ContestRepository $repo */
        $repo = $this->getContestDoctrineRepository();

        /** @var ContestEntity[] $contestEntities */
        $contestEntities = $repo->findBy([], [
            'id' => 'desc'
        ], $limit, $start);

        $total = $repo->count([]);

        // Get array [ 'contestUuid' => string, 'competitorCount' => int]
        $competitorCounts = $this
            ->getCompetitorDoctrineRepository()
            ->countPerContest($contestEntities);

        /** @var Contest[] $contests */
        $contests = [];

        // Build contest domain entities adding the competitors count
        foreach ($contestEntities as $contestEntity) {
            /** @var Contest $contest */
            $contest = $contestEntity->toDomainEntity();
            $contest->setCountCompetitors(0);
            foreach ($competitorCounts as $competitorCount) {
                if ($competitorCount['contestUuid'] == $contest->uuid()) {
                    $contest->setCountCompetitors($competitorCount['competitorCount']);
                }
            }
            $contests[] = $contest;
        }

        return $this->render('admin/contest/index.html.twig', array(
            'contests'  => $contests,
            'start'     => $start,
            'limit'     => $limit,
            'count'     => count($contests),
            'total'     => $total
        ));
    }

    /**
     * Create new contest
     *
     * @Route("/create", name="admin_contest_create")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createAction(Request $request) : Response
    {
        // Create contest data entity
        $contestFormEntity = new ContestFormEntity();

        // Create the contest data form
        $form = $this->createForm(ContestForm::class, $contestFormEntity, [
            'action' => $this->generateUrl('admin_contest_create'),
            'mode'   => ContestForm::MODE_CREATE
        ]);

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contestEntity = new ContestEntity($contestFormEntity->toDomainEntity());

            $em = $this->getDoctrine()->getManager();
            $em->persist($contestEntity);
            $em->flush();

            return $this->redirectToRoute('admin_contest_view', [
                'uuid' => $contestEntity->getUuid()
            ]);
        }

        return $this->render('admin/contest/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * View contest
     *
     * @Route("/{uuid}", name="admin_contest_view",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function viewAction(string $uuid) : Response
    {
        /** @var ContestEntity $contestEntity */
        $contestEntity = $this->getContestDoctrineRepository()->findOneBy([
            'uuid' => $uuid
        ]);

        if (!$contestEntity) {
            throw new NotFoundHttpException();
        }

        /** @var CompetitorEntity[] $competitorEntities */
        $competitorEntities = $this->getCompetitorDoctrineRepository()->findBy([
            'contestUuid' => $uuid
        ]);

        /** @var RoundEntity[] $roundEntities */
        $roundEntities = $this->getRoundDoctrineRepository()->findBy([
            'contestUuid' => $uuid
        ]);

        /** @var Contest $contest */
        $contest = $contestEntity->toDomainEntity();

        /** @var Competitor[] $competitors */
        $competitors = [];
        foreach ($competitorEntities as $competitorEntity) {
            $competitors[] = $competitorEntity->toDomainEntity();
        }

        /** @var Round[] $rounds */
        $rounds = [];
        foreach ($roundEntities as $roundEntity) {
            $rounds[] = $roundEntity->toDomainEntity();
        }

        $matchCount = [];
        foreach ($rounds as $round) {
            $matchCount[$round->uuid()] = $this->getMatchDoctrineRepository()->count([
                'roundUuid' => $round->uuid()
            ]);
        }

        return $this->render('admin/contest/view.html.twig', [
            'contest'     => $contest,
            'competitors' => $competitors,
            'rounds'      => $rounds,
            'matchCount'  => $matchCount
        ]);
    }

    /**
     * Edit contest
     *
     * @Route("/{uuid}/edit", name="admin_contest_edit",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param Request $request
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function editAction(Request $request, string $uuid) : Response
    {
        /** @var ContestEntity $contestEntity */
        $contestEntity = $this->getContestDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$contestEntity) {
            throw new NotFoundHttpException();
        }

        // Create contest data entity
        $contestFormEntity = new ContestFormEntity($contestEntity->toDomainEntity());

        // Create the contest data form
        $form = $this->createForm(ContestForm::class, $contestFormEntity, [
            'action' => $this->generateUrl('admin_contest_edit', [ 'uuid' => $uuid ]),
            'mode'   => ContestForm::MODE_EDIT
        ]);

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contestEntity->fromDomainEntity($contestFormEntity->toDomainEntity());

            $em = $this->getDoctrine()->getManager();
            $em->persist($contestEntity);
            $em->flush();

            return $this->redirectToRoute('admin_contest_view', [
                'uuid' => $contestEntity->getUuid()
            ]);
        }

        return $this->render('admin/contest/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Remove contest and all its competitors
     *
     * @Route("/{uuid}/remove", name="admin_contest_remove",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function removeAction(string $uuid) : Response
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        /** @var ContestRepository $repo */
        $repo = $this->getContestDoctrineRepository();

        /** @var ContestEntity $contestEntity */
        $contestEntity = $repo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$contestEntity) {
            throw new NotFoundHttpException();
        }

        // Remove the entity and its relations
        $repo->removeContest($contestEntity);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Create competitor for a contest using a form, skipping some validations
     *
     * @Route("/{uuid}/competitor/register", name="admin_competitor_register",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param Request $request
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function competitorRegisterAction(Request $request, string $uuid)
    {
        /** @var ContestEntity $contestEntity */
        $contestEntity = $this->getContestDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$contestEntity) {
            throw new NotFoundHttpException();
        }

        /** @var Contest $contest */
        $contest = $contestEntity->toDomainEntity();

        // Check max competitors
        $competitorCounts = $this->getCompetitorDoctrineRepository()->countPerContest([ $contest ]);
        foreach ($competitorCounts as $competitorCount) {
            if ($competitorCount['contestUuid'] == $contest->uuid()) {
                $contest->setCountCompetitors($competitorCount['competitorCount']);
            }
        }

        $maxCompetitors = $contest->maxCompetitors();
        if (null !== $maxCompetitors && $contest->countCompetitors() >= $maxCompetitors) {
            $this->addFlash("danger", $this->get('translator')->trans('app.error-messages.max-competitors', [
                '%name%' => $contest->name()
            ]));
            return $this->redirectToRoute('admin_contest_view', [ 'uuid' => $contest->uuid() ]);
        }

        // Create competitor data entity
        $formEntity = new CompetitorFormEntity();
        $formEntity->setContest($contestEntity);

        // Create the competitor data form
        $form = $this->createForm(CompetitorForm::class, $formEntity, [
            'action' => $this->generateUrl('admin_competitor_register', [ 'uuid' => $uuid ]),
            'mode'   => 'admin'
        ]);

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Competitor $competitor */
            $competitor = $formEntity->toDomainEntity(null);

            if (!$competitor->validated()) {
                /** @var GenerateTokenInterface $generator */
                $generator = $this->get('app.contest.register.generate_token');
                $generator->addToken($competitor);
            }

            /** @var CompetitorEntity $competitorEntity */
            $competitorEntity = new CompetitorEntity($competitor);

            $em = $this->getDoctrine()->getManager();
            $em->persist($competitorEntity);
            $em->flush();

            return $this->redirectToRoute('admin_contest_view', [
                'uuid' => $uuid
            ]);
        }

        return $this->render('admin/contest/register-competitor.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit a competitor for a contest
     *
     * @Route("/competitor/{uuid}/edit", name="admin_competitor_edit",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param Request $request  the request data
     * @param string $uuid      the UUID of the competitor
     * @return Response
     * @throws \Exception
     */
    public function competitorEditAction(Request $request, string $uuid)
    {
        /** @var CompetitorRepository $competitorRepo */
        $competitorRepo = $this->getCompetitorDoctrineRepository();

        /** @var CompetitorEntity $competitorEntity */
        $competitorEntity = $competitorRepo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$competitorEntity) {
            throw new NotFoundHttpException();
        }

        /** @var Competitor $competitor */
        $competitor = $competitorEntity->toDomainEntity();

        /** @var ContestEntity $contestEntity */
        $contestEntity = $this->getContestDoctrineRepository()->findOneBy(array(
            'uuid' => $competitor->contest()
        ));

        if (!$contestEntity) {
            throw new NotFoundHttpException();
        }

        /** @var Contest $contest */
        $contest = $contestEntity->toDomainEntity();

        // Create competitor data entity
        $formEntity = new CompetitorFormEntity();
        $formEntity->fromDomainEntity($competitor);
        $formEntity->setContest($contestEntity);

        // Create the competitor data form
        $form = $this->createForm(CompetitorForm::class, $formEntity, [
            'action' => $this->generateUrl('admin_competitor_edit', [ 'uuid' => $uuid ]),
            'mode'   => 'admin'
        ]);

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Competitor $competitor */
            $competitor = $formEntity->toDomainEntity($competitor);

            if (!$competitor->validated()) {
                /** @var GenerateTokenInterface $generator */
                $generator = $this->get('app.contest.register.generate_token');
                $generator->addToken($competitor);
            }

            /** @var CompetitorEntity $competitorEntity */
            $competitorEntity->fromDomainEntity($competitor);

            $em = $this->getDoctrine()->getManager();
            $em->persist($competitorEntity);
            $em->flush();

            return $this->redirectToRoute('admin_contest_view', [
                'uuid' => $contest->uuid()
            ]);
        }

        return $this->render('admin/contest/register-competitor.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Remove a single competitor from a contest
     *
     * @Route("/competitor/{uuid}/remove", name="admin_competitor_remove",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function competitorRemoveAction(string $uuid) : Response
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        /** @var CompetitorRepository $repo */
        $repo = $this->getCompetitorDoctrineRepository();

        /** @var CompetitorEntity $competitorEntity */
        $competitorEntity = $repo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$competitorEntity) {
            throw new NotFoundHttpException();
        }

        // Remove the entity and its relations
        $repo->removeCompetitor($competitorEntity);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Create round for a contest
     *
     * @Route("/{uuid}/round/create", name="admin_round_create",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param Request $request
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function roundCreateAction(Request $request, string $uuid)
    {
        /** @var ContestEntity $contestEntity */
        $contestEntity = $this->getContestDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$contestEntity) {
            throw new NotFoundHttpException();
        }

        $rounds = $this->getRoundDoctrineRepository()->findBy([
            'contestUuid' => $uuid
        ]);

        $roundName = $this->get('translator')->trans('app.round-create.texts.round-name', [
            '%num%' => (1 + count($rounds))
        ]);

        // Create round data entity
        $formEntity = new RoundFormEntity($contestEntity->toDomainEntity());
        $formEntity->setName($roundName);

        // Create the round data form
        $form = $this->createForm(RoundForm::class, $formEntity, [
            'action' => $this->generateUrl('admin_round_create', [ 'uuid' => $uuid ]),
            'rounds' => array_map(function (RoundEntity $entity) {
                return $entity->toDomainEntity();
            }, $rounds)
        ]);

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Round $round */
            $round = $formEntity->toDomainEntity();
            $sourceRoundUuid = $formEntity->getSourceRound();

            try {
                $roundManager = $this->get('app.contest.round.manager');
                $roundManager->addParticipants($round, $sourceRoundUuid);

                $matchManager = $this->get('app.contest.match.manager');
                $matches = $matchManager->createMatches($round);

                $em = $this->getDoctrine()->getManager();
                $roundEntity = new RoundEntity($round);
                $em->persist($roundEntity);

                /** @var Match $match */
                foreach ($matches as $match) {
                    $gameEntity = new GameEntity($match->game());
                    $matchEntity = new MatchEntity($match);
                    $em->persist($gameEntity);
                    $em->persist($matchEntity);
                }

                $em->flush();

                return $this->redirectToRoute('admin_contest_view', [
                    'uuid' => $uuid
                ]);
            } catch (\Exception $exc) {
                dump($exc);
                $form->addError(new FormError($exc->getMessage()));
            }
        }

        return $this->render('admin/contest/create-round.html.twig', array(
            'form' => $form->createView(),
        ));
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
}
