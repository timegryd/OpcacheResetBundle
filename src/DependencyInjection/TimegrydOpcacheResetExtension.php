<?php

namespace Timegryd\OpcacheResetBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Timegryd\OpcacheResetBundle\Helper\OpcacheResetCommandHelper;
use Timegryd\OpcacheResetBundle\Command\OpcacheResetCommand;

class TimegrydOpcacheResetExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processConfig = $this->processConfiguration($configuration, $configs);

        foreach ($processConfig as $key => $value) {
            $container->setParameter($this->getAlias().'.'.$key, $value);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->addClassesToCompile([
            OpcacheResetCommandHelper::class,
            OpcacheResetCommand::class,
        ]);
    }
}
