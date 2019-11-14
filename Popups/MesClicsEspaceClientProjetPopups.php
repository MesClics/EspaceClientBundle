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
}