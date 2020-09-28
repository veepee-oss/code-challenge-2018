<?php

namespace AppBundle\Service\Contest;

use AppBundle\Domain\Entity\Contest\Participant;
use AppBundle\Domain\Entity\Contest\Round;
use AppBundle\Domain\Service\Contest\RoundManagerInterface;
use AppBundle\Entity\Competitor as CompetitorEntity;
use AppBundle\Entity\Round as RoundEntity;
use AppBundle\Repository\CompetitorRepository;
use AppBundle\Repository\RoundRepository;

/**
 * Service to to find and add the participants to a round
 *
 * @package AppBundle\Service\Contest
 */
class RoundManager implements RoundManagerInterface
{
    /** @var CompetitorRepository */
    private $competitorRepo;

    /** @var RoundRepository */
    private $roundRepo;

    /**
     * RoundManager constructor
     *
     * @param CompetitorRepository $competitorRepo
     * @param RoundRepository $roundRepo
     */
    public function __construct(CompetitorRepository $competitorRepo, RoundRepository $roundRepo)
    {
        $this->competitorRepo = $competitorRepo;
        $this->roundRepo = $roundRepo;
    }

    /**
     * Copies all the participants from the source round (or from the contest)
     *
     * @param Round $round
     * @param string|null $sourceRoundUuid
     * @return Round
     * @throws \Exception
     */
    public function addParticipants(Round $round, ?string $sourceRoundUuid): Round
    {
        if (null !== $sourceRoundUuid) {
            // Add participants from the source round

            /** @var RoundEntity $sourceRoundEntity */
            $sourceRoundEntity = $this->roundRepo->findOneBy([
                'uuid' => $sourceRoundUuid
            ]);

            /** @var Round $sourceRound */
            $sourceRound = $sourceRoundEntity->toDomainEntity();

            /** @var Participant $participant */
            foreach ($sourceRound->participants() as $sourceParticipant) {
                if ($sourceParticipant->classified()) {
                    $participant = new Participant($sourceParticipant->competitor(), null, null);
                    $round->addParticipant($participant);
                }
            }
        } else {
            // Add all the validated participants from the contest

            /** @var CompetitorEntity[] $competitorEntities */
            $competitorEntities = $this->competitorRepo->findBy([
                'contestUuid' => $round->contest()
            ]);

            /** @var CompetitorEntity $competitorEntity */
            foreach ($competitorEntities as $competitorEntity) {
                if ($competitorEntity->isValidated()) {
                    $participant = new Participant($competitorEntity->toDomainEntity(), null, null);
                    $round->addParticipant($participant);
                }
            }
        }

        return $round;
    }
}
