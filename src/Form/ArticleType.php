<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // la fonction add permet de créer les champs du fomulaire
        $builder
            ->add('titre', TextType::class, [
                "required" => false,
                "attr" => [
                    "placeholder" => "Saisir le titre de l'article"
                ]
            ])
            // On définit le champ qui permet d'associer une catégorie à l'article ds le formulaire
            // Ce champ provient d'une autre entité : Category
            ->add("category", EntityType::class, [ // importer la class EntityType
                "class" => Category::class, // importer la class Category, on précise de quelle entité provient ce champ
                "choice_label" => "titre" // le contenu de la liste déroulante sera le titre des catégories
            ] ) 
            ->add("tags", EntityType::class, [ // EntityType -> champs select qui permet de selectionne ttes les données d'une entité
                "class" => Tag::class, // importer la class Tag, on précise de quelle entité provient ce champ
                "choice_label" => "name", // le contenu de la liste déroulante sera le titre des catégories
                "multiple" => true // multiple car on est sur ManyToMany donc plusieur choix
            ] ) 
            ->add('contenu', TextareaType::class, [
                "required" => false,
                "label" => "Contenu de l'article",
                "attr" => [
                    "placeholder" => "Saisir l'article",
                    "rows" => 15
                ]
            ])
            ->add('image', TextType::class, [
                "required" => false,
                "attr" => [
                    "placeholder" => "saisir l'URL de l'image"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
