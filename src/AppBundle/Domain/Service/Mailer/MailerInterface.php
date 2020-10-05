<?php

namespace AppBundle\Domain\Service\Mailer;

use AppBundle\Domain\Entity\Contest\Competitor;
use AppBundle\Domain\Entity\Contest\Contest;

/**
 * Interface SendMail
 *
 * @package AppBundle\Domain\Service\Mailer
 */
interface MailerInterface
{
    /**
     * Sends an email with the link with the token to a competitor
     *
     * @param Competitor $competitor
     * @param Contest $contest
     * @throws MailerException
     */
    public function sendTokenToCompetitor(Competitor $competitor, Contest $contest) : void;
}
