<?php

namespace MesClics\EspaceClientBundle\Popups;

class MesClicsEspaceClientClientPopups{
    
    public static function onRemoval(array &$popups){
        $popups["delete"] = array(
            "options" => array(
                "illustration" => array(
                    'url' => '@mesclicsespaceclientbundle/images/icones/clients/svg/remove.svg',
                    'alt' => 'illustration de suppression de client',
                    'title' => 'supprimer un client',
                    'type' => 'svg',
                    'class' => 'client-remove'
                    ),
                "class" => "alert"
            ),
            "template" => "MesClicsEspaceClientBundle:PopUps:client-remove.html.twig"
        );
    }
}