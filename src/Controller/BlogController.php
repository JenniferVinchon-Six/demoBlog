<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * Méthode permettant d'afficher l'ensemble des articles du blog
     * 
     * @Route("/blog", name="blog")
     */
    public function blog(): Response
    {
        // traitements requete selection BDD des articles
        // pr exécuter une requete de SELECTION j'ai besoin d'appeler la class Article du fichier ArticleRepository ds le dossier Repository
        // $repoArticles : est un objet issu de la class ArticleRepository
        $repoArticles = $this->getDoctrine()->getRepository(Article::class);
        dump($repoArticles);// dump(): outil de debug, on affiche se que contient $repoArticles

        $articles = $repoArticles->findAll();
        dump($articles);

        return $this->render('blog/blog.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    /**
     * Méthode permettant d'afficher la page d'accueil du blog Symfony
     * 
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig', [
            "title" => "Blog dédié à la musique, venez voir mais surtout écouter !!!",
            "age" => 25
        ]);
    }

    /**
     * Méthode permettant d'afficher le détail/ le contenu d'un article
     * 
     * @Route("/blog/12", name="blog_show")
     */
    public function show(): Response
    {
        return $this->render('blog/show.html.twig');
    }
}
