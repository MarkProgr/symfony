<?php

namespace App\Tests\Command;

use App\Command\ExportProductsCommand;
use App\Csv\CsvWriterFactory;
use App\Entity\Product;
use App\Filesystem\FileUploader;
use App\Repository\ProductRepository;
use League\Csv\Writer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ExportProductsCommandTest extends TestCase
{
    private $productRepositoryMock;
    private $uploaderMock;
    private $commandTester;
    private $csvMock;
    protected function setUp(): void
    {
        $this->productRepositoryMock = $this->createMock(ProductRepository::class);
        $this->uploaderMock = $this->createMock(FileUploader::class);
        $this->csvMock = $this->createMock(CsvWriterFactory::class);

        $application = new Application();
        $application->add(new ExportProductsCommand($this->productRepositoryMock, $this->uploaderMock, $this->csvMock));
        $command = $application->find('products:export');
        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $writerMock = $this->createMock(Writer::class);
        $this->csvMock->method('build')->willReturn($writerMock);

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([new Product()]);

        $writerMock->expects($this->exactly(2))->method('insertOne');

        $this->uploaderMock->expects($this->once())->method('uploadFile');

        $this->commandTester->execute([]);

        $this->assertEquals('Successfully exported', trim($this->commandTester->getDisplay()));
    }
}
