<?php

namespace App\DataFixtures;

use App\Entity\Article; // on appel la class Article du fichier Article.php ds le dossier Entity
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // La boucle FOR tourne 10 fois car nous voulons créer 10 articles
        for($i =1; $i <= 11; $i++)
        {
            // Pour pouvoir insérer des données ds la table SQL article, nous devons instancier son entité correspondante (Article du fichier Article.php ds le dossier Entity), Symfony se sert l'objet entité $ article pour injecter les valeurs ds les requetes SQL
            $article = new Article;

            // On fait appel aux setteurs de l'objet entité afin de renseigner les titre, les contenu, les images et les dates des faux articles stockés en BDD
            $article->setTitre("Titre de l'article $i")
                    ->setContenu("<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin sagittis neque diam, eu lacinia metus ultricies et. Pellentesque lobortis velit id commodo vestibulum. Nulla ut rutrum dui. Nulla quis malesuada neque. Praesent et nulla a eros finibus hendrerit et non erat. Proin varius mauris et lorem pharetra elementum. Pellentesque faucibus enim nec tempor lobortis. Duis laoreet elementum mauris, nec porta ex scelerisque ullamcorper. Proin sodales a urna nec condimentum. Nulla purus augue, gravida et lacus convallis, scelerisque tincidunt justo. Donec dictum mauris urna, id tempus dui pharetra at. Nunc eget vehicula quam.</p>")
                    ->setImage("https://picsum.photos/600/600?grayscale")
                    ->setDate(new \DateTime());

            // Un manager (objectManager) en Symfony est une class permettant, entre autre, de manipuler les lignes de la BDD (INSERT, UPDATE, DELETE)

             // persist() : méthode issue de la classe ObjectManager permettant de préaprer et de garder en méméoire les requetes d'insertion
            // persist() => donne en PHP -> $data =$bdd->prepare("INSERT INTO article VALUES("getTitre()"))
            $manager->persist($article);
        }

        // flush() : méthode issue de la classe ObjectManager permettant véritablement d'executer les requetes d'insertions en BDD
        $manager->flush(); // égale au execute() en PHP
    }
}
