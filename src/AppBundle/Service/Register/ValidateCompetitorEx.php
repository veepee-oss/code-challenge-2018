<?php

namespace AppBundle\Service\Register;

use AppBundle\Domain\Entity\Competitor\Competitor;
use AppBundle\Domain\Entity\Contest\Contest;
use AppBundle\Domain\Service\MovePlayer\ValidatePlayerServiceInterface;
use AppBundle\Domain\Service\Register\ValidateCompetitor;
use AppBundle\Domain\Service\Register\ValidateCompetitorInterface;
use AppBundle\Domain\Service\Register\ValidationResults;
use AppBundle\Repository\CompetitorRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Service to add extra validations to the validate a competitor for a contest service
 *
 * @package AppBundle\Service\Register
 */
class ValidateCompetitorEx extends ValidateCompetitor implements ValidateCompetitorInterface
{
    /** @var CompetitorRepository */
    private $repository;

    /**
     * ValidateCompetitor constructor
     *
     * @param ValidatePlayerServiceInterface $playerValidator
     * @param CompetitorRepository $repository
     */
    public function __construct(ValidatePlayerServiceInterface $playerValidator, CompetitorRepository $repository)
    {
        parent::__construct($playerValidator);
        $this->repository = $repository;
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
        $results = parent::validate($competitor, $contest);
        $this->validateDuplicateUrl($competitor->contest(), $competitor->email(), $competitor->url(), $results);
        return $results;
    }

    /**
     * Validates if the URL is duplicated
     *
     * @param string $contest
     * @param string $email
     * @param string $url
     * @param ValidationResults $results
     * @return void
     */
    protected function validateDuplicateUrl(string $contest, string $email, string $url, ValidationResults $results)
    {
        try {
            $count = $this->repository->searchForDuplicateUrl($contest, $email, $url);
            if ($count > 0) {
                $results->addFieldError('This url has already been registered!', 'url');
            }
        } catch (NonUniqueResultException $exc) {
            $results->addGlobalError($exc->getMessage());
        }
    }
}
