<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\Tests\Unit\DataTransferObjects;

use Lickd\SlackGatewayClient\DataTransferObjects\SlackFileDto;
use Lickd\SlackGatewayClient\DataTransferObjects\SlackFileMessageDto;
use PHPUnit\Framework\TestCase;

class SlackFileMessageDtoTest extends TestCase
{
    public function testAllFieldsAreSetCorrectly(): void
    {
        $file = new SlackFileDto(s3Bucket: 'my-bucket', s3Key: 'uploads/report.pdf', name: 'report.pdf');

        $dto = new SlackFileMessageDto(
            channel:        '#uploads',
            text:           'Here is your report',
            file:           $file,
            blocks:         [['type' => 'section']],
            threadTs:       '1234567890.123456',
            taskToken:      'task-token-abc',
            idempotencyKey: 'key-123',
            source:         'report-service',
        );

        $this->assertSame('#uploads', $dto->channel);
        $this->assertSame('Here is your report', $dto->text);
        $this->assertSame($file, $dto->file);
        $this->assertSame([['type' => 'section']], $dto->blocks);
        $this->assertSame('1234567890.123456', $dto->threadTs);
        $this->assertSame('task-token-abc', $dto->taskToken);
        $this->assertSame('key-123', $dto->idempotencyKey);
        $this->assertSame('report-service', $dto->source);
    }

    public function testDefaultsWhenOptionalFieldsOmitted(): void
    {
        $file = new SlackFileDto(s3Bucket: 'my-bucket', s3Key: 'uploads/clip.mp3', name: 'clip.mp3');

        $dto = new SlackFileMessageDto(channel: '#music', text: 'New clip', file: $file);

        $this->assertSame([], $dto->blocks);
        $this->assertNull($dto->threadTs);
        $this->assertNull($dto->taskToken);
        $this->assertNull($dto->idempotencyKey);
        $this->assertSame('unknown', $dto->source);
    }

    public function testSlackFileDtoFields(): void
    {
        $file = new SlackFileDto(s3Bucket: 'assets-bucket', s3Key: 'path/to/file.mp4', name: 'file.mp4');

        $this->assertSame('assets-bucket', $file->s3Bucket);
        $this->assertSame('path/to/file.mp4', $file->s3Key);
        $this->assertSame('file.mp4', $file->name);
    }
}
