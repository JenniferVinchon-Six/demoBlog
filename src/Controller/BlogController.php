<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
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
    public function blog(ArticleRepository $repoArticles): Response
    {
        // ********* CREER LE LIEN avc ArticleRepository.php et DONC la BDD ********* //
        // traitements requete selection BDD des articles
        // pr exécuter une requete de SELECTION j'ai besoin d'appeler la class Article du fichier ArticleRepository ds le dossier Repository
        // la class Repository permet uniquement de formuler et d'executer des requete SQL de selection (SELECT)
        // Cette classe contient des méthodes mis à disposition par Symfony pour formuler et executer des requetes SQL en BDD
        // $repoArticles : est un objet issu de la class ArticleRepository
        // getRepository() : méthode permettant d'importer la class REpository d'une entité
        // $repoArticles = $this->getDoctrine()->getRepository(Article::class);

        // Contrôle :
        dump($repoArticles);// dump(): outil de debug propre à Symfony, on affiche se que contient $repoArticles

        // ********* SELECTIONNE ds la BDD des ARTICLES ********* //
        // findAll(): SELECT * FROM article + FETCHALL
        // $articles : tableau ARRAY multidimentionnel contenant l'ensemble des articles stockés ds la BDD
        $articles = $repoArticles->findAll();
        dump($articles); // dump(): outil de debug propre à Symfony, on affiche se que contient $articles

        // ********* AFFICHE les ARTICLES s/ LE TEMPLATE ds ma page et donc le navigateur ********* //
        return $this->render('blog/blog.html.twig', [
            'articlesBDD' => $articles // via la méthode render() on transmet au template les articles que nous avons selectionnés en BDD afin de les traités et de les afficher avec le langage TWIG
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
     * Méthode permettant de CREER un nouvel article et de MODIFIER un article existant
     * 
     * @Route("/blog/new", name="blog_create")
     */
    public function create(): Response
    {
        return $this->render("blog/create.html.twig");
    }

    /**
     * Méthode permettant d'afficher le détail/ le contenu d'un article
     * 
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article): Response
    {
        // ds notre methode on a accès a l'id de l'article
        // dump($id); // id transmis ds l'URL envoyer en argument à la fonction (show)

        // ****** IMPORTATION de la class ArticleRepository
        // $repoArticle1 = $this->getDoctrine()->getRepository(Article::class);
        
        // dump($repoArticle1);

        // ****** RECUPERE les données en BDD de l'aticle cliqué
        // find() : méthode mise à disposition par Symfony issue de la class ArticleRepository.php permettant de selectionner un élément de la BDD par son ID
        // $article : tableau ARRAY contenant toutes les données de l'article selectionné en BDD en fonction de l'ID transmit dans l'URL
        // SELECT * FROM article WHERE id = 6 + FETCH
        // $article = $repoArticle1->find($id);
        dump($article);
        
        // ****** ENVOIS les infos au TEMPLATE
        // render() : méthode qui permet d'envoyer les info receptionner au dessu
        return $this->render('blog/show.html.twig', [
            "articleBDD" => $article // on transmet au template les données de l'article selectionné en BDD afin de les traiter avec le langage TWIG ds le template
        ]);
    }
}
