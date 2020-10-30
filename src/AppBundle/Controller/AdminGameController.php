<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\GameEngine\ConsumerDaemonManagerInterface;
use AppBundle\Domain\Service\GameEngine\GameDaemonManagerInterface;
use AppBundle\Form\EditPlayer\PlayerEntity;
use AppBundle\Form\EditPlayer\PlayerForm;
use AppBundle\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
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
class AdminGameController extends Controller
{
    /**
     * Admin game view
     *
     * @Route("/", name="admin_index")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function indexAction(Request $request) : Response
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

        return $this->render('admin/game/index.html.twig', array(
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
    public function viewDetailsAction($uuid) : Response
    {
        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getGameDoctrineRepository()->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $game = $entity->toDomainEntity();

        return $this->render('admin/game/details.html.twig', array(
            'game' => $game
        ));
    }

    /**
     * Edit player data
     *
     * @Route("/{guuid}/player/{puuid}/edit", name="admin_player_edit", requirements={
     *    "guuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}",
     *    "puuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"
     * })
     *
     * @param Request $request the request data object
     * @param string  $guuid   the uuid of the game
     * @param string  $puuid   the uuid of the player
     * @return Response
     * @throws \Exception
     */
    public function editPlayerAction(Request $request, string $guuid, string $puuid) : Response
    {
        /** @var \AppBundle\Entity\Game $gameEntity */
        $gameEntity = $this->getGameDoctrineRepository()->findOneBy(array(
            'uuid' => $guuid
        ));

        if (!$gameEntity) {
            throw new NotFoundHttpException();
        }

        /** @var Game $game */
        $game = $gameEntity->toDomainEntity();

        /** @var Player $player */
        $player = $game->playerByUuid($puuid);

        if (!$player) {
            throw new NotFoundHttpException();
        }

        // Create player form data entity
        $playerFormEntity = new PlayerEntity($player);

        // Create the player data form
        $form = $this->createForm(PlayerForm::class, $playerFormEntity, [
            'action' => $this->generateUrl('admin_player_edit', ['guuid' => $guuid, 'puuid' => $puuid])
        ]);

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $player = $playerFormEntity->toDomainEntity($player);
            $game->addPlayer($player);
            $gameEntity->fromDomainEntity($game);

            $em = $this->getDoctrine()->getManager();
            $em->persist($gameEntity);
            $em->flush();

            return $this->redirectToRoute('admin_game_details', [
                'uuid' => $guuid
            ]);
        }

        return $this->render('admin/game/editPlayer.html.twig', array(
            'form' => $form->createView(),
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
    public function removeAction($uuid) : Response
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        /** @var GameRepository $repo */
        $repo = $this->getGameDoctrineRepository();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $repo->findOneBy(array(
            'uuid' => $uuid
        ));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        // Remove the entity and its relations
        $repo->removeGame($entity);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Start the game daemon
     *
     * @Route("/daemon/start", name="admin_daemon_start")
     * @return Response
     */
    public function startDaemonAction() : Response
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
    public function stopDaemonAction() : Response
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
    public function restartDaemonAction() : Response
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
    public function startConsumerAction() : Response
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
    public function stopConsumerAction() : Response
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
        return $this->redirectToRoute('admin_index');
    }
}
