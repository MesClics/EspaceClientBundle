<?php

namespace MesClics\EspaceClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ProjetType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use MesClics\EspaceClientBundle\Repository\ProjetRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ContratType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $client = $builder->getData()->getClient();
        $builder
        ->add('type', TextType::class)
        ->add('dateSignature', DateTimeType::class, array('required' => false))
        ->add('projets', EntityType::class, array(
            'class' => 'MesClicsEspaceClientBundle:Projet',
            'query_builder' => function(ProjetRepository $repo) use($client){
                return $repo->getProjetsWithNoContratQB($client);
            },
            'property_path' => 'projets',
            'choice_label' => function(Projet $projet){
                return $projet->getSelectLabel();
            },
            'choice_attr' => function(Projet $projet, $key, $index){
                return ['class' => 'oocss-form-input-button',
                        'title' => $projet->getNom()];
            },
            'expanded' => false,
            'multiple' => true,
            'required' => false
        ))
        ->add('submit', SubmitType::class)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MesClics\EspaceClientBundle\Entity\Contrat',
            // 'projet' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mesclics_espaceclientbundle_contrat';
    }


}
