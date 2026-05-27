<?php

declare(strict_types=1);

namespace Lickd\SlackGatewayClient\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('slack_gateway_client');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('queue_base_url')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('queue_prefix')->isRequired()->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }
}
