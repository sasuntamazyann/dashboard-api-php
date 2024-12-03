<?php

namespace Dashboard\DashboardApi\Integrations\Slim\Common;


use Slim\Interfaces\ErrorRendererInterface;

class ApiErrorRenderer implements ErrorRendererInterface
{
    public function __invoke(\Throwable $exception, bool $displayErrorDetails): string
    {
        $payload = [
            'code' => 'something_went_wrong',
        ];

        if ($displayErrorDetails) {
            $payload['message'] = $exception->getMessage();
            $payload['trace'] = $exception->getTrace();
        } else {
            $payload['message'] = 'Something went wrong';
        }

        return json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
