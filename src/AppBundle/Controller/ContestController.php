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
        $formEntity = new CompetitorEntity();

        // Create the competitor data form
        $form = $this->createForm(CompetitorForm::class, $formEntity, [
            'action' => $this->generateUrl('contest_register')
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

                return $this->redirectToRoute('contest_registered', [
                    'uuid' => $competitor->contest()
                ]);
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
        /** @var ContestRepository $repo */
        $repo = $this->getDoctrine()->getRepository('AppBundle:Contest');

        /** @var \AppBundle\Entity\Contest $entity */
        $entity = $repo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        return $this->render('contest/registered.html.twig', [
            'contest' => $entity->toDomainEntity()
        ]);
    }
}
