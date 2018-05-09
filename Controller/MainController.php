<?php
namespace MesClics\EspaceClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MesClics\EspaceClientBundle\Entity\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_USER') and ")
 */
class MainController extends Controller{
    public function indexAction($client, $section, Request $request){
        // if(!$this->get('security.authorization_checker')->isGranted('ROLE_CLIENT')){
        //     throw new AccessDeniedException('Vous devez être client chez nous et vous connecter pour accéder à cette page.');
        // }

        //on supprime les éventuels slash de fin
        $client = str_replace('/', '', $client);

        //on modifie les alias de pages pour charger le bon template
        //tableau des alias homonymes ($key = version secondaire, $value = version principale)
        $sectionsAliases = array(
            'charte-graphique' => 'charte'
        );
        foreach($sectionsAliases as $sectionAliasBis => $sectionAlias){
            $section = str_replace($sectionAliasBis, $sectionAlias, $section);
        }

        //on supprime les éventuels slash à la fin des urls saisies
        $section = str_replace('/', '', $section);

        //on check si $client est bien un client enregistré
        $clientChecker = $this->get('mesclics_espace_client.clientChecker');
        $clientFound = $clientChecker->isClient($client, 'alias');
        if(!$clientFound){
            throw new NotFoundHttpException('La page demandée n\'existe pas');
        }

        $args = array(
            'client' => $clientFound
        );
        
        //TODO: si section est la charte, on récupére la charte graphique du client
        //si la section est factures récupérer les factures, si c'est devis, récupérer les devis etc...
        if($section === 'charte'){
            $charte = array(
                'primaryColors' => array(
                    'blanc' => array(
                        'hex' => 'ffffff',
                        'rgb' => '(255, 255, 255)'
                    )
                ),
                'secondaryColors' => array(
                    'noir' => array(
                        'hex' => '000000',
                        'rgb' => '(0, 0, 0)'
                    )
                )
            );
            //on passe l'objet récupéré à @args pour la vue
            $args['charte'] = $charte;
        }

        //FIXME: uniquement si le client est authentifié - créer un service - (sinon on le redirige vers la page d'accueil avec un message d'erreur)
        $is_authenticated = false;
        $session = $request->getSession();
        if($is_authenticated){ //si l'user est authentifié on l'envoie sur son espace client avec un message de bienvenue
            $session->getFlashBag()->add('welcome', 'Bienvenue sur votre espace client');
            return $this->render('MesClicsEspaceClientBundle:Main:'.$section.'.html.twig', $args); 
        } else{ //sinon on le redirige sur la page d'accueil avec message d'erreur
            $session->getFlashBag()->add('error', 'Vous devez vous connecter pour accéder à votre espace client');
            return $this->redirectToRoute('mes_clics_index');
        }
    }

    //TODO: modifier pour récup dans bdd
    // protected function getClientName($client){
    //     //on récupère le nom commercial du client (FIXME: ds la bdd)
    //     $clients_correspondances = array(
    //         'mesclics' => 'mesclics',
    //         'sophroetenergies' => 'Sophro Et Energies',
    //         'ams-nettoyage' => 'AMS Nettoyage'
    //     );
    //     foreach($clients_correspondances AS $racc => $nom){
    //         if($racc === $client){
    //             $client = $nom;
    //         }
    //     }
    //     return $client;
    // }

    public function testAction($var){
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('MesClicsEspaceClientBundle:Client');
        $client = $rep->findOneByNom($var);
        
        $clients = $rep->getClientsWithImage();
        $nb_clients = $rep->countClients();
        
        $args = array(
            'name' => $var,
            'client' => $client,
            'clients' => $clients,
            'nb_clients' => $nb_clients
        );
        return $this->render('MesClicsEspaceClientBundle:Main:tests.html.twig', $args);
    }

    public function updateAction($id, $numero, Request $request){
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository('MesClicsEspaceClientBundle:Client')->find($id);
        $client->setNumero($numero);
        $em->flush();

        $flash = $request->getSession()->getFlashBag()->add('success', 'Le client a bien été mis à jour');
        return $this->redirectToRoute('mes_clics_index');
    }

    public function addClientAction($nom, $prospect, Request $request){
        //FIXME: Retraiter pour que l'ajout se fasse par formulaire.
        $em = $this->getDoctrine()->getManager();
        $client = new Client();
        $em->persist($client);
        $client->setNom($nom); //l'alias est un slug automatiquement généré avec l'annotation gedmo à partir du nom
        $client->setProspect($prospect);
        $em->flush(); //le numéro est rajouté automatiquement grâce au listener ClientCreationListener
    
        $flash = $request->getSession()->getFlashBag()->add('success', 'Le client a bien été ajouté');
        return $this->redirectToRoute('mes_clics_index');
    }
}