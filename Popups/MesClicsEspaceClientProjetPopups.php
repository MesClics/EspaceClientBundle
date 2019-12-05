<?php

namespace MesClics\EspaceClientBundle\Popups;

class MesClicsEspaceClientProjetPopups{


    public static function onDelete(Array &$popups){
        $popups['delete'] = array(        
            'options' => array(
                'illustration' => array(
                    'url' => '@mesclicsespaceclientbundle/images/icones/projets/svg/remove.svg',
                    'alt' => 'illustration de suppression de projet',
                    'title' => 'supprimer un projet',
                    'type' => 'svg',
                    'class' => 'projet-remove'
                ),
                'class' => 'alert'
            ),
            'template' => 'MesClicsEspaceClientBundle:PopUps:client-projets-remove.html.twig'
        );
    }

    public static function onDetach(Array &$popups){
        $popups['detach'] = array(
            'options' => array(
                'illustration' => array(
                    'url' => '@mesclicsespaceclientbundle/images/icones/projets/svg/detach.svg',
                    'alt' => 'illustration de dissociation de projet',
                    'title' => 'dissocier le projet',
                    'type' => 'svg',
                    'class' => 'projet-detach'
                ),
                'class' => 'alert'
            ),
            'template' => 'MesClicsEspaceClientBundle:PopUps:client-contrat-projets-detach.html.twig'
        );
    }

    public static function onAttach(Array &$popups){
        $popups['attach'] = array(
            'options' => array(
                'illustration' => array(
                    'url' => '@mesclicsespaceclientbundle/images/icones/projets/svg/attach.svg',
                    'alt' => 'illustration pour l\'association de projet',
                    'title' => 'associer le projet',
                    'type' => 'svg',
                    'class' => 'projet-attach'
                )
            ),
            'template' => 'MesClicsEspaceClientBundle:PopUps:client-projets-attach.html.twig'
        );
    }
}