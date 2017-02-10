<?php

namespace Timegryd\OpcacheResetBundle\Tests\Helper;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use GuzzleHttp\Psr7\Response as HttpResponse;
use Timegryd\OpcacheResetBundle\Helper\OpcacheResetCommandHelper;

class OpcacheResetCommandHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Mockery\MockInterface of KernelInterface
     */
    protected $kernel;

    /**
     * @var \Mockery\MockInterface of OpcacheResetCommandHelper
     */
    protected $helper;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $workspace;

    protected function setUp()
    {
        $this->kernel = \Mockery::mock(KernelInterface::class);
        $this->helper = new OpcacheResetCommandHelper($this->kernel);
    }

    public function testGetFileName()
    {
        $fileName = $this->helper->getFileName('test');

        $this->assertEquals($fileName, 'opcache-reset-test.php');
    }

    public function testGetFilePath()
    {
        $filePath = $this->helper->getFilePath('dirtest', 'test');

        $this->assertEquals($filePath, 'dirtest/opcache-reset-test.php');
    }

    public function testGenerateUrl()
    {
        $url = $this->helper->generateUrl('timegryd.io', 'test');

        $this->assertEquals($url, 'timegryd.io/opcache-reset-test.php');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCreateFileDirNotExists()
    {
        $this->helper->createFile('dir-not-existing', 'token');
    }

    protected function setUpFilesystem()
    {
        $this->filesystem = new Filesystem();
        $this->workspace = sys_get_temp_dir().'/'.microtime(true).'.'.mt_rand();

        mkdir($this->workspace, 0777, true);

        $this->workspace = realpath($this->workspace);
    }

    protected function tearDownFilesystem()
    {
        $this->filesystem->remove($this->workspace);
    }

    public function testCreateFileDirExists()
    {
        $this->setUpFilesystem();

        $sourceFile = $this->workspace.DIRECTORY_SEPARATOR.'source_file';

        file_put_contents($sourceFile, 'SOURCE FILE');

        $this
            ->kernel
            ->shouldReceive('locateResource')
            ->andReturn($sourceFile)
        ;

        $return = $this->helper->createFile($this->workspace, 'token');

        $this->assertInstanceOf(OpcacheResetCommandHelper::class, $return);

        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'opcache-reset-token.php';

        $this->assertFileExists($targetFilePath);
        $this->assertEquals('SOURCE FILE', file_get_contents($targetFilePath));

        $this->tearDownFilesystem();
    }

    public function testClean()
    {
        $this->setUpFilesystem();

        $fileToDelete1 = $this->workspace.DIRECTORY_SEPARATOR.'opcache-reset-1.php';
        touch($fileToDelete1);

        $fileToDelete2 = $this->workspace.DIRECTORY_SEPARATOR.'opcache-reset-2.php';
        touch($fileToDelete2);

        $fileNotToDelete = $this->workspace.DIRECTORY_SEPARATOR.'filenottodelete.php';
        touch($fileNotToDelete);

        $return = $this->helper->clean($this->workspace);

        $this->assertFileNotExists($fileToDelete1);
        $this->assertFileNotExists($fileToDelete2);
        $this->assertFileExists($fileNotToDelete);

        $this->assertInstanceOf(OpcacheResetCommandHelper::class, $return);

        $this->tearDownFilesystem();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testHandleResponseBadStatusCode()
    {
        $response = $this->getMockedResponse(null, Response::HTTP_NOT_FOUND);

        $this->helper->handleResponse($response);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testHandleResponseBadJson()
    {
        $response = $this->getMockedResponse('bad-json');

        $this->helper->handleResponse($response);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testHandleResponsePropertySuccessMissing()
    {
        $response = $this->getMockedResponse('{"message": "failure"}');

        $this->helper->handleResponse($response);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testHandleResponsePropertyMessageMissing()
    {
        $response = $this->getMockedResponse('{"success": true}');

        $this->helper->handleResponse($response);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testHandleResponseFailure()
    {
        $response = $this->getMockedResponse('{"success": false, "message": "failure"}');

        $this->helper->handleResponse($response);
    }

    public function testHandleResponseSuccess()
    {
        $response = $this->getMockedResponse('{"success": "true", "message": "Success"}');

        $message = $this->helper->handleResponse($response);

        $this->assertEquals('Success', $message);
    }

    /**
     * Mock Response.
     *
     * @param string $body
     * @param int    $status
     *
     * @return \Mockery\MockInterface of HttpResponse
     */
    protected function getMockedResponse($body = null, $status = Response::HTTP_OK)
    {
        $response = \Mockery::mock(HttpResponse::class);

        $response
            ->shouldReceive('getStatusCode')
            ->andReturn($status)
        ;

        if (null !== $body) {
            $response
                ->shouldReceive('getBody')
                ->andReturn($body)
            ;
        }

        return $response;
    }
}
