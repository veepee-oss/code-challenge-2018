<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\GameEngine\ConsumerDaemonManagerInterface;
use AppBundle\Domain\Service\GameEngine\GameDaemonManagerInterface;
use AppBundle\Repository\GameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Game admin controller
 *
 * @package AppBundle\Controller
 * @Route("/game/admin")
 */
class AdminController extends Controller
{
    /**
     * Admin game view
     *
     * @Route("/", name="admin_view")
     * @return Response
     * @throws \Exception
     */
    public function adminAction()
    {
        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->getGameDaemonManagerService();
        $processId = $gameDaemonManager->getProcessId();

        /** @var ConsumerDaemonManagerInterface $consumerDaemonManager */
        $consumerDaemonManager = $this->getConsumerDaemonManager();
        $consumerIds = $consumerDaemonManager->getProcessIds();

        /** @var \AppBundle\Entity\Game[] $entities */
        $entities = $this->getGameDoctrineRepository()->findBy(
            array(),    // Criteria
            array(      // Order by
                'status'  => 'asc'
            )
        );

        $playingGames = array();
        $pausedGames = array();
        $notStartedGames = array();
        $finishedGames = array();

        foreach ($entities as $entity) {
            $game = $entity->toDomainEntity();
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
            'finishedGames'   => $finishedGames
        ));
    }

    /**
     * View Game Details
     *
     * @Route("/{uuid}/details", name="game_details",
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
     * Get players data
     *
     * @Route("/players", name="admin_get_players")
     * @return JsonResponse
     */
    public function getPlayersAction()
    {
        /** @var \AppBundle\Entity\Game $entities[] */
        $entities = $this->getGameDoctrineRepository()->findAll();

        $result = [];

        /** @var \AppBundle\Entity\Game $entity */
        foreach ($entities as $entity) {
            try {
                /** @var Game $game */
                $game = $entity->toDomainEntity();

                /** @var Player $player */
                foreach ($game->players() as $player) {
                    $found = array_filter($result, function ($item) use ($player) {
                        return $item['name'] == $player->name()
                            && $item['email'] == $player->email()
                            && $item['url'] == $player->url();
                    });
                    if (empty($found)) {
                        $result[] = [
                            'name' => $player->name(),
                            'email' => $player->email(),
                            'url' => $player->url()
                        ];
                    }
                }
            } catch (\Exception $exc) {
                // Do nothing
            }
        }

        return new JsonResponse($result);
    }

    /**
     * remove a game
     *
     * @Method("POST")
     * @Route("/{uuid}/remove", name="game_remove",
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
