<?php

namespace Timegryd\OpcacheResetBundle\Helper;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use GuzzleHttp\Psr7\Response as HttpResponse;

class OpcacheResetCommandHelper
{
    const FILE_NAME_MODEL = 'opcache-reset-%s.php';

    /**
     * @var KernelInterface
     */
    protected $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Return generated file name.
     *
     * @param string $replacement
     *
     * @return string
     */
    public function getFileName($replacement)
    {
        return sprintf(self::FILE_NAME_MODEL, $replacement);
    }

    /**
     * Return generated file path.
     *
     * @param string $dir
     * @param string $replacement
     *
     * @return string
     */
    public function getFilePath($dir, $replacement)
    {
        return sprintf('%s/%s', $dir, $this->getFileName($replacement));
    }

    /**
     * Generate url with given host and token.
     *
     * @param string $host
     * @param string $token
     *
     * @return string
     */
    public function generateUrl($host, $token)
    {
        return sprintf('%s/%s', $host, $this->getFileName($token));
    }

    /**
     * Create file in given directory.
     *
     * @param string $dir
     * @param string $token
     *
     * @return OpcacheResetCommandHelper
     */
    public function createFile($dir, $token)
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException(sprintf('Web directory does not exists : %s', $dir));
        }

        $templatePath = $this->kernel->locateResource('@TimegrydOpcacheResetBundle/Resources/template/opcacheReset.php');

        $fileSystem = new FileSystem();

        $fileSystem->copy(
            $templatePath,
            $this->getFilePath($dir, $token)
        );

        return $this;
    }

    /**
     * Remove all opcache reset files.
     *
     * @param string $dir
     *
     * @return OpcacheResetCommandHelper
     */
    public function clean($dir)
    {
        array_map('unlink', glob($this->getFilePath($dir, '*')));

        return $this;
    }

    /**
     * Handle given response and return message from it.
     *
     * @param HttpResponse $response
     *
     * @return string
     */
    public function handleResponse(HttpResponse $response)
    {
        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new \RuntimeException('Unable to access url');
        }

        $result = json_decode($response->getBody());

        if (null === $result) {
            throw new \RuntimeException('Unable to decode json response.');
        }

        if (!property_exists($result, 'success')) {
            throw new \InvalidArgumentException('"success" property is missing in returned json.');
        }

        if (!property_exists($result, 'message')) {
            throw new \InvalidArgumentException('"message" property is missing in returned json.');
        }

        if (false === $result->success) {
            throw new \RuntimeException($result->message);
        }

        return $result->message;
    }
}
