<?php

namespace App\Command;

use App\Entity\Product;
use App\Filesystem\FileUploader;
use App\Repository\ProductRepository;
use Goutte\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand('products:import', 'Imports products from Amazon')]
class ImportCommand extends Command
{
    public function __construct(
        private ProductRepository $productRepository,
        private FileUploader $uploader,
        private HttpClientInterface $httpClient,
        private Client $client,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $crawler = $this->client->request(
            'GET',
            'https://sneaker-head.by/catalog/muzskoe/krossovki',
        );

        $links = $crawler->filter('.good-item__title')->links();

        foreach ($links as $link) {
            $crawler = $this->client->click($link);

            $product = new Product();
            $product->setName($crawler->filter('.good__title')->html());

            $manufacturer = $crawler->filter('.col-md-6 > a')->attr('href');
            $product->setManufacturer(substr($manufacturer, 7));

            $description = $crawler->filter('.good__title')->html();
            $product->setDescription($description);

            $price = $crawler->filter('.good__prices-new')->html();
            $product->setPrice(substr($price, 0, 3));

            $this->productRepository->save($product, true);

            $image = $crawler->filter('.swiper-wrapper img')->image();
            $fileInfo = new \finfo(FILEINFO_MIME_TYPE);

            $request = $this->httpClient->request('GET', $image->getUri());

            $this->uploader->uploadFile(
                sha1($image->getUri()) . '.' . substr($fileInfo->buffer($request->getContent()), 6),
                $request->getContent()
            );
        }

        $output->writeln('Successfully imported');

        return Command::SUCCESS;
    }
}
