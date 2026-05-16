<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient;

use Aws\Sqs\SqsClient;
use Lickd\SlackGatewayClient\DataTransferObjects\SlackMessageDto;
use Lickd\SlackGatewayClient\Enums\SlackQueue;
use Psr\Log\LoggerInterface;

final class SlackGatewayPublisher
{
    public function __construct(
        private readonly SqsClient $sqsClient,
        private readonly LoggerInterface $logger,
        private readonly string $queueBaseUrl,
    ) {}

    public function publish(SlackMessageDto $message, SlackQueue $queue): void
    {
        $queueUrl = $this->queueBaseUrl . '/' . $queue->value;

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
            ]),
        ]);

        $this->logger->info('Slack message published to SQS', [
            'class'           => self::class,
            'function'        => __FUNCTION__,
            'channel'         => $message->channel,
            'queue'           => $queue->value,
            'source'          => $message->source,
            'idempotency_key' => $message->idempotencyKey,
        ]);
    }
}
