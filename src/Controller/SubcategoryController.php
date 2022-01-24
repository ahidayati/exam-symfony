<?php

namespace App\Controller;

use App\Entity\SubCategory;
use App\Form\SubCategoryFormType;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/subcategory')]
class SubcategoryController extends AbstractController
{
    private SubCategoryRepository $subCategoryRepository;
    private EntityManagerInterface $em;
    private PaginatorInterface $paginator;

    /**
     * @param SubCategoryRepository $subCategoryRepository
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     */
    public function __construct(SubCategoryRepository $subCategoryRepository, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->subCategoryRepository = $subCategoryRepository;
        $this->em = $em;
        $this->paginator = $paginator;
    }


    #[Route('/add', name: 'subcategory_add')]
    public function addSubcategory(Request $request): Response
    {
        $subcategoryEntity = new SubCategory ();
        $form = $this->createForm(SubCategoryFormType::class, $subcategoryEntity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($subcategoryEntity);
            $this->em->flush();
            return $this->redirectToRoute('category_view', ['id' => $subcategoryEntity->getCategory()->getId()]);
        }

        return $this->render('subcategory/add.html.twig', [
            'subcategoryForm' => $form->createView(),
        ]);
    }

    #[Route('/view/{id}', name: 'subcategory_view')]
    public function viewSubcategory(int $id): Response
    {

        return $this->render('subcategory/view.html.twig', [
            'subcategoryView' => $this->subCategoryRepository->find($id),
        ]);
    }
}