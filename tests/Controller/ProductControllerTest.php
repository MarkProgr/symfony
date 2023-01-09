<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Tests\DatabaseTestCase;

class ProductControllerTest extends DatabaseTestCase
{
    private ProductRepository $repository;

    private string $path = '/product/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::$kernel->getContainer()->get('doctrine')->getRepository(Product::class);
    }

    public function testIndex(): void
    {
        $fixture = new Product();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setManufacturer('My Title');
        $fixture->setPrice(2000);

        $this->repository->save($fixture, true);

        $this->client->request('GET', $this->path);
        self::assertSelectorExists('.product');
        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Product index');
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'product[name]' => 'Testing',
            'product[description]' => 'Testing',
            'product[manufacturer]' => 'Testing',
            'product[price]' => 1000,
        ]);

        self::assertResponseRedirects('/product/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $fixture = new Product();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setManufacturer('My Title');
        $fixture->setPrice(1000);

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Product');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $fixture = new Product();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setManufacturer('My Title');
        $fixture->setPrice(2000);

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'product[name]' => 'Something New',
            'product[description]' => 'Something New',
            'product[manufacturer]' => 'Something New',
            'product[price]' => '2000',
        ]);

        self::assertResponseRedirects('/product/');

        $fixture = $this->repository->find($fixture->getId());

        self::assertSame('Something New', $fixture->getName());
        self::assertSame('Something New', $fixture->getDescription());
        self::assertSame('Something New', $fixture->getManufacturer());
        self::assertSame('2000', $fixture->getPrice());
    }

    public function testRemove(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Product();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setManufacturer('My Title');
        $fixture->setPrice(2000);

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/product/');
    }

    public function testFind()
    {
        $this->client->request('GET', sprintf('%snew', $this->path));
        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'product[name]' => 'My Title',
            'product[description]' => 'My Title',
            'product[manufacturer]' => 'My Title',
            'product[price]' => 1000,
        ]);

        sleep(10);

        $this->client->request('POST', $this->path . 'find', ['value' => 'My Title']);

        self::assertSelectorExists('.product');
        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Founded products');
    }
}
