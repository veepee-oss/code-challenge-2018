<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Contest\Contest;
use AppBundle\Entity\Contest as ContestEntity;
use AppBundle\Form\CreateContest\ContestEntity as ContestFormEntity;
use AppBundle\Form\CreateContest\ContestForm;
use AppBundle\Repository\ContestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        // Get query params
        $limit = $request->query->get('limit', 200);
        $start = $request->query->get('start', 0);

        /** @var ContestRepository $repo */
        $repo = $this->getContestDoctrineRepository();

        /** @var ContestEntity[] $contestEntities */
        $contestEntities = $repo->findBy([], [
            'id' => 'desc'
        ], $limit, $start);

        $total = $this->getContestDoctrineRepository()->count([]);

        /** @var Contest[] $contests */
        $contests = [];
        foreach ($contestEntities as $contestEntity) {
            $contests[] = $contestEntity->toDomainEntity();
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

            return $this->redirectToRoute('admin_contest_view', ['uuid' => $contestEntity->getUuid()]);
        }

        return $this->render('admin/contest/create.html.twig', array(
            'form' => $form->createView(),
        ));
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
        /** @var ContestRepository $repo */
        $repo = $this->getContestDoctrineRepository();

        /** @var ContestEntity $contestEntity */
        $contestEntity = $repo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$contestEntity) {
            throw new NotFoundHttpException();
        }

        return $this->render('admin/contest/view.html.twig', array(
            'contest' => $contestEntity->toDomainEntity(),
        ));
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
        /** @var ContestRepository $repo */
        $repo = $this->getContestDoctrineRepository();

        /** @var ContestEntity $contestEntity */
        $contestEntity = $repo->findOneBy(array(
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

            return $this->redirectToRoute('admin_contest_view', ['uuid' => $contestEntity->getUuid()]);
        }

        return $this->render('admin/contest/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Remove contest
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
        /** @var ContestRepository $repo */
        $repo = $this->getContestDoctrineRepository();

        /** @var ContestEntity $contestEntity */
        $contestEntity = $repo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$contestEntity) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($contestEntity);
        $em->flush();

        return new Response('', 204);
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
}
