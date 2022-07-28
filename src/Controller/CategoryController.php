<?php


namespace App\Controller;


use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{

    /**
     * @Route("/category/sidebar", name="category.sidebar")
     */
    public function index(): Response
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        $categories = $categoryRepository->findAll();

        $output = array(); // array to count posts by Categories

        foreach($categories as $category)
        {
            $output[$category->getName()] = count($category->getPosts());
        }

        return $this->render('front/sidebar/sidebarCategory.twig', [
            'categories' => $categories,
            'output' => $output
        ]);
    }

}