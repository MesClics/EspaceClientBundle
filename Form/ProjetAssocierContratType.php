<?php

namespace MesClics\EspaceClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use MesClics\EspaceClientBundle\Repository\ContratRepository;

class ProjetAssocierContratType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $client = $options['client'];
        $client = $builder->getData()->getClient();
        $builder
            ->add('contrat', EntityType::class, array(
                'class' => 'MesClicsEspaceClientBundle:Contrat',
                'choice_label' => 'selectLabel',
                'query_builder' => function(ContratRepository $contrat_repo) use ($client){
                        return $contrat_repo->getContratsQB($client);
                    }
            ))
            ->add('associer', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MesClics\EspaceClientBundle\Entity\Projet'
        ));
        $resolver->setRequired(array(
            // 'client'
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mesclics_espaceclientbundle_projetAssocierContrat';
    }


}
