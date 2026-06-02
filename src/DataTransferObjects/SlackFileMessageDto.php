<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\DataTransferObjects;

final readonly class SlackFileMessageDto
{
    public function __construct(
        public string $channel,
        public string $text,
        public SlackFileDto $file,
        public array $blocks = [],
        public ?string $threadTs = null,
        public ?string $taskToken = null,
        public ?string $idempotencyKey = null,
        public string $source = 'unknown',
    ) {}
}
