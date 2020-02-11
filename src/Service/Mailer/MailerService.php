<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use App\Templating\TwigTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    private const TEMPLATE_SUBJECT_MAP = [
        TwigTemplate::USER_REGISTER => '[My App] Welcome!',
    ];

    private MailerInterface $mailer;
    private Environment $engine;
    private LoggerInterface $logger;
    private string $defaultSender;

    public function __construct(MailerInterface $mailer, Environment $engine, LoggerInterface $logger, string $defaultSender)
    {
        $this->mailer = $mailer;
        $this->engine = $engine;
        $this->defaultSender = $defaultSender;
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    public function send(string $receiver, string $template, array $payload): void
    {
        $email = (new Email())
            ->from($this->defaultSender)
            ->to($receiver)
            ->subject(self::TEMPLATE_SUBJECT_MAP[$template])
            ->html($this->engine->render($template, $payload));

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->debug(\sprintf('Error sending message %s', $exception->getMessage()));
        }
    }
}
