<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\GameEngine\GameDaemonManagerInterface;
use AppBundle\Domain\Service\GameEngine\GameEngine;
use AppBundle\Domain\Service\MazeBuilder\MazeBuilderInterface;
use AppBundle\Domain\Service\MazeRender\MazeRenderInterface;
use AppBundle\Domain\Service\MovePlayer\ValidatePlayerServiceInterface;
use AppBundle\Form\CreateGame\GameEntity;
use AppBundle\Form\CreateGame\GameForm;
use AppBundle\Form\CreateGame\PlayerEntity;
use AppBundle\Repository\GameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Game controller
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
        $form = $this->createNewGameForm(
            $gameEntity,
            $this->generateUrl('game_create'),
            GameForm::TYPE_GAME_DATA
        );

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Add players to the game data entity
            for ($i = 0; $i < $gameEntity->getPlayerNum(); ++$i) {
                $gameEntity->addPlayer(new PlayerEntity());
            }

            // Create the players form (step 2)
            $form = $this->createNewGameForm(
                $gameEntity,
                $this->generateUrl('game_create_next'),
                GameForm::TYPE_PLAYERS
            );
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
        $form = $this->createNewGameForm(
            $gameEntity,
            $this->generateUrl('game_create_next'),
            GameForm::TYPE_PLAYERS
        );

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
        $entity = $this->getGameDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $renderer = $this->getMazeRendererService();
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
        $entity = $this->getGameDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $renderer = $this->getMazeRendererService();
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
        $entity = $this->getGameDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $renderer = $this->getMazeRendererService();
        $game = $entity->toDomainEntity();
        $maze = $renderer->render($game);

        return $this->render(':game:panels.html.twig', array(
            'game' => $game,
            'maze' => $maze
        ));
    }

    /**
     * Get the refreshed maze view
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
        $entity = $this->getGameDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $renderer = $this->getMazeRendererService();
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
        $entity = $this->getGameDoctrineRepository()->findOneBy(array(
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
        $entity = $this->getGameDoctrineRepository()->findOneBy(array(
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
        $entity = $this->getGameDoctrineRepository()->findOneBy(array(
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
     * Creates the form to ask for new game params
     *
     * @param GameEntity $gameEntity
     * @param string $action
     * @param string $formType
     * @return FormInterface
     */
    private function createNewGameForm(GameEntity $gameEntity, string $action, string $formType) : FormInterface
    {
        return $this->createForm(GameForm::class, $gameEntity, array(
            'action'    => $action,
            'form_type' => $formType
        ));
    }

    /**
     * Checks if the daemon is running in the background
     *
     * @return void
     */
    private function checkDaemon() : void
    {
        /** @var GameDaemonManagerInterface $gameDaemonManager */
        $gameDaemonManager = $this->get('app.game.daemon');
        $gameDaemonManager->start();
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
     * Get the object to render de maze
     *
     * @return MazeRenderInterface
     */
    private function getMazeRendererService(): MazeRenderInterface
    {
        return $this->get('app.maze.renderer');
    }
}
