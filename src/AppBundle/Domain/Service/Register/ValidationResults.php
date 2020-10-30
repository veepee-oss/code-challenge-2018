<?php

namespace AppBundle\Domain\Service\Register;

/**
 * Class to hold the validation results when registering a competitor to a contest
 *
 * Returns an array with the next structure:
 *
 * [ 'global' => [ 'global-error-1', 'global-error-20, ...]
 * , 'field-1' => [ 'field-1-eror-1', ... ]
 * , 'field-2' => [ ... ],
 * , ... ]
 *
 * @package AppBundle\Domain\Service\Register
 */
class ValidationResults
{
    /** @var int constants */
    public const STATUS_OK = 0;
    public const STATUS_ERROR = 4;

    /** @var int status */
    private $status = self::STATUS_OK;

    /** @var array */
    private $result = [];

    /**
     * Return if the validation is OK
     *
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->status != self::STATUS_ERROR;
    }

    /**
     * Return the status
     *
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * Return the results
     *
     * @return array
     */
    public function result(): array
    {
        return $this->result;
    }

    /**
     * Add a global error message
     *
     * @param string $message
     * @return ValidationResults
     */
    public function addGlobalError(string $message) : ValidationResults
    {
        $this->status = self::STATUS_ERROR;
        $this->result['global'][] = $message;
        return $this;
    }

    /**
     * Add an error message to a particular field
     *
     * @param string $message
     * @param string $field
     * @return ValidationResults
     */
    public function addFieldError(string $message, string $field) : ValidationResults
    {
        $this->status = self::STATUS_ERROR;
        $this->result[$field][] = $message;
        return $this;
    }

    /**
     * Merges the current validation results with another results
     *
     * @param ValidationResults $results
     * @return $this
     */
    public function mergeResults(ValidationResults $results) : ValidationResults
    {
        foreach ($results->result() as $field => $messages) {
            foreach ($messages as $message) {
                $this->addFieldError($message, $field);
            }
        }
        return  $this;
    }
}
