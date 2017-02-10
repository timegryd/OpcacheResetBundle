<?php

namespace Timegryd\OpcacheResetBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use GuzzleHttp\Client as HttpClient;
use Timegryd\OpcacheResetBundle\Helper\OpcacheResetCommandHelper;

class OpcacheResetCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    protected $defaultHost;

    /**
     * @var string
     */
    protected $defaultDir;

    /**
     * @var OpcacheResetCommandHelper
     */
    protected $helper;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @param string $defaultHost
     * @param string $defaultDir
     */
    public function __construct($defaultHost, $defaultDir, OpcacheResetCommandHelper $opcacheResetCommandHelper, HttpClient $httpClient)
    {
        $this->defaultHost = $defaultHost;
        $this->defaultDir = $defaultDir;
        $this->helper = $opcacheResetCommandHelper;
        $this->httpClient = $httpClient;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('opcache:reset')
            ->setDescription('Reset opcache')
            ->addArgument('host', InputArgument::OPTIONAL, 'Base url to call', $this->defaultHost)
            ->addArgument('dir', InputArgument::OPTIONAL, 'Web dir where to create file to call', $this->defaultDir)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['Opcache reset begin', '==================='], OutputInterface::VERBOSITY_VERBOSE);

        $token = sha1(uniqid());

        $this->helper->createFile($input->getArgument('dir'), $token);
        $output->writeln('File created', OutputInterface::VERBOSITY_VERBOSE);

        $url = $this->helper->generateUrl($input->getArgument('host'), $token);

        $response = $this->httpClient->request('GET', $url);

        $this->helper->clean($input->getArgument('dir'));
        $output->writeln('File deleted', OutputInterface::VERBOSITY_VERBOSE);

        $output->writeln($this->helper->handleResponse($response), OutputInterface::VERBOSITY_VERBOSE);
    }
}
