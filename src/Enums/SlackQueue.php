<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\Enums;

enum SlackQueue: string
{
    case High = 'lickd-slack-high';
    case Normal = 'lickd-slack-normal';
    case Low = 'lickd-slack-low';
}
