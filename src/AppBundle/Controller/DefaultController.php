<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Service\Register\GenerateTokenInterface;
use AppBundle\Domain\Service\Register\ValidateCompetitorInterface;
use AppBundle\Domain\Service\Register\ValidationResults;
use AppBundle\Entity\Competitor as Entity;
use AppBundle\Form\RegisterCompetitor\CompetitorEntity;
use AppBundle\Form\RegisterCompetitor\CompetitorForm;
use AppBundle\Repository\ContestRepository;
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
        $contests = $repo->findActiveContests();

        return $this->render('default/index.html.twig', [
            'activeContests' => count($contests) > 0
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
     * Page to register to a contest
     *
     * @Route("/register", name="register")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function registerAction(Request $request) : Response
    {
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

            /** @var GenerateTokenInterface $generator */
            $generator = $this->get('app.contest.register.generate_token');
            $generator->addToken($competitor);

            /** @var ValidateCompetitorInterface $validator */
            $validator = $this->get('app.contest.register.validate_competitor_ex');

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
                $em = $this->getDoctrine()->getManager();
                $entities = $em
                    ->getRepository('AppBundle:Competitor')
                    ->findBy([
                        'contestUuid' => $competitor->contest(),
                        'email'       => $competitor->email()
                    ]);

                /** @var Entity $entity */
                foreach ($entities as $entity) {
                    $em->remove($entity);
                }

                $entity = new Entity($competitor);
                $em->persist($entity);
                $em->flush();

                // TODO: Send email to the user

                return $this->redirectToRoute('registered');
            }
        }

        return $this->render('default/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Registered to a contest page
     *
     * @Route("/registered", name="registered")
     * @return Response
     * @throws \Exception
     */
    public function registeredAction() : Response
    {
        return $this->render('default/registered.html.twig');
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
