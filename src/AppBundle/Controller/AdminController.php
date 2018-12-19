<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Service\GameEngine\ConsumerDaemonManagerInterface;
use AppBundle\Domain\Service\GameEngine\GameDaemonManagerInterface;
use AppBundle\Repository\GameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Game admin controller
 *
 * @package AppBundle\Controller
 * @Route("/admin/game")
 */
class AdminController extends Controller
{
    /**
     * Admin game view
     *
     * @Route("/", name="admin_view")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function adminAction(Request $request)
    {
        // Get query params
        $limit = $request->query->get('limit', 200);
        $start = $request->query->get('start', 0);

        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->getGameDaemonManagerService();
        $processId = $gameDaemonManager->getProcessId();

        /** @var ConsumerDaemonManagerInterface $consumerDaemonManager */
        $consumerDaemonManager = $this->getConsumerDaemonManager();
        $consumerIds = $consumerDaemonManager->getProcessIds();

        /** @var \AppBundle\Entity\Game[] $entities */
        $entities = $this->getGameDoctrineRepository()->findBy([], [
            'id' => 'desc'
        ], $limit, $start);

        $total = $this->getGameDoctrineRepository()->count([]);

        /** @var Game[] $allGames */
        $allGames = [];
        foreach ($entities as $entity) {
            $allGames[] = $entity->toDomainEntity();
        }

        // Sort by status and last updated date
        usort($allGames, function (Game $game1, Game $game2) {
            $res = $game1->status() <=> $game2->status();
            if (0 === $res) {
                $res = $game2->lastUpdatedAt()->getTimestamp() <=> $game1->lastUpdatedAt()->getTimestamp();
            }
            return $res;
        });

        // Split by status
        $playingGames = [];
        $pausedGames = [];
        $notStartedGames = [];
        $finishedGames = [];
        foreach ($allGames as $game) {
            if ($game->playing()) {
                $playingGames[] = $game;
            } elseif ($game->paused()) {
                $pausedGames[] = $game;
            } elseif (!$game->finished()) {
                $notStartedGames[] = $game;
            } else {
                $finishedGames[] = $game;
            }
        }

        return $this->render('game/admin.html.twig', array(
            'processId'       => $processId,
            'consumerIds'     => $consumerIds,
            'playingGames'    => $playingGames,
            'pausedGames'     => $pausedGames,
            'notStartedGames' => $notStartedGames,
            'finishedGames'   => $finishedGames,
            'start'           => $start,
            'limit'           => $limit,
            'count'           => count($allGames),
            'total'           => $total
        ));
    }

    /**
     * View Game Details
     *
     * @Route("/{uuid}/details", name="admin_game_details",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function viewDetailsAction($uuid)
    {
        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getGameDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $game = $entity->toDomainEntity();

        return $this->render(':game:details.html.twig', array(
            'game' => $game
        ));
    }

    /**
     * remove a game
     *
     * @Method("POST")
     * @Route("/{uuid}/remove", name="admin_game_remove",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function removeAction($uuid)
    {
        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getGameDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $logger = $this->get('app.logger');
        $logger->clear($uuid);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Start the game daemon
     *
     * @Route("/daemon/start", name="admin_daemon_start")
     * @return Response
     */
    public function startDaemonAction()
    {
        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->getGameDaemonManagerService();
        $gameDaemonManager->start();

        return $this->redirectToAdminView();
    }

    /**
     * Stop the game daemon
     *
     * @Route("/daemon/stop", name="admin_daemon_stop")
     * @return Response
     */
    public function stopDaemonAction()
    {
        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->getGameDaemonManagerService();
        $gameDaemonManager->stop();

        return $this->redirectToAdminView();
    }

    /**
     * Restart the game daemon
     *
     * @Route("/daemon/restart", name="admin_daemon_restart")
     * @return Response
     */
    public function restartDaemonAction()
    {
        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->getGameDaemonManagerService();
        $gameDaemonManager->start(true);

        return $this->redirectToAdminView();
    }

    /**
     * Start a consumer daemon
     *
     * @Route("/consumer/start", name="admin_consumer_start")
     * @return Response
     */
    public function startConsumerAction()
    {
        /** @var ConsumerDaemonManagerInterface $consumerDaemonManager */
        $consumerDaemonManager = $this->getConsumerDaemonManager();
        $consumerDaemonManager->start();

        return $this->redirectToAdminView();
    }

    /**
     * Stop all the consumer daemons
     *
     * @Route("/consumer/stop", name="admin_consumer_stop")
     * @return Response
     */
    public function stopConsumerAction()
    {
        /** @var ConsumerDaemonManagerInterface $consumerDaemonManager */
        $consumerDaemonManager = $this->getConsumerDaemonManager();
        $consumerDaemonManager->stop();

        return $this->redirectToAdminView();
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

    /**
     * Get the game daemon manager service
     *
     * @return GameDaemonManagerInterface
     */
    private function getGameDaemonManagerService() : GameDaemonManagerInterface
    {
        return $this->get('app.game.daemon');
    }

    /**
     * Get the consumer daemon manager service
     *
     * @return ConsumerDaemonManagerInterface
     */
    private function getConsumerDaemonManager() : ConsumerDaemonManagerInterface
    {
        return $this->get('app.consumer.daemon');
    }

    /**
     * Redirect the action to show the admin view
     *
     * @return RedirectResponse
     */
    private function redirectToAdminView() : RedirectResponse
    {
        return $this->redirectToRoute('admin_view');
    }
}
