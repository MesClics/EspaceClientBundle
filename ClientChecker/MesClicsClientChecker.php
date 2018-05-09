<?php
    namespace MesClics\EspaceClientBundle\ClientChecker;
    
    use MesClics\EspaceClientBundle\Repository\ClientRepository;
    use MesClics\EspaceClientBundle\Client;

    class MesClicsClientChecker{
        private $clientsList;

        public function __construct(ClientRepository $clientRepository){
           $this->clientsList = $clientRepository->getClientsList();
        }

        /**
         * Vérifie si @client est bien un client enregistré. @clue correspond à un attribut de client qui peut être son alias, son nom, son numéro ou son id, sur lequel la recherche est effectuée. Par défaut on recherche un client par son numéro
         */
        public function isClient($clientToCheck, $clue = 'numero'){
            //On peut imaginer qu'on vérifie dans une liste de clients dans les paramètres de l'application          
            foreach($this->clientsList as $clientItem){
                $method = 'get'.ucfirst($clue);
                $clueToCheck = $clientItem->$method();
                if(strstr($clueToCheck, $clientToCheck)){
                    return $clientItem;
                }
            }
            return false;
        }
    }