<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Application\UseCase\ParseProductUseCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;

class ProductController extends AbstractController
{
    /**
     * Lists all products on the main page.
     *
     * @param ProductRepository $productRepository
     * @return Response
     */
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * Creates a new product.
     *
     * If an external URL (e.g. from alza.cz) is provided in the form, the
     * ParseProductUseCase is used to prefill product data for any empty fields.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return Response
     */
    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): Response {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = $validator->validate($product);
            
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                
                if ($request->headers->get('Turbo-Frame')) {
                    return $this->render('product/new.html.twig', [
                        'product' => $product,
                        'form' => $form->createView(),
                    ]);
                }
                return $this->redirectToRoute('app_product_new');
            }

            if ($form->isValid()) {
                $entityManager->persist($product);
                $entityManager->flush();

                $this->addFlash('success', 'Продукт успешно создан');
                return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a single product.
     *
     * @param Product $product
     * @return Response
     */
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * Edits an existing product.
     *
     * @param Request $request
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): Response {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = $validator->validate($product);
            
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                
                if ($request->headers->get('Turbo-Frame')) {
                    return $this->render('product/edit.html.twig', [
                        'product' => $product,
                        'form' => $form->createView(),
                    ]);
                }
                return $this->redirectToRoute('app_product_edit', ['id' => $product->getId()]);
            }

            if ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Продукт успешно обновлен');
                return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a product.
     *
     * @param Request $request
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager
    ): Response {
        try {
            if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
                $entityManager->remove($product);
                $entityManager->flush();
                $this->addFlash('success', 'Продукт успешно удален');
            } else {
                $this->addFlash('error', 'Неверный CSRF токен');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Ошибка при удалении продукта');
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/parse-product', name: 'app_product_parse', methods: ['POST'])]
    public function parseProduct(
        Request $request,
        ParseProductUseCase $parseProductUseCase,
        ValidatorInterface $validator
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            $url = $data['url'] ?? null;

            if (!$url) {
                throw new \Exception('URL не указан');
            }

            // Валидация URL
            $urlConstraint = new Assert\Url();
            $urlErrors = $validator->validate($url, $urlConstraint);
            
            if (count($urlErrors) > 0) {
                throw new \Exception('Неверный формат URL');
            }

            // Проверка домена alza.cz
            if (!str_contains($url, 'alza.cz')) {
                throw new \Exception('URL должен быть с домена alza.cz');
            }

            $parsedProduct = $parseProductUseCase->execute($url);

            return new JsonResponse([
                'name' => $parsedProduct->getName(),
                'price' => $parsedProduct->getPrice(),
                'photo' => $parsedProduct->getPhoto()
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
