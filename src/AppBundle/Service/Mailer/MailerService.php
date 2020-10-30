<?php

namespace AppBundle\Service\Mailer;

use AppBundle\Domain\Entity\Contest\Competitor;
use AppBundle\Domain\Entity\Contest\Contest;
use AppBundle\Domain\Service\Mailer\MailerException;
use AppBundle\Domain\Service\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MailerService
 *
 * @package AppBundle\Service\Mailer
 */
class MailerService implements MailerInterface
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var string */
    private $from;

    /**
     * MailerService constructor.
     *
     * @param TranslatorInterface   $translator
     * @param UrlGeneratorInterface $urlGenerator
     * @param string                $from
     */
    public function __construct(
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        string $from
    ) {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->from = $from;
    }

    /**
     * Sends an email with the link with the token to a competitor
     *
     * @param Competitor $competitor
     * @param Contest $contest
     * @throws MailerException
     */
    public function sendTokenToCompetitor(Competitor $competitor, Contest $contest): void
    {
        $subject = $this->translator->trans('app.mail.subject', [
            '%contest%' => $contest->name()
        ]);

        $message = $this->translator->trans('app.mail.content.1', [
            '%contest%' => $contest->name()
        ]);

        $message .= $this->translator->trans('app.mail.content.2', [
            '%email%' => $competitor->email(),
            '%name%' => $competitor->name(),
            '%url%' => $competitor->url()
        ]);

        if (!$competitor->validated()) {
            $message .= $this->translator->trans('app.mail.content.3', [
                '%url%' => $this->urlGenerator->generate('contest_validate_token', [
                    'token' => $competitor->validationToken()
                ], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);
        }

        $message .= $this->translator->trans('app.mail.content.4');

        $headers = 'To: ' . $competitor->email() . "\r\n"
            . 'Bcc: ' . $this->from . "\r\n"
            . 'From: ' . $this->from . "\r\n"
            . 'Reply-To: ' . $this->from . "\r\n"
            . 'X-Mailer: PHP/' . phpversion();

        $parameters = null;

        try {
            $result = mail(
                $competitor->email(),
                $subject,
                $message,
                $headers,
                $parameters
            );
            if (!$result) {
                throw new MailerException("Unable to send the email!");
            }
        } catch (\Exception $exception) {
            throw new MailerException("An error occurred while sending an email!", 0, $exception);
        }
    }
}
