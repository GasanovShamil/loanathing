<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnnounceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    'label' => 'Titre',
                    'constraints' => array(new NotBlank(array("message" => "Le titre est obligatoire")))
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'label' => 'Description',
                    'constraints' => array(new NotBlank(array("message" => "La description est obligatoire")))
                )
            )
            ->add(
                'startDate',
                TextType::class,
                array(
                    'label' => 'Début',
                    'attr' => array('class' => 'form-date-picker'),
                    'constraints' => array(new NotBlank(array("message" => "La date de début est obligatoire")))
                )
            )
            ->add(
                'endDate',
                TextType::class,
                array(
                    'label' => 'Fin',
                    'attr' => array('class' => 'form-date-picker'),
                    'constraints' => array(new NotBlank(array("message" => "La date de fin est obligatoire")))
                )
            )
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'label' => 'Type',
                    'constraints' => array(new NotBlank(array("message" => "Le type est obligatoire")))
                )
            )
            ->add(
                'tags',
                ChoiceType::class,
                array(
                    'label' => 'Tags',
                    'attr' => array('required' => false)
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
