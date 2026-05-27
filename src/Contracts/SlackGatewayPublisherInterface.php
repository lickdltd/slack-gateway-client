<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\Contracts;

use Lickd\SlackGatewayClient\DataTransferObjects\SlackMessageDto;
use Lickd\SlackGatewayClient\Enums\SlackQueue;

interface SlackGatewayPublisherInterface
{
    public function publish(SlackMessageDto $message, SlackQueue $queue): void;
}
