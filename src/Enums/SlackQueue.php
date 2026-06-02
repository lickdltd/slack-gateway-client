<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\Enums;

enum SlackQueue
{
    case High;
    case Normal;
    case Low;
    case FileUpload;

    public function suffix(): string
    {
        return match ($this) {
            self::High       => 'high',
            self::Normal     => 'normal',
            self::Low        => 'low',
            self::FileUpload => 'file-upload',
        };
    }
}
