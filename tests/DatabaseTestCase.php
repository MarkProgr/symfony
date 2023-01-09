<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class DatabaseTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    private Application $application;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->application = new Application(static::$kernel);
        $this->runCommand('doctrine:database:drop', [
            '--force' => true,
            '--if-exists' => true,
        ]);
        $this->runCommand('doctrine:database:create', [
            '--if-not-exists' => true,
        ]);
        $this->runCommand('doctrine:migrations:migrate');
        $this->runCommand('fos:elastica:delete');
        $this->runCommand('fos:elastica:create');
    }

    protected function runCommand(string $command, array $input = [])
    {
        $command = $this->application->find($command);
        $input = new ArrayInput($input);
        $input->setInteractive(false);
        $command->run($input, new NullOutput());
    }
}
