<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Config\Framework\RequestConfig;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Knp\Component\Pager\PaginatorInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

class PostController extends AbstractController
{

    /**
     * @Route("/post", name="post")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $postRepository = $this->getDoctrine()->getRepository(Post::class);

        // get all valid posts (publishedAt <= now)
        $datas = $postRepository->findValid();

        $listPost = $paginator->paginate($datas, $request->query->getInt('page', 1), 5);

        if(!$listPost) {
            throw $this->createNotFoundException(
                "Erreur - liste des Posts"
            );
        }

        return $this->render('front/post/display.html.twig', [
            'listPost' => $listPost
        ]);
    }

    /**
     * @Route("/post/category/{id}", name="post.category")
     */
    public function postByCategory(Request $request, PaginatorInterface $paginator, int $id): Response
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        $category = $categoryRepository->findOneBy(['id' => $id]);

        $postRepository = $this->getDoctrine()->getRepository(Post::class);

        $datas = $category->getPosts();

        $listPost = $paginator->paginate($datas, $request->query->getInt('page', 1), 5);

        if(!$listPost) {
            throw $this->createNotFoundException(
                "Erreur - liste des Posts"
            );
        }

        return $this->render('front/post/display.html.twig', [
            'listPost' => $listPost
        ]);
    }


    /**
     * @Route("/post/{slug}", name="post.show")
     */
    public function show(string $slug, Request $request): Response
    {
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $post = $postRepository->findOneBy(['slug' => $slug]);

        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $commentRepository->findBy([
            'post' => $post,
            'valid' => 1
        ]);

        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        if($commentForm->isSubmitted() && $commentForm->isValid())
        {
            $comment = $commentForm->getData();
            $comment->setPost($post);
            $comment->setValid(0); // need to be validate by moderator
            $comment->setCreatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('post.show', ['slug' => $post->getSlug()]);
        }

        if(!$post) {
            throw $this->createNotFoundException(
                "Erreur - post introuvable"
            );
        }

        return $this->render('front/post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'commentForm' => $commentForm->createView()
        ]);
    }

    /**
     * @Route("/post/edit/{id}")
     */
    public function edit(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);

        if(!$post) {
            return $this->createNotFoundException();
        }

        $postForm = $this->createForm(PostType::class, $post);

        $postForm->handleRequest($request);

        if($postForm->isSubmitted() && $postForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
        }

        return $this->render('front/post/create.html.twig', [
            'postForm' => $postForm->createView()
        ]);
    }

}