<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="admin_comment_index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository,Request $request, PaginatorInterface $paginator): Response
    {
        $data = $commentRepository->findAllSorted();
        $comments = $paginator->paginate($data, $request->query->getInt('page', 1), 5);

        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }


    /**
     * @Route("/{id}", name="admin_comment_delete", methods={"POST"})
     */
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/valider/{id}", name="admin_comment_validate")
     */
    public function validate(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $comment->setValid(!$comment->getValid());
        $entityManager->flush();

        return $this->redirectToRoute('admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
