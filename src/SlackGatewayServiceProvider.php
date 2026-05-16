<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient;

use Aws\Sqs\SqsClient;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class SlackGatewayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SlackGatewayPublisher::class, function ($app) {
            return new SlackGatewayPublisher(
                sqsClient:    $app->make(SqsClient::class),
                logger:       $app->make(LoggerInterface::class),
                queueBaseUrl: config('services.slack_gateway.queue_base_url'),
            );
        });
    }
}
