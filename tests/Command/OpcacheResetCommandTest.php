<?php

namespace Timegryd\OpcacheResetBundle\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response as HttpResponse;
use Timegryd\OpcacheResetBundle\Helper\OpcacheResetCommandHelper;
use Timegryd\OpcacheResetBundle\Command\OpcacheResetCommand;

class OpcacheResetCommandTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $helper = \Mockery::mock(OpcacheResetCommandHelper::class)
            ->shouldReceive('createFile')->once()
            ->shouldReceive('generateUrl')->once()
            ->shouldReceive('clean')->once()
            ->shouldReceive('handleResponse')->once()
            ->getMock()
        ;

        $response = \Mockery::mock(HttpResponse::class)
            ->shouldReceive('getStatusCode')
                ->andReturn(Response::HTTP_OK)
            ->shouldReceive('getBody')
                ->andReturn('{"success": "true", "message": "Success"}')
            ->getMock()
        ;

        $httpClient = \Mockery::mock(HttpClient::class)
            ->shouldReceive('request')->once()
            ->andReturn($response)
            ->getMock()
        ;

        $application = new Application();
        $application->add(new OpcacheResetCommand(
            'default-host.com',
            '/default-dir',
            $helper,
            $httpClient
        ));

        $command = $application->find('opcache:reset');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertEquals('', $commandTester->getDisplay());
    }
}
