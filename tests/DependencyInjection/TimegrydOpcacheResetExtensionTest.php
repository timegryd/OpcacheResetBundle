<?php

namespace Timegryd\OpcacheResetBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Timegryd\OpcacheResetBundle\DependencyInjection\TimegrydOpcacheResetExtension;

class TimegrydOpcacheResetExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [
            new TimegrydOpcacheResetExtension(),
        ];
    }

    public function testLoad()
    {
        $config = [
            'host' => 'example.com',
            'dir' => 'web-dir',
        ];

        $this->load($config);

        $this->assertContainerBuilderHasParameter('timegryd_opcache_reset.host', 'example.com');
        $this->assertContainerBuilderHasParameter('timegryd_opcache_reset.dir', 'web-dir');

        $this->assertContainerBuilderHasService('timegryd_opcache_reset.guzzle', 'GuzzleHttp\Client');
        $this->assertContainerBuilderHasService('timegryd_opcache_reset.helper', 'Timegryd\OpcacheResetBundle\Helper\OpcacheResetCommandHelper');
        $this->assertContainerBuilderHasService('timegryd_opcache_reset.command', 'Timegryd\OpcacheResetBundle\Command\OpcacheResetCommand');
    }
}
