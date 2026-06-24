<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\DependencyInjection;

use Aws\Sqs\SqsClient;
use Lickd\SlackGatewayClient\Contracts\SlackGatewayPublisherInterface;
use Lickd\SlackGatewayClient\SlackGatewayPublisher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class SlackGatewayClientExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->register(SlackGatewayPublisher::class, SlackGatewayPublisher::class)
            ->setArguments([
                new Reference(SqsClient::class),
                new Reference('logger'),
                $config['queue_base_url'],
                $config['queue_prefix'],
                $config['queue_suffix'],
            ])
            ->setAutowired(false)
            ->setPublic(false);

        $container->setAlias(SlackGatewayPublisherInterface::class, SlackGatewayPublisher::class)
            ->setPublic(false);
    }
}
