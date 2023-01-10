<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    private PaginatedFinderInterface $finder;

    private CacheItemPoolInterface $cache;

    public function __construct(PaginatedFinderInterface $finder, CacheItemPoolInterface $cache)
    {
        $this->finder = $finder;
        $this->cache = $cache;
    }

    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(
        ProductRepository $productRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $cachingTemplate = $this->cache->getItem('index page ' . $request->query->getInt('page'));

        $renderingTemplate = $this->render(
            'product/index.html.twig',
            [
                'products' => $paginator->paginate(
                    $productRepository->findAll(),
                    $request->query->getInt('page', 1),
                    3
                ),
            ]
        );

        if ($cachingTemplate->isHit() && $renderingTemplate === $cachingTemplate->get()) {
            return $cachingTemplate->get();
        }

        $this->cache->save(
            $cachingTemplate
                ->set($renderingTemplate)
                ->expiresAfter(\DateInterval::createFromDateString('1 minute'))
        );

        return $renderingTemplate;
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm(
            'product/new.html.twig',
            [
            'product' => $product,
            'form' => $form,
            ]
        );
    }

    #[Route('/find', name: 'app_product_find_form', methods: ['GET'])]
    public function findForm(): Response
    {
        $cachingTemplate = $this->cache->getItem('products finding form');

        if ($cachingTemplate->isHit()) {
            return $cachingTemplate->get();
        }

        $this->cache->save(
            $cachingTemplate
                ->set($this->render('product/find_form.html.twig'))
                ->expiresAfter(\DateInterval::createFromDateString('1 minute'))
        );

        return $this->render('product/find_form.html.twig');
    }

    #[Route('/find', name: 'app_product_find', methods: ['POST'])]
    public function find(Request $request): Response
    {
        $searchValue = $request->get('value');

        $products = $this->finder->find($searchValue);

        $cachingTemplate = $this->cache->getItem('searching value - ' . $searchValue);

        $renderingTemplate = $this->render('product/founded_products.html.twig', compact('products'));

        if ($cachingTemplate->isHit() && $renderingTemplate === $cachingTemplate->get()) {
            return $cachingTemplate->get();
        }

        $this->cache->save(
            $cachingTemplate
                ->set($renderingTemplate)
                ->expiresAfter(\DateInterval::createFromDateString('1 minute'))
        );

        return $renderingTemplate;
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        $cachingTemplate = $this->cache->getItem('showed product ' . $product->getId());

        $renderingTemplate = $this->render(
            'product/show.html.twig',
            [
                'product' => $product,
            ]
        );

        if ($cachingTemplate->isHit() && $renderingTemplate === $cachingTemplate->get()) {
            return $cachingTemplate->get();
        }

        $this->cache->save(
            $cachingTemplate
                ->set($renderingTemplate)
                ->expiresAfter(\DateInterval::createFromDateString('1 minute'))
        );

        return $renderingTemplate;
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm(
            'product/edit.html.twig',
            [
            'product' => $product,
            'form' => $form,
            ]
        );
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
