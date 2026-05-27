<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient;

use Aws\Sqs\SqsClient;
use Lickd\SlackGatewayClient\Contracts\SlackGatewayPublisherInterface;
use Lickd\SlackGatewayClient\DataTransferObjects\SlackMessageDto;
use Lickd\SlackGatewayClient\Enums\SlackQueue;
use Psr\Log\LoggerInterface;

final class SlackGatewayPublisher implements SlackGatewayPublisherInterface
{
    public function __construct(
        private readonly SqsClient $sqsClient,
        private readonly LoggerInterface $logger,
        private readonly string $queueBaseUrl,
        private readonly string $queuePrefix,
    ) {
        if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $queuePrefix)) {
            throw new \InvalidArgumentException(
                'Queue prefix must be non-empty and contain only alphanumeric characters, hyphens, and underscores.'
            );
        }
    }

    public function publish(SlackMessageDto $message, SlackQueue $queue): void
    {
        $queueUrl = rtrim($this->queueBaseUrl, '/') . '/' . $this->queuePrefix . '-' . $queue->suffix();

        $this->sqsClient->sendMessage([
            'QueueUrl'    => $queueUrl,
            'MessageBody' => json_encode([
                'channel'        => $message->channel,
                'text'           => $message->text,
                'blocks'         => $message->blocks,
                'attachments'    => $message->attachments,
                'threadTs'       => $message->threadTs,
                'idempotencyKey' => $message->idempotencyKey,
                'source'         => $message->source,
            ], JSON_THROW_ON_ERROR),
        ]);

        $this->logger->info('Slack message published to SQS', [
            'class'           => self::class,
            'function'        => __FUNCTION__,
            'channel'         => $message->channel,
            'queue'           => $queue->name,
            'source'          => $message->source,
            'idempotency_key' => $message->idempotencyKey,
        ]);
    }
}
