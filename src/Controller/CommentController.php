<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{

    /**
     * @Route("/comment/sidebar", name="comment.sidebar")
     */
    public function index(): Response
    {
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);

        // get all valid comments, sorted by publication date
        $comments = $commentRepository->findBy(['valid' => 1], ['createdAt' => 'DESC'], 5);

        return $this->render('front/sidebar/sidebarComment.html.twig', [
            'comments' => $comments
        ]);
    }

}