<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Service\Register\ValidateCompetitorInterface;
use AppBundle\Domain\Service\Register\ValidationResults;
use AppBundle\Entity\Competitor as Entity;
use AppBundle\Form\RegisterCompetitor\CompetitorEntity;
use AppBundle\Form\RegisterCompetitor\CompetitorForm;
use AppBundle\Repository\ContestRepository;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function registerAction(Request $request) : Response
    {
        $this->getLogger()->info('DefaultController::registerAction()');

        // Create competitor data entity
        $formEntity = new CompetitorEntity();

        // Create the competitor data form
        $form = $this->createForm(CompetitorForm::class, $formEntity, [
            'action' => $this->generateUrl('register')
        ]);

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $competitor = $formEntity->toDomainEntity();
            $contest = $formEntity->getContest()->toDomainEntity();

            /** @var ValidateCompetitorInterface $validator */
            $validator = $this->get('app.contest.validate_competitor_ex');

            // Validate the data entered
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
                $entity = new Entity($competitor);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('default/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function loginAction() : Response
    {
        $this->getLogger()->info('DefaultController::loginAction()');
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
