<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contest;
use AppBundle\Form\CreateContest\ContestEntity;
use AppBundle\Form\CreateContest\ContestForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contest controller
 *
 * @package AppBundle\Controller
 * @Route("/admin/contest")
 */
class ContestController extends Controller
{
    /**
     * Create new contest
     *
     * @Route("/create", name="contest_create")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createAction(Request $request) : Response
    {
        // Create contest data entity
        $contestEntity = new ContestEntity();

        // Create the contest data form
        $form = $this->createForm(ContestForm::class, $contestEntity, [
            'action' => $this->generateUrl('contest_create')
        ]);

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity = new Contest($contestEntity->toDomainEntity());

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            // TODO redirect to the contest page
            return $this->redirectToRoute('admin_view');
        }

        return $this->render('contest/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
