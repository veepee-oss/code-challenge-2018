<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\GameEngine\ConsumerDaemonManagerInterface;
use AppBundle\Domain\Service\GameEngine\GameDaemonManagerInterface;
use AppBundle\Domain\Service\GameEngine\GameEngine;
use AppBundle\Domain\Service\MazeBuilder\MazeBuilderInterface;
use AppBundle\Domain\Service\MovePlayer\ValidatePlayerServiceInterface;
use AppBundle\Form\CreateGame\GameEntity;
use AppBundle\Form\CreateGame\GameForm;
use AppBundle\Form\CreateGame\PlayerEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Game and game admin controller
 *
 * @package AppBundle\Controller
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * Create new game (step 1)
     *
     * @Route("/create", name="game_create")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request) : Response
    {
        $now = new \DateTime('now');
        $dateFormat = $this->getParameter('default_time_format');
        $startTime = \DateTime::createFromFormat($dateFormat, $this->getParameter('game_start_time'));
        $endTime = \DateTime::createFromFormat($dateFormat, $this->getParameter('game_end_time'));
        $freeTime = \DateTime::createFromFormat($dateFormat, $this->getParameter('game_free_time'));
        $accessGranted = $this->isGranted('ROLE_GUEST');

        if (!$accessGranted
            && ($now <= $startTime
            || ($now >= $endTime
            && $now <= $freeTime))) {
            return $this->render('game/gameOver.html.twig', array(
                'now' => $now,
                'start_time' => $startTime,
                'end_time' => $endTime
            ));
        }

        // Create game data entity
        $gameEntity = new GameEntity();

        // Create the game data form (step 1)
        $form = $this->createForm(GameForm::class, $gameEntity, array(
            'action'    => $this->generateUrl('game_create'),
            'form_type' => GameForm::TYPE_GAME_DATA
        ));

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Add players to the game data entity
            for ($i = 0; $i < $gameEntity->getPlayerNum(); ++$i) {
                $gameEntity->addPlayer(new PlayerEntity());
            }

            // Create the players form (step 2)
            $form = $this->createForm(gameForm::class, $gameEntity, array(
                'action'    => $this->generateUrl('game_create_next'),
                'form_type' => GameForm::TYPE_PLAYERS
            ));
        }

        return $this->render('game/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Create new game (step 2)
     *
     * @Route("/create/next", name="game_create_next")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createNextAction(Request $request)
    {
        // Create game data $players entity
        $gameEntity = new GameEntity();

        // Create the players form (step 2)
        $form = $this->createForm(gameForm::class, $gameEntity, array(
            'action'    => $this->generateUrl('game_create_next'),
            'form_type' => GameForm::TYPE_PLAYERS
        ));

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Create the maze of height x width
            /** @var MazeBuilderInterface $mazeBuilder */
            $mazeBuilder = $this->get('app.maze.builder');
            $maze = $mazeBuilder->buildRandomMaze(
                $gameEntity->getHeight(),
                $gameEntity->getWidth()
            );

            /** @var ValidatePlayerServiceInterface $playerValidator */
            $playerValidator = $this->get('app.player.validate.service');

            // Create players
            $errors = false;
            $players = array();
            for ($pos = 0; $pos < $gameEntity->getPlayerNum(); $pos++) {
                $url = $gameEntity->getPlayerAt($pos)->getUrl();
                $player = new Player($url, $maze->createStartPosition());
                try {
                    $playerValidator->validate($player, null);
                    $players[] = $player;
                } catch (\Exception $exc) {
                    $form->get('players')->addError(new FormError($exc->getMessage()));
                    $errors = true;
                }
            }

            // Create game if no errors
            if (!$errors) {

                /** @var GameEngine $engine */
                $engine = $this->get('app.game.engine');
                $game = $engine->create(
                    $maze,
                    $players,
                    $gameEntity->getGhostRate(),
                    $gameEntity->getMinGhosts(),
                    $gameEntity->getLimit(),
                    $gameEntity->getName()
                );

                // Save game data in the database
                $entity = new \AppBundle\Entity\Game($game);
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                // Show the game
                return $this->redirectToRoute(
                    'game_view',
                    array(
                        'uuid' => $game->uuid()
                    )
                );
            }
        }

        return $this->render('game/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * View Game (Maze & Panels)
     *
     * @Route("/{uuid}/view", name="game_view",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function viewAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $renderer = $this->get('app.maze.renderer');
        $game = $entity->toDomainEntity();
        $maze = $renderer->render($game);

        return $this->render(':game:view.html.twig', array(
            'game' => $game,
            'maze' => $maze
        ));
    }

    /**
     * View Game Maze
     *
     * @Route("/{uuid}/view/maze", name="game_view_maze",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function viewMazeAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $renderer = $this->get('app.maze.renderer');
        $game = $entity->toDomainEntity();
        $maze = $renderer->render($game);

        return $this->render(':game:maze.html.twig', array(
            'game' => $game,
            'maze' => $maze
        ));
    }

    /**
     * View Game Panels
     *
     * @Route("/{uuid}/view/panels", name="game_view_panels",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function viewPanelsAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $renderer = $this->get('app.maze.renderer');
        $game = $entity->toDomainEntity();
        $maze = $renderer->render($game);

        return $this->render(':game:panels.html.twig', array(
            'game' => $game,
            'maze' => $maze
        ));
    }

    /**
     * View Game Details
     *
     * @Route("/{uuid}/view/details", name="game_view_details",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function viewDetailsAction($uuid)
    {
        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
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
     * View only maze
     *
     * @Route("/{uuid}/refresh", name="game_refresh",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return JsonResponse
     * @throws \Exception
     */
    public function refreshAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $renderer = $this->get('app.maze.renderer');
        $game = $entity->toDomainEntity();
        $maze = $renderer->render($game);

        $mazeHtml = $this->renderView(':game:viewMaze.html.twig', array(
            'game' => $game,
            'maze' => $maze
        ));

        $panelsHtml = $this->renderView(':game:viewPanels.html.twig', array(
            'game' => $game,
            'maze' => $maze
        ));

        $data = array(
            'mazeHtml'   => $mazeHtml,
            'panelsHtml' => $panelsHtml,
            'playing'    => $game->playing(),
            'finished'   => $game->finished()
        );

        return new JsonResponse($data);
    }

    /**
     * Start a game
     *
     * @Route("/{uuid}/start", name="game_start",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function startAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $game = $entity->toDomainEntity();
        $game->startPlaying();

        $entity->fromDomainEntity($game);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response();
    }

    /**
     * Stop a game
     *
     * @Route("/{uuid}/stop", name="game_stop",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function stopAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $game = $entity->toDomainEntity();
        $game->stopPlaying();

        $entity->fromDomainEntity($game);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response();
    }

    /**
     * Reset a game
     *
     * @Route("/{uuid}/reset", name="game_reset",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function resetAction($uuid)
    {
        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $game = $entity->toDomainEntity();

        $logger = $this->get('app.logger');
        $logger->clear($uuid);

        /** @var GameEngine $engine */
        $engine = $this->get('app.game.engine');
        $engine->reset($game);

        $entity->fromDomainEntity($game);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response();
    }

    /**
     * remove the game
     *
     * @Route("/{uuid}/remove", name="game_remove",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     * @Method("POST")
     *
     * @param string $uuid
     * @return Response
     * @throws \Exception
     */
    public function removeAction($uuid)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $em->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $logger = $this->get('app.logger');
        $logger->clear($uuid);

        $em->remove($entity);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Download the logs of the game
     *
     * @Route("/{guuid}/download", name="game_download",
     *     requirements={"guuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @Route("/{guuid}/player/{puuid}/download", name="player_download",
     *     requirements={
     *         "guuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}",
     *         "puuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"
     *     })
     *
     * @param string $guuid Game Uuid
     * @param string $puuid Player Uuid
     * @return JsonResponse
     * @throws \Exception
     */
    public function downloadLogAction($guuid, $puuid = null)
    {
        $logger = $this->get('app.logger');
        $logs = $logger->read($guuid, $puuid);

        $headers = array();
        if (!$this->get('kernel')->isDebug()) {
            $filename = $guuid;
            if ($puuid) {
                $filename .= '.' . $puuid;
            }
            $headers = array(
                'Content-Disposition' => 'attachment; filename=\'' . $filename . '.log'
            );
        }

        return new JsonResponse($logs, 200, $headers);
    }

    /**
     * Checks if the daemon is running in the background
     *
     * @return void
     */
    private function checkDaemon()
    {
        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->get('app.game.daemon');
        $gameDaemonManager->start();
    }

    /**
     * Admin game view
     *
     * @Route("/admin", name="admin_view")
     * @return Response
     * @throws \Exception
     */
    public function adminAction()
    {
        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->get('app.game.daemon');
        $processId = $gameDaemonManager->getProcessId();

        /** @var ConsumerDaemonManagerInterface $gameDaemonManager */
        $consumerDaemonManager = $this->get('app.consumer.daemon');
        $consumerIds = $consumerDaemonManager->getProcessIds();

        /** @var \AppBundle\Entity\Game[] $entities */
        $entities = $this->getDoctrine()->getRepository('AppBundle:Game')->findBy(
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
     * Start the game daemon
     *
     * @Route("/admin/daemon/start", name="admin_daemon_start")
     * @return Response
     */
    public function startDaemonAction()
    {
        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->get('app.game.daemon');
        $gameDaemonManager->start();

        return $this->redirectToRoute('admin_view');
    }

    /**
     * Stop the game daemon
     *
     * @Route("/admin/daemon/stop", name="admin_daemon_stop")
     * @return Response
     */
    public function stopDaemonAction()
    {
        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->get('app.game.daemon');
        $gameDaemonManager->stop();

        return $this->redirectToRoute('admin_view');
    }

    /**
     * Restart the game daemon
     *
     * @Route("/admin/daemon/restart", name="admin_daemon_restart")
     * @return Response
     */
    public function restartDaemonAction()
    {
        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->get('app.game.daemon');
        $gameDaemonManager->start(true);

        return $this->redirectToRoute('admin_view');
    }

    /**
     * Start a consumer daemon
     *
     * @Route("/admin/consumer/start", name="admin_consumer_start")
     * @return Response
     */
    public function startConsumerAction()
    {
        /** @var ConsumerDaemonManagerInterface $consumerDaemonManager */
        $consumerDaemonManager = $this->get('app.consumer.daemon');
        $consumerDaemonManager->start();

        return $this->redirectToRoute('admin_view');
    }

    /**
     * Stop all the consumer daemons
     *
     * @Route("/admin/consumer/stop", name="admin_consumer_stop")
     * @return Response
     */
    public function stopConsumerAction()
    {
        /** @var ConsumerDaemonManagerInterface $consumerDaemonManager */
        $consumerDaemonManager = $this->get('app.consumer.daemon');
        $consumerDaemonManager->stop();

        return $this->redirectToRoute('admin_view');
    }
}
