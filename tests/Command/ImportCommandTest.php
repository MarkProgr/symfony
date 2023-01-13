<?php

namespace App\Tests\Command;

use App\Command\ImportCommand;
use App\Filesystem\FileUploader;
use App\Repository\ProductRepository;
use Goutte\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\DomCrawler\Link;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImportCommandTest extends TestCase
{
    private $productRepositoryMock;
    private $uploader;
    private $httpClient;
    private $commandTester;
    private $client;
    protected function setUp(): void
    {
        $this->productRepositoryMock = $this->createMock(ProductRepository::class);
        $this->uploader = $this->createMock(FileUploader::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->client = $this->createMock(Client::class);

        $application = new Application();
        $application->add(
            new ImportCommand(
                $this->productRepositoryMock,
                $this->uploader,
                $this->httpClient,
                $this->client
            )
        );
        $command = $application->find('products:import');
        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $requestCrawlerMock = $this->createMock(Crawler::class);

        $this->client->method('request')->willReturn($requestCrawlerMock);

        $filteredCrawlerMock = $this->createMock(Crawler::class);
        $requestCrawlerMock->method('filter')->willReturn($filteredCrawlerMock);
        $filteredCrawlerMock->method('links')->willReturn([$this->createMock(Link::class)]);

        $clickedCrawlerMock = $this->createMock(Crawler::class);

        $this->client->method('click')->willReturn($clickedCrawlerMock);

        $clickedCrawlerMock->method('image')->willReturn($this->createMock(Image::class));

        $this->uploader->expects($this->exactly(1))->method('uploadFile');

        $this->commandTester->execute([]);

        $this->assertEquals('Successfully imported', trim($this->commandTester->getDisplay()));
    }
}
