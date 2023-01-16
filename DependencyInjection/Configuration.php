<?php

namespace Weglot\TextMasterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('weglot_textmaster_api');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('api_key')->isRequired()->end()
            ->scalarNode('api_secret')->isRequired()->end()
            ->enumNode('textmaster_env')
            ->values(['staging', 'production'])
            ->isRequired()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
