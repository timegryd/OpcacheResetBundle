<?php

namespace Timegryd\OpcacheResetBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Timegryd\OpcacheResetBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Processor
     */
    protected $processor;

    protected function setUp()
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testEmpty()
    {
        $this->processor->processConfiguration($this->configuration, []);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testHostRequired()
    {
        $this->processor->processConfiguration($this->configuration, [
            'timegryd-opcache-reset' => [],
        ]);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testDirRequired()
    {
        $this->processor->processConfiguration($this->configuration, [
            'timegryd-opcache-reset' => [
                'host' => 'example.com',
            ],
        ]);
    }

    public function testSuccess()
    {
        $actual = $this->processor->processConfiguration($this->configuration, [
            'timegryd_opcache_reset' => [
                'host' => 'example.com',
                'dir' => 'web-dir',
            ],
        ]);

        $expected = [
            'host' => 'example.com',
            'dir' => 'web-dir',
        ];

        $this->assertEquals($expected, $actual);
    }
}
