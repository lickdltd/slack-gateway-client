<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\Tests\Unit\DependencyInjection;

use Lickd\SlackGatewayClient\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    private Processor $processor;

    protected function setUp(): void
    {
        $this->processor = new Processor();
    }

    public function testValidConfigIsProcessed(): void
    {
        $config = $this->processor->processConfiguration(new Configuration(), [[
            'queue_base_url' => 'https://sqs.eu-west-1.amazonaws.com/123456789',
            'queue_prefix'   => 'test-slack',
        ]]);

        $this->assertSame('https://sqs.eu-west-1.amazonaws.com/123456789', $config['queue_base_url']);
        $this->assertSame('test-slack', $config['queue_prefix']);
    }

    public function testMissingQueueBaseUrlThrows(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration(new Configuration(), [[
            'queue_prefix' => 'test-slack',
        ]]);
    }

    public function testMissingQueuePrefixThrows(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->processor->processConfiguration(new Configuration(), [[
            'queue_base_url' => 'https://sqs.eu-west-1.amazonaws.com/123456789',
        ]]);
    }
}
