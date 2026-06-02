<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\DataTransferObjects;

final readonly class SlackFileDto
{
    public function __construct(
        public string $s3Bucket,
        public string $s3Key,
        public string $name,
    ) {}
}
