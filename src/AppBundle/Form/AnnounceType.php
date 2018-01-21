<?php

namespace AppBundle\Form;

use AppBundle\Entity\Tag;
use AppBundle\Entity\Type;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnounceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                EntityType::class,
                array(
                    'label' => 'Type',
                    'class' => Type::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('t')
                            ->orderBy('t.id', 'ASC');
                    },
                    'choice_label' => 'label'
                )
            )
            ->add(
                'tag',
                EntityType::class,
                array(
                    'label' => 'Tag',
                    'class' => Tag::class,
                    'attr' => array('placeholder' => 'Choisir un tag'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('t')
                            ->orderBy('t.label', 'ASC');
                    },
                    'choice_label' => 'label'
                )
            )
            ->add(
                'name',
                TextType::class,
                array(
                    'label' => 'Titre'
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'label' => 'Description'
                )
            )
            ->add(
                'file',
                FileType::class,
                array(
                    'label' => 'Image (.png)'
                )
            )
            ->add(
                'startDate',
                TextType::class,
                array(
                    'label' => 'Début',
                    'attr' => array('class' => 'date-picker')
                )
            )
            ->add(
                'endDate',
                TextType::class,
                array(
                    'label' => 'Fin',
                    'attr' => array('class' => 'date-picker')
                )
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Announce'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_announce';
    }
}