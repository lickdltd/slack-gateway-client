<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\DataTransferObjects;

final readonly class SlackMessageDto
{
    public function __construct(
        public string $channel,
        public string $text,
        public array $blocks = [],
        public array $attachments = [],
        public ?string $threadTs = null,
        public ?string $idempotencyKey = null,
        public string $source = 'unknown',
    ) {}
}
