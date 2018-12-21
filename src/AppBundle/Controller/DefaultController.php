<?php

namespace AppBundle\Controller;

use AppBundle\Repository\ContestRepository;
use Psr\Log\LoggerInterface;
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
     * @Route("/", name="homepage")
     * @return Response
     * @throws \Exception
     */
    public function indexAction() : Response
    {
        $this->getLogger()->info('DefaultController::indexAction()');

        /** @var ContestRepository $repo */
        $repo = $this->getDoctrine()->getRepository('AppBundle:Contest');
        $contests = $repo->findActiveContests();

        return $this->render('default/index.html.twig', [
            'activeContests' => count($contests) > 0
        ]);
    }

    /**
     * @Route("/rules", name="rules")
     * @return Response
     */
    public function rulesAction() : Response
    {
        $this->getLogger()->info('DefaultController::rulesAction()');
        return $this->render('default/rules.html.twig');
    }

    /**
     * @Route("/credits", name="credits")
     * @return Response
     */
    public function creditsAction() : Response
    {
        $this->getLogger()->info('DefaultController::creditsAction()');
        return $this->render('default/credits.html.twig');
    }

    /**
     * @Route("/register", name="register")
     * @return Response
     */
    public function registerAction()
    {
        $this->getLogger()->info('DefaultController::registerAction()');

        // TODO register
        return new Response();
    }

    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function loginAction() : Response
    {
        return $this->redirectToRoute('homepage');
    }

    /**
     * Get the logger
     *
     * @return LoggerInterface
     */
    private function getLogger() : LoggerInterface
    {
        return $this->get('logger');
    }
}
