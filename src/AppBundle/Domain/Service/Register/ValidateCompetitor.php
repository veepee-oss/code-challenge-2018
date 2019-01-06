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

        $this->validateEmail($competitor->email(), $contest->emailRestrictionsRegex(), $results);
        $this->validateUrlAndEmail($competitor->url(), $competitor->email(), $results);

        return $results;
    }

    /**
     * Validates the email using a regular expression
     *
     * @param string $email
     * @param string|null $regex
     * @param ValidationResults $results
     * @return void
     */
    protected function validateEmail(string $email, ?string $regex, ValidationResults $results)
    {
        if (null === $regex || empty($regex)) {
            return;
        }

        if (!preg_match('/' . $regex . '/', $email)) {
            $results->addFieldError('The email does not match the rules of this this contest!', 'email');
        }
    }

    /**
     * Validates the endpoint /name of the URL and the email returned
     *
     * @param string $url
     * @param string $email
     * @param ValidationResults $results
     * @return void
     */
    protected function validateUrlAndEmail(string $url, string $email, ValidationResults $results)
    {
        try {
            $player = new Player($url, new Position(0, 0));
            $this->playerValidator->validate($player);
            if ($email != $player->email()) {
                $results->addFieldError('The email provided is not the same returned by the API!', 'email');
            }
        } catch (\Exception $exc) {
            $results->addFieldError($exc->getMessage(), 'url');
        }
    }
}
