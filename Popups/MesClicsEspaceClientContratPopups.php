<?php

namespace MesClics\EspaceClientBundle\Popups;

class MesClicsEspaceClientContratPopups{
    public static function onDelete(Array &$popups){
        $popups["delete"] = array(
            'options' => array(
                'illustration' => array(
                    'url' => '@mesclicsespaceclientbundle/images/icones/contrats/svg/remove.svg',
                    'alt' => 'illustration de suppression de contrat',
                    'title' => 'supprimer un contrat',
                    'type' => 'svg',
                    'class' => 'contrat-remove'
                ),
                'class' => 'alert'
            ),
            'template' => 'MesClicsEspaceClientBundle:PopUps:client-contrats-remove.html.twig'
        );
    }
}