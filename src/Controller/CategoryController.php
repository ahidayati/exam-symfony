<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    private PaginatorInterface $paginator;
    private CategoryRepository $categoryRepository;
    private EntityManagerInterface $em;

    /**
     * @param PaginatorInterface $paginator
     * @param CategoryRepository $categoryRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(PaginatorInterface $paginator, CategoryRepository $categoryRepository, EntityManagerInterface $em)
    {
        $this->paginator = $paginator;
        $this->categoryRepository = $categoryRepository;
        $this->em = $em;
    }


    #[Route('/', name: 'category_index')]
    public function indexCategory(Request $request): Response
    {
        $queryBuilder = $this->categoryRepository->getQbAll();
        $categoryList = $this->paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 3);

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categoryList,
        ]);
    }

    #[Route('/add', name: 'category_add')]
    public function addCategory(Request $request): Response
    {
        $categoryEntity = new Category ();
        $form = $this->createForm(CategoryFormType::class, $categoryEntity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($categoryEntity);
            $this->em->flush();
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/add.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }

    #[Route('/view/{id}', name: 'category_view')]
    public function viewCategory(int $id): Response
    {

        return $this->render('category/view.html.twig', [
            'categoryView' => $this->categoryRepository->find($id),
        ]);
    }

}