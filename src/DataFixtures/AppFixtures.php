<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;
use App\Entity\Comment;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 8; $i++) {

            $product = new Post();
            $product->setTitle('Post n° '.$i);
            $product->setDescription('This is the post n° '.$i);
            $product->setContent('blabla bla blablabla blablabla');
            $product->setSlug('slug'.$i);
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setUpdatedAt(new \DateTimeImmutable());
            $product->setPublishedAt(new \DateTimeImmutable());

            $manager->persist($product);

            for ($j = 0; $j < 8; $j++) {

                $comment = new Comment();
                $comment->setUsername('personne '.$j);
                $comment->setContent('aled '.$j);
                $comment->setvalid(1);
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setPost($product);
                $manager->persist($comment);
            }

        }

        $tab = array("Insolite", "Tendance", "Couleur", "Forme", "Texture", "Gluten free");

        for ($l = 0; $l < 6; $l++) {

            $category = new Category();
            $category->setName($tab[$l]);
            $manager->persist($category);

        }



        $manager->flush();
    }
}
