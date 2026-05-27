<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient;

use Aws\Sqs\SqsClient;
use Illuminate\Support\ServiceProvider;
use Lickd\SlackGatewayClient\Contracts\SlackGatewayPublisherInterface;
use Psr\Log\LoggerInterface;

class SlackGatewayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SlackGatewayPublisher::class, function ($app) {
            $queueBaseUrl = config('services.slack_gateway.queue_base_url');
            $queuePrefix  = config('services.slack_gateway.queue_prefix');

            if (!is_string($queueBaseUrl) || trim($queueBaseUrl) === '') {
                throw new \InvalidArgumentException('Missing or invalid config: services.slack_gateway.queue_base_url');
            }

            if (!is_string($queuePrefix) || trim($queuePrefix) === '') {
                throw new \InvalidArgumentException('Missing or invalid config: services.slack_gateway.queue_prefix');
            }

            return new SlackGatewayPublisher(
                sqsClient:    $app->make(SqsClient::class),
                logger:       $app->make(LoggerInterface::class),
                queueBaseUrl: $queueBaseUrl,
                queuePrefix:  $queuePrefix,
            );
        });

        $this->app->alias(SlackGatewayPublisher::class, SlackGatewayPublisherInterface::class);
    }
}
