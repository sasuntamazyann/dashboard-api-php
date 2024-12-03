<?php

namespace Dashboard\DashboardApi\Integrations\Mailgun;
use Mailgun\Mailgun;

class MailgunClient
{
    private array $config;

    private Mailgun $mailgunInstance;

    public function __construct(array $config)
    {
        $this->config = $config['mailgun'];
        $this->mailgunInstance = Mailgun::create($this->config['api_key']);
    }

    public function sendEmail(
        string $recipientEmailAddress,
        string $subject,
        string $textContent,
        string $htmlContent,
        array $variables = [],
    ): void {
        foreach ($variables as $variableName => $variableValue) {
            $htmlContent = str_replace("{{{$variableName}}}", $variableValue, $htmlContent);
            $textContent = str_replace("{{{$variableName}}}", $variableValue, $textContent);
        }

        $this->mailgunInstance->messages()->send(
            $this->config['domain'],
            [
                'from'    => "{$this->config['from']['name']} <{$this->config['from']['email']}>",
                'to'      => $recipientEmailAddress,
                'subject' => $subject,
                'text'    => $textContent,
                'html'    => $htmlContent,
            ]
        );
    }
}
