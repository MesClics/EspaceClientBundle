<?php

namespace MesClics\EspaceClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Repository\ProjetRepository;
use MesClics\EspaceClientBundle\Form\ProjetType;
use MesClics\EspaceClientBundle\Form\ContratType;

class ContratAssocierProjetsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $client = $options['client'];
        
        $builder
        ->add('projets', EntityType::class, array(
            'class' => 'MesClicsEspaceClientBundle:Projet',
            'choice_label' => 'selectLabel',
            'query_builder' => function(ProjetRepository $projet_repo) use ($client){
                    return $projet_repo->getProjetsWithNoContratQB($client);
                }
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
            'data_class' => 'MesClics\EspaceClientBundle\Entity\Contrat'
        ));
        $resolver->setRequired(array(
            'client'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mesclics_espaceclientbundle_contratAssocierProjet';
    }


}
