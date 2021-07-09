<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Article; // on appel la class Article du fichier Article.php ds le dossier Entity

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // On importe la librairie Faker pour les fixtures, cela nous permet de créer des faux articles, catégories, commentaires plus évolués avec par exemple des faux noms, faux prénoms, date aléatoires etc...
        $faker = \Faker\Factory::create("fr_FR");

        // Création de 3 catégories
        for($cat = 1; $cat <= 3 ; $cat++)
        {
            $categorie = new Category; // /!\ ne pas oublier de faire import class pr importer la class Category

            // ******* RECUPERER
            $categorie->setTitre($faker->word)
                    ->setDescription($faker->paragraph());

            // ******* PREPARER
            // je prépare l'insertion de mes 3 catégories
            $manager->persist($categorie);

            // creation de 4 à 10 articles par catégories
            for($art = 1; $art <= mt_rand(4,10); $art++)
            {
                // $faker->paragraphs(5) retourne 1 ARRAY, setContenu attend une chaine de caractères en arguments 
                // join (alias implode) permet d'extraire chaque paragraphe faket afin de les rassembler en une chaine de caractères avec un séparateur (<p></p>)
                $contenu = '<p>' . join($faker->paragraphs(5), "</p><p>") . '</p>';

                $article = new Article;

                $article->setTitre($faker->sentence())
                        ->setContenu($contenu)
                        ->setImage($faker->imageUrl(600,600))
                        ->setDate($faker->dateTimeBetween("-6 months"))
                        ->setCategory($categorie);

                $manager->persist($article);

                // Création de 4 à 10 commentaire pour chaque article
                for($cmt =1; $cmt <= mt_rand(4,10); $cmt++)
                {
                    // ***** TRAITEMENT DES DATES
                    $now = new DateTime;
                    $interval = $now->diff($article->getDate()); // retourne un timestamp (temps en secondes) entre la date de création des articles et aujoud'hui

                    $days = $interval->days; // retourne le nb de jour entre la date de création des articles et aujourd'hui

                    $minimum = "-$days days"; /* -100 days | le but est d'avoir des dates de commentaires entre la date de création des articles et aujourd'hui */

                    // ***** TRAITEMENT DES PARAGRAPHES DE COMMENTAIRES
                    $contenu = '<p>' . join($faker->paragraphs(2), "</p><p>") . '</p>';

                    $comment = new Comment;

                    $comment->setAuteur($faker->name)
                            ->setCommentaire($contenu)
                            ->setDate($faker->dateTimeBetween($minimum)) // dateTimeBetween(-10 days)
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
