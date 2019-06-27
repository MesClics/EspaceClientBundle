<?php

namespace MesClics\EspaceClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use MesClics\EspaceClientBundle\Entity\Projet;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use MesClics\EspaceClientBundle\Repository\ContratRepository;

class ProjetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // if($formKind === 'ajoutProjet'){
            $builder
                ->add('type', TextType::class)
                ->add('nom', TextType::class)
                ->add('isClosed', CheckboxType::class, array('required'=> false))
                ->add('submit', SubmitType::class)
            ;
        // }

        // if($formKind === 'associationContratToProjet'){
        //     $builder
        //         ->add('contrat', EntityType::class, array(
        //             'class' => 'MesClicsEspaceClientBundle:Contrat',
        //             'choice_label' => 'selectLabel',
        //             'query_builder' => function(ContratRepository $contrat_repo) use ($client){
        //                     return $contrat_repo->getContratsQB($client);
        //                 }
        //         ))
        //         ->add('associer', SubmitType::class)
        //     ;
        // }

        // if($formKind === 'dissociationContrat'){
        //     $builder
        //         ->add('dissocier', SubmitType::class)
        //     ;
        // }
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Projet::class
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mesclics_espaceclientbundle_projet';
    }


}
