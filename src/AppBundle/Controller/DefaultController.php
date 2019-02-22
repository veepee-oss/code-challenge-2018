<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contest as ContestEntity;
use AppBundle\Repository\ContestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller
 *
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Default page
     *
     * @Route("/", name="homepage")
     * @return Response
     * @throws \Exception
     */
    public function indexAction() : Response
    {
        /** @var ContestRepository $repo */
        $repo = $this->getDoctrine()->getRepository('AppBundle:Contest');

        /** @var ContestEntity $openedContests */
        $openedContests = $repo->findOpenedContests();

        /** @var ContestEntity $openedContests */
        $activeContests = $repo->findActiveContests();

        return $this->render('default/index.html.twig', [
            'openedContests' => $openedContests,
            'activeContests' => $activeContests
        ]);
    }

    /**
     * Rules static page
     *
     * @Route("/rules", name="rules")
     * @return Response
     */
    public function rulesAction() : Response
    {
        return $this->render('default/rules.html.twig');
    }

    /**
     * Credits static page
     *
     * @Route("/credits", name="credits")
     * @return Response
     */
    public function creditsAction() : Response
    {
        return $this->render('default/credits.html.twig');
    }

    /**
     * Login page
     *
     * @Route("/login", name="login")
     * @return Response
     */
    public function loginAction() : Response
    {
        return $this->redirectToRoute('homepage');
    }
}
