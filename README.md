# lickd/slack-gateway-client

Laravel package for publishing messages to the Lickd Slack gateway micro-service via SQS. Does not talk to Slack directly — it puts messages onto a queue for the gateway to process.

## Requirements

- PHP 8.2+
- Laravel 10+

## Installation

```bash
composer require lickd/slack-gateway-client
```

The service provider is auto-discovered via Laravel's package discovery.

## Configuration

Add the following to `config/services.php` in the consuming service:

```php
'slack_gateway' => [
    'queue_base_url' => env('SLACK_GATEWAY_QUEUE_BASE_URL'),
],
```

Add the env var to your `.env`:

```env
SLACK_GATEWAY_QUEUE_BASE_URL=https://sqs.eu-west-1.amazonaws.com/123456789
```

The package resolves an `Aws\Sqs\SqsClient` from the Laravel container — ensure one is bound in your service provider. SQS credentials (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`) are managed by the consuming service.

## Usage

Inject `SlackGatewayPublisher` and call `publish()` with a `SlackMessageDto` and a `SlackQueue` priority:

```php
use Lickd\SlackGatewayClient\DataTransferObjects\SlackMessageDto;
use Lickd\SlackGatewayClient\Enums\SlackQueue;
use Lickd\SlackGatewayClient\SlackGatewayPublisher;

class MyService
{
    public function __construct(private readonly SlackGatewayPublisher $slack) {}

    public function notifyRelease(string $version): void
    {
        $this->slack->publish(
            new SlackMessageDto(
                channel:        '#releases',
                text:           "Deploy {$version} complete",
                source:         'deploy-service',
                idempotencyKey: "deploy-{$version}",
            ),
            SlackQueue::High,
        );
    }
}
```

### SlackMessageDto

| Property | Type | Required | Default |
|---|---|---|---|
| `channel` | `string` | yes | — |
| `text` | `string` | yes | — |
| `blocks` | `array` | no | `[]` |
| `attachments` | `array` | no | `[]` |
| `threadTs` | `?string` | no | `null` |
| `idempotencyKey` | `?string` | no | `null` |
| `source` | `string` | no | `'unknown'` |

### SlackQueue

| Case | Queue name |
|---|---|
| `SlackQueue::High` | `lickd-slack-high` |
| `SlackQueue::Normal` | `lickd-slack-normal` |
| `SlackQueue::Low` | `lickd-slack-low` |

## Testing

```bash
composer install
./vendor/bin/phpunit
```

## Repository

[https://github.com/lickdltd/slack-gateway-client](https://github.com/lickdltd/slack-gateway-client)
