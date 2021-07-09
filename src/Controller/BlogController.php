<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/blog/new_old", name="blog_create_old")
     */
    public function createOld(Request $request, EntityManagerInterface $manager): Response
    {
        // *********** REQUEST -> récupère les données du formulaire (INJECTION DE DEPENDANCE) :
        // la class REQUEST permet de stocker et d'avoir accès aux données véhiculées par les superglobales  ($_POST, $_GET, $_COOKIE, $_FILES etc...)
        dump($request);

        // *********** INSERTION des données ds la table SQL -> Article *********** //

        // *********** CONDITION à condition que le champs de saisi n'est pas vide
        // la propriété $request->request permet de stocker et d'accéder aux données saisie ds le formulaire, c'est à dire aux données de la superglobales $_POST
        // Si les données sont supérieurs à 0, donc si nous avons bien saisie des données dans le formulaire, alors on entre dans la condition IF
        if($request->request->count() > 0)
        {
            // *********** CREATION d'un objet $article pr recupérer les données et les envoyer ds la table SQL -> Article
            // Si nous voulons insérer des données dans la table SQL Article, nous devons instancier et remplir un objert issu de son entité correspondante (classe Article)
            $article = new Article;

            // *********** RECUPERATION DES DONNEES
            // On renseigne tout les setter (setTitre(), setContenu() etc...) de l'objet avec les données saisie ds le formulaire
            // $request->request->get("titre") : permet d'atteindre la valeur du titre saisi ds le champ "titre" du formulaire
            $article->setTitre($request->request->get("titre"))
                    ->setContenu($request->request->get("contenu"))
                    ->setImage($request->request->get("image"))
                    ->setDate(new \DateTime());

            // *********** PREPARATION DE LA REQUETE
            // Pour manipuler les lignes de la BDD (INSERT, UPDATE, DELETE), nous avons besoin d'un mamager (EntityManagerInterface) 
            // persist() : méthode issue de l'interface EntityManagerInterface permettant de préparer et garder en mémmoire la requete d'insertion
            // $data = $bdd->prepare("INSERT INTO article VALUE("$article->getTitre")")
            $manager->persist($article);

            // *********** ENREGISTREMENT en BDD
            // flush() : méthode issue de l'interface EntityManagerInterface permettant veritablement d'executer le requete d'insertion en BDD
            // $data->execute()
            $manager->flush();

            dump($article);

            // *********** REDIRECTION vers la page detail de l'article qu'on viens de créer
            // Après l'insertion de l'article en BDD, ns redirigeons l'internaute vers l'affichage du détail de l'article, dons une autre route via la méthode redirectToRoute()
            // Cette méthode attend 2 arguments 
            // 1. La route 
            // 2. le paramètre a transmettre dans la route, dans notre cas l'ID de l'article
            return $this->redirectToRoute("blog_show", [
                "id" => $article->getId()
            ]);
        }

        return $this->render("blog/create.html.twig");
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function create(Article $article = null, Request $request, EntityManagerInterface $manager): Response
    {
        // Si la variable $article N'EST PAS (null), si elle ne contient aucun article de la BDD, cela veut dire nous avons envoyé la route '/blog/new', c'est une insertion, on entre dans le IF et on crée une nouvelle instance de l'entité Article, création d'un nouvel article
        // Si la variable $article contient un article de la BDD, cela veut dire que nous avons envoyé la route '/blog/id/edit', c'est une modifiction d'article, on entre pas dans le IF, $article ne sera pas null, il contient un article de la BDD, l'article à modifier
        if(!$article) // Si l'article n'existe pas (donc $article = null)
        {
            $article = new Article;
        }

        // En renseignant les setter de l'entité, on s'aperçoit que les valeurs sont envoyés directement dans les attributs 'value' du formulaire, cela est dû au fait que l'entité $article est relié au formulaire
        // $article->setTitre("Titre bidon")
        //         ->setContenu("Contenu bidon");

        dump($request);

        // *********** 1. CREATION D'1 FORMULAIRE
        // createForm() : permet ici de créer un formulaire d'ajout d'article en fonction de la class ArticleType
        // En 2 ème argument de createForm(), nous transmettons l'objet entité $article afin de préciser que le formulaire a pour but de remplir l'objet $article, on relie l'entité au formulaire.
        $formArticle = $this->createForm(ArticleType::class, $article);

        // *********** 1. RECUPERE LES DONNEES saisie ds le FORMULAIRE
        // handleRequest() : permet ici ds notre cas, de récupérer toute les données saisie ds le formulaire et de les transmettre aux bon setter de l'entité $article
        // handleRequest() renseigne chaque setter de l'entité $article avec les données saisi dans le formulaire
        $formArticle->handleRequest($request);

        dump($article);

        // *********** 2. CONDITION (si le formul à bien été renseigné)
        // Si le formulaire a bien été validée && que toutes les données saisie sont bien transmise à la bonne entité, alors on entre ds la condition IF
        if($formArticle->isSubmitted() && $formArticle->isValid())
        {
            // on rendeigne le setter de la date, puisque nous n'avons pas de champ "date" ds le formulaire
            // Si l'article ne possède pas d'ID, alors on entre ds la condition IF et on execute le SETTER de la date. On entre dans le IF que ds le cas de la création d'un nouvel article
            if(!$article->getID())
            {
                $article->setDate(new \DateTime());
            }

            // *********** PREPARATION DE LA REQUETE D'INSERTION
             // Pour manipuler les lignes de la BDD (INSERT, UPDATE, DELETE), nous avons besoin d'un mamager (EntityManagerInterface) 
            // persist() : méthode issue de l'interface EntityManagerInterface permettant de préparer et garder en mémmoire la requete d'insertion
            // $data = $bdd->prepare("INSERT INTO article VALUE("$article->getTitre")")
            $manager->persist($article);
            
            // *********** INSERTION EN BDD
            // flush() : méthode issue de l'interface EntityManagerInterface permettant veritablement d'executer le requete d'insertion en BDD
            // $data->execute()
            $manager->flush();

            // *********** REDIRECTION sur 1 autre page à la validation du FORMULAIRE
            return $this->redirectToRoute("blog_show", [
                "id" => $article->getId()
            ]);
        }

        // *********** GENERER / AFFICHAGE du FORMULAIRE ds le template 
        return $this->render("blog/create2.html.twig", [
            "formArticle" => $formArticle->createView(), // on transmet le formulaire au template afin de pouvoir l'afficher avec TWIG
            // createView() va retourner un petit objet qui représente l'affichage du formulaire, on le récupère dans le template create2.html.twig
            "editMode" => $article->getID() // on transmet l'ID de l'article au template
        ]);
    }

    /**
     * Méthode permettant d'afficher le détail/ le contenu d'un article
     * 
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request): Response
    {
        // ***** AJOUTER -> Request $request ds les () de public function show pr récupérer les donnees du formulaire d'ajout de commentaire

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

        // ****** TRAITEMENT COMMENTAIRE ARTICLE (formulaire + insertion)
        // on instancie notre objet $comment
        $comment = new Comment; // importer la class app/Entity/Comment 

        // je creer mon formulaire et je le relie à une identité $comment
        $formComment = $this->createForm(CommentType::class, $comment);

        dump($request);

        $formComment->handleRequest($request); // on envois les donnée ds le setter de Comment

        dump($comment);

        // ****** ENVOIS les infos au TEMPLATE
        // render() : méthode qui permet d'envoyer les info receptionner au dessu
        return $this->render('blog/show.html.twig', [
            "articleBDD" => $article, // on transmet au template les données de l'article selectionné en BDD afin de les traiter avec le langage TWIG ds le template
            "formComment" => $formComment->createView() // affichage du formulaire pr les commentaires
        ]);
    }
}
