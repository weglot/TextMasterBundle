<?php
/**
 * Created by PhpStorm.
 * User: etienne
 * Date: 09/11/2018
 * Time: 14:24.
 */

namespace Weglot\TextMasterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
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
