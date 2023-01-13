<?php

namespace App\Tests\Command;

use App\Command\ExportProductsCommand;
use App\Csv\CsvWriterFactory;
use App\Entity\Product;
use App\Filesystem\FileUploader;
use App\Message\ExportProduct;
use App\Repository\ProductRepository;
use League\Csv\Writer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class ExportProductsCommandTest extends TestCase
{
    private $productRepositoryMock;
    private $uploaderMock;
    private $commandTester;
    private $csvMock;
    private $busMock;
    protected function setUp(): void
    {
        $this->productRepositoryMock = $this->createMock(ProductRepository::class);
        $this->uploaderMock = $this->createMock(FileUploader::class);
        $this->csvMock = $this->createMock(CsvWriterFactory::class);
        $this->busMock = $this->createMock(MessageBusInterface::class);


        $application = new Application();
        $application->add(
            new ExportProductsCommand(
                $this->productRepositoryMock,
                $this->uploaderMock,
                $this->csvMock,
                $this->busMock
            )
        );
        $command = $application->find('products:export');
        $this->commandTester = new CommandTester($command);
    }

    public function testExecute()
    {
        $product = new Product();
        $product->setName('12');
        $product->setManufacturer('123');

        $writerMock = $this->createMock(Writer::class);
        $this->csvMock->method('build')->willReturn($writerMock);

        $this->productRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$product]);

        $this->busMock
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn(
                new Envelope(
                    new ExportProduct(
                        $product->getName(),
                        $product->getManufacturer()
                    )
                )
            );

        $writerMock->expects($this->exactly(2))->method('insertOne');

        $this->uploaderMock->expects($this->once())->method('uploadFile');

        $this->commandTester->execute([]);

        $this->assertEquals('Successfully exported', trim($this->commandTester->getDisplay()));
    }
}
