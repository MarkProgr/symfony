<?php

namespace App\Command;

use App\Csv\CsvWriterFactory;
use App\Filesystem\FileUploader;
use App\Repository\ProductRepository;
use League\Csv\CannotInsertRecord;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('products:export', 'Exports products into CSV')]
class ExportProductsCommand extends Command
{
    public function __construct(
        private ProductRepository $productRepository,
        private FileUploader $uploader,
        private CsvWriterFactory $csv
    ) {
        parent::__construct();
    }

    /**
     * @throws CannotInsertRecord
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csv = $this->csv->build();

        $csv->insertOne(['id', 'name', 'description', 'manufacturer', 'price']);

        $products = $this->productRepository->findAll();

        foreach ($products as $product) {
            $csv->insertOne(
                [
                    $product->getId(),
                    $product->getName(),
                    $product->getDescription(),
                    $product->getManufacturer(),
                    $product->getPrice()
                ]
            );
        }

        $this->uploader->uploadFile(
            'products.csv',
            $csv
        );

        $output->writeln('Successfully exported');

        return Command::SUCCESS;
    }
}
