<?php
namespace MesClics\EspaceClientBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use MesClics\EspaceClientBundle\Form\ClientType;

class ClientEditType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
        ->remove('ajouter')
        ->add('modifier', SubmitType::class);
    }

    public function getParent(){
        return ClientType::class;
    }
}