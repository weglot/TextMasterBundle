<?php
/**
 * Created by PhpStorm.
 * User: etienne
 * Date: 05/11/2018
 * Time: 15:38.
 */

namespace Weglot\TextMasterBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WeglotTextMasterExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('weglot_textmaster_api.api_key', $config['api_key']);
        $container->setParameter('weglot_textmaster_api.api_secret', $config['api_secret']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function getAlias()
    {
        return 'weglot_textmaster_api';
    }
}
