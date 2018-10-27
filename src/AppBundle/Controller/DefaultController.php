<?php

namespace AppBundle\Controller;

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
     */
    public function indexAction() : Response
    {
        $this->getLogger()->info('DefaultController::indexAction()');
        return $this->render('default/index.html.twig');
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
     * Get the logger
     *
     * @return LoggerInterface
     */
    private function getLogger() : LoggerInterface
    {
        return $this->get('logger');
    }
}
