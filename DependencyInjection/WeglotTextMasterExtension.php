<?php

namespace Weglot\TextMasterBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WeglotTextMasterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('weglot_textmaster_api.api_key', $config['api_key']);
        $container->setParameter('weglot_textmaster_api.api_secret', $config['api_secret']);
        $container->setParameter('weglot_textmaster_api.textmaster_env', $config['textmaster_env']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function getAlias(): string
    {
        return 'weglot_textmaster_api';
    }
}
