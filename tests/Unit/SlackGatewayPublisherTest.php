<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\Tests\Unit;

use Aws\Sqs\SqsClient;
use Lickd\SlackGatewayClient\DataTransferObjects\SlackMessageDto;
use Lickd\SlackGatewayClient\Enums\SlackQueue;
use Lickd\SlackGatewayClient\SlackGatewayPublisher;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SlackGatewayPublisherTest extends TestCase
{
    private const QUEUE_BASE_URL = 'https://sqs.eu-west-1.amazonaws.com/123456789';

    public function testPublishUsesCorrectQueueUrlForHighPriority(): void
    {
        $capturedArgs = null;

        $sqsClient = $this->createMock(SqsClient::class);
        $sqsClient->expects($this->once())
            ->method('__call')
            ->with('sendMessage', $this->callback(function ($args) use (&$capturedArgs) {
                $capturedArgs = $args[0];
                return true;
            }));

        $logger = $this->createMock(LoggerInterface::class);

        $publisher = new SlackGatewayPublisher($sqsClient, $logger, self::QUEUE_BASE_URL);
        $dto = new SlackMessageDto(channel: '#general', text: 'Hello');

        $publisher->publish($dto, SlackQueue::High);

        $this->assertSame(
            self::QUEUE_BASE_URL . '/lickd-slack-high',
            $capturedArgs['QueueUrl'],
        );
    }

    public function testPublishUsesCorrectQueueUrlForNormalPriority(): void
    {
        $capturedArgs = null;

        $sqsClient = $this->createMock(SqsClient::class);
        $sqsClient->expects($this->once())
            ->method('__call')
            ->with('sendMessage', $this->callback(function ($args) use (&$capturedArgs) {
                $capturedArgs = $args[0];
                return true;
            }));

        $logger = $this->createMock(LoggerInterface::class);

        $publisher = new SlackGatewayPublisher($sqsClient, $logger, self::QUEUE_BASE_URL);
        $dto = new SlackMessageDto(channel: '#general', text: 'Hello');

        $publisher->publish($dto, SlackQueue::Normal);

        $this->assertSame(
            self::QUEUE_BASE_URL . '/lickd-slack-normal',
            $capturedArgs['QueueUrl'],
        );
    }

    public function testPublishUsesCorrectQueueUrlForLowPriority(): void
    {
        $capturedArgs = null;

        $sqsClient = $this->createMock(SqsClient::class);
        $sqsClient->expects($this->once())
            ->method('__call')
            ->with('sendMessage', $this->callback(function ($args) use (&$capturedArgs) {
                $capturedArgs = $args[0];
                return true;
            }));

        $logger = $this->createMock(LoggerInterface::class);

        $publisher = new SlackGatewayPublisher($sqsClient, $logger, self::QUEUE_BASE_URL);
        $dto = new SlackMessageDto(channel: '#general', text: 'Hello');

        $publisher->publish($dto, SlackQueue::Low);

        $this->assertSame(
            self::QUEUE_BASE_URL . '/lickd-slack-low',
            $capturedArgs['QueueUrl'],
        );
    }

    public function testPublishSerialisesAllDtoFieldsIntoMessageBody(): void
    {
        $capturedArgs = null;

        $sqsClient = $this->createMock(SqsClient::class);
        $sqsClient->expects($this->once())
            ->method('__call')
            ->with('sendMessage', $this->callback(function ($args) use (&$capturedArgs) {
                $capturedArgs = $args[0];
                return true;
            }));

        $logger = $this->createMock(LoggerInterface::class);

        $publisher = new SlackGatewayPublisher($sqsClient, $logger, self::QUEUE_BASE_URL);

        $dto = new SlackMessageDto(
            channel:        '#releases',
            text:           'Deploy complete',
            blocks:         [['type' => 'section', 'text' => ['type' => 'mrkdwn', 'text' => 'Done']]],
            attachments:    [['color' => '#36a64f', 'text' => 'Success']],
            threadTs:       '1234567890.123456',
            idempotencyKey: 'deploy-abc-123',
            source:         'deploy-service',
        );

        $publisher->publish($dto, SlackQueue::Normal);

        $body = json_decode($capturedArgs['MessageBody'], true);

        $this->assertSame('#releases', $body['channel']);
        $this->assertSame('Deploy complete', $body['text']);
        $this->assertSame([['type' => 'section', 'text' => ['type' => 'mrkdwn', 'text' => 'Done']]], $body['blocks']);
        $this->assertSame([['color' => '#36a64f', 'text' => 'Success']], $body['attachments']);
        $this->assertSame('1234567890.123456', $body['threadTs']);
        $this->assertSame('deploy-abc-123', $body['idempotencyKey']);
        $this->assertSame('deploy-service', $body['source']);
    }

    public function testPublishSerialisesDefaultsWhenOptionalFieldsOmitted(): void
    {
        $capturedArgs = null;

        $sqsClient = $this->createMock(SqsClient::class);
        $sqsClient->expects($this->once())
            ->method('__call')
            ->with('sendMessage', $this->callback(function ($args) use (&$capturedArgs) {
                $capturedArgs = $args[0];
                return true;
            }));

        $logger = $this->createMock(LoggerInterface::class);

        $publisher = new SlackGatewayPublisher($sqsClient, $logger, self::QUEUE_BASE_URL);
        $dto = new SlackMessageDto(channel: '#alerts', text: 'Something happened');

        $publisher->publish($dto, SlackQueue::Low);

        $body = json_decode($capturedArgs['MessageBody'], true);

        $this->assertSame([], $body['blocks']);
        $this->assertSame([], $body['attachments']);
        $this->assertNull($body['threadTs']);
        $this->assertNull($body['idempotencyKey']);
        $this->assertSame('unknown', $body['source']);
    }
}
