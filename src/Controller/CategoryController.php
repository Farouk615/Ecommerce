<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->json($categoryRepository->findAll());
    }

    #[Route('/new', name: 'app_category_new', methods: ['POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $em): JsonResponse
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $data = json_decode($request->getContent(), true);
        $form->submit($data['category']);
        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);
            $em->flush();
            return new JsonResponse(['status' => 'Category created'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['status' => 'Category not created'], Response::HTTP_CREATED);    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->json($category);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository,
                         EntityManagerInterface $em): JsonResponse
    {
        $form = $this->createForm(CategoryType::class, $category);
        $data = json_decode($request->getContent(), true);
        $form->submit($data['category']);
        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);
            $em->flush();
            return new JsonResponse(['status' => 'Category updated'], Response::HTTP_OK);
        }
        return new JsonResponse(['status' => 'Category updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
