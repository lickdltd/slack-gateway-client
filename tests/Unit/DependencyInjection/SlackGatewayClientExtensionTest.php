<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\Tests\Unit\DependencyInjection;

use Lickd\SlackGatewayClient\Contracts\SlackGatewayPublisherInterface;
use Lickd\SlackGatewayClient\DependencyInjection\SlackGatewayClientExtension;
use Lickd\SlackGatewayClient\SlackGatewayPublisher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SlackGatewayClientExtensionTest extends TestCase
{
    private const CONFIG = [[
        'queue_base_url' => 'https://sqs.eu-west-1.amazonaws.com/123456789',
        'queue_prefix'   => 'test-slack',
    ]];

    private ContainerBuilder $container;
    private SlackGatewayClientExtension $extension;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->extension = new SlackGatewayClientExtension();
    }

    public function testPublisherIsRegistered(): void
    {
        $this->extension->load(self::CONFIG, $this->container);

        $this->assertTrue($this->container->hasDefinition(SlackGatewayPublisher::class));
    }

    public function testInterfaceAliasIsRegistered(): void
    {
        $this->extension->load(self::CONFIG, $this->container);

        $this->assertTrue($this->container->hasAlias(SlackGatewayPublisherInterface::class));
        $this->assertSame(
            SlackGatewayPublisher::class,
            (string) $this->container->getAlias(SlackGatewayPublisherInterface::class)
        );
    }

    public function testQueueConfigIsInjected(): void
    {
        $this->extension->load(self::CONFIG, $this->container);

        $args   = $this->container->getDefinition(SlackGatewayPublisher::class)->getArguments();
        $params = (new \ReflectionClass(SlackGatewayPublisher::class))->getConstructor()->getParameters();
        $index  = array_flip(array_map(fn(\ReflectionParameter $p) => $p->getName(), $params));

        $this->assertSame('https://sqs.eu-west-1.amazonaws.com/123456789', $args[$index['queueBaseUrl']]);
        $this->assertSame('test-slack', $args[$index['queuePrefix']]);
    }
}
