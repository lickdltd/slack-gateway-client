# slack-gateway-client

[![CI](https://github.com/lickdltd/slack-gateway-client/actions/workflows/test.yml/badge.svg)](https://github.com/lickdltd/slack-gateway-client/actions/workflows/test.yml)

A PHP package for publishing messages to a Slack gateway micro-service via AWS SQS. Does not communicate with Slack directly — it enqueues messages for a gateway service to process.

Supports Laravel and Symfony.

## Requirements

| Dependency | Version |
|---|---|
| PHP | `^8.2` |
| Laravel | `^10.0 \| ^11.0 \| ^12.0` |
| Symfony | `^6.0 \| ^7.0` |
| aws/aws-sdk-php | `^3.0` |

Laravel and Symfony are optional — only the one matching your framework is required.

## Installation

```bash
composer require lickd/slack-gateway-client
```

### Laravel

The service provider is auto-discovered. No additional setup required.

### Symfony

Register the bundle in `config/bundles.php`:

```php
Lickd\SlackGatewayClient\SlackGatewayClientBundle::class => ['all' => true],
```

## Configuration

Both frameworks read from the same two environment variables:

```env
SLACK_GATEWAY_QUEUE_BASE_URL=https://sqs.eu-west-1.amazonaws.com/123456789
SLACK_GATEWAY_QUEUE_PREFIX=my-slack
```

Queue names are derived as `{prefix}-high`, `{prefix}-normal`, and `{prefix}-low`.

Both frameworks also require an `Aws\Sqs\SqsClient` instance to be bound in the container. SQS credentials (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`) are managed by the consuming application.

### Laravel

Add to `config/services.php`:

```php
'slack_gateway' => [
    'queue_base_url' => env('SLACK_GATEWAY_QUEUE_BASE_URL'),
    'queue_prefix'   => env('SLACK_GATEWAY_QUEUE_PREFIX'),
],
```

### Symfony

Create `config/packages/slack_gateway_client.yaml`:

```yaml
slack_gateway_client:
    queue_base_url: '%env(SLACK_GATEWAY_QUEUE_BASE_URL)%'
    queue_prefix: '%env(SLACK_GATEWAY_QUEUE_PREFIX)%'
```

## Usage

Inject `SlackGatewayPublisherInterface` and call `publish()` with a `SlackMessageDto` and a `SlackQueue` priority:

```php
use Lickd\SlackGatewayClient\Contracts\SlackGatewayPublisherInterface;
use Lickd\SlackGatewayClient\DataTransferObjects\SlackMessageDto;
use Lickd\SlackGatewayClient\Enums\SlackQueue;

class MyService
{
    public function __construct(private readonly SlackGatewayPublisherInterface $slack) {}

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

| Case | Derived queue name |
|---|---|
| `SlackQueue::High` | `{SLACK_GATEWAY_QUEUE_PREFIX}-high` |
| `SlackQueue::Normal` | `{SLACK_GATEWAY_QUEUE_PREFIX}-normal` |
| `SlackQueue::Low` | `{SLACK_GATEWAY_QUEUE_PREFIX}-low` |

## Testing

```bash
composer install
./vendor/bin/phpunit
```

## Licence

MIT. See [LICENSE](LICENSE).
