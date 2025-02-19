<?php

namespace App\Controller;

use App\Entity\Post;

use App\Form\PostType;
use App\Entity\Category;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageController extends AbstractController
{
    #[Route('/page', name: 'app_page')]
    public function index(PostRepository $pr): Response
    {
        return $this->render('page/index.html.twig', [
            'posts' => $pr->findAll(),
        ]);
    }
    #[Route('/p/{slug}', name: 'app_post_show')]
    public function show(PostRepository $pr, string $slug): Response
    {
        return $this->render('page/show.html.twig', [
            'post' => $pr->findOneBy(['slug' => $slug]),
        ]);
    }
    #[Route('/new', name: 'app_post_new')]
    public function new(EntityManagerInterface $em, Request $request): Response
    {
        $post = new Post();
        $category = new Category();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post
                ->setAuthor($this->getUser())
                ->setCategory($category);

            $em->persist($category);
            $em->persist($post);
            $em->flush();
        }

        return $this->render('page/new.html.twig', [
            'postForm' => $form->createView()
        ]);
    }
}
