<?php

namespace AppBundle\Domain\Service\Register;

use AppBundle\Domain\Entity\Contest\Competitor;
use AppBundle\Domain\Entity\Contest\Contest;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Position;
use AppBundle\Domain\Service\MovePlayer\ValidatePlayerServiceInterface;

/**
 * Service to validate a competitor for a contest
 *
 * @package AppBundle\Domain\Service\Register
 */
class ValidateCompetitor implements ValidateCompetitorInterface
{
    /** @var ValidatePlayerServiceInterface */
    protected $playerValidator;

    /**
     * ValidateCompetitor constructor
     *
     * @param ValidatePlayerServiceInterface $playerValidator
     */
    public function __construct(ValidatePlayerServiceInterface $playerValidator)
    {
        $this->playerValidator = $playerValidator;
    }

    /**
     * Validates the competitor for one contest
     *
     * @param Competitor $competitor
     * @param Contest $contest
     * @return ValidationResults
     */
    public function validate(Competitor $competitor, Contest $contest): ValidationResults
    {
        $results = new ValidationResults();

        $this->validateEmail($competitor, $contest, $results);
        $this->validateUrlAndEmailAndGetName($competitor, $results);

        return $results;
    }

    /**
     * Validates the email using a regular expression
     *
     * @param Competitor $competitor
     * @param Contest $contest
     * @param ValidationResults $results
     * @return void
     */
    protected function validateEmail(Competitor $competitor, Contest $contest, ValidationResults $results)
    {
        $regex = $contest->emailRestrictionsRegex();
        if (null === $regex || empty($regex)) {
            return;
        }

        $email = $competitor->email();
        if (!preg_match('/' . $regex . '/', $email)) {
            $results->addFieldError('The email does not match the rules of this this contest!', 'email');
        }
    }

    /**
     * Validates the endpoint /name of the URL and the email returned
     *
     * @param Competitor $competitor
     * @param ValidationResults $results
     * @return void
     */
    protected function validateUrlAndEmailAndGetName(Competitor $competitor, ValidationResults $results)
    {
        try {
            $player = new Player($competitor->url(), new Position(0, 0));
            $this->playerValidator->validate($player);
            if ($competitor->email() != $player->email()) {
                $results->addFieldError('The email provided is not the same returned by the API!', 'email');
            } else {
                $competitor->setName($player->name());
            }
        } catch (\Exception $exc) {
            $results->addFieldError($exc->getMessage(), 'url');
        }
    }
}
