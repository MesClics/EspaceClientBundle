<?php
namespace MesClics\EspaceClientBundle\Controller;

use MesClics\UserBundle\Entity\User;
use MesClics\UserBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use Symfony\Component\HttpFoundation\Response;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ClientType;
use MesClics\EspaceClientBundle\Form\ContratType;
use MesClics\UtilsBundle\ApisManager\ApisManager;
use MesClics\EspaceClientBundle\Form\ClientEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use MesClics\EspaceClientBundle\Form\FormManager\ClientFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends Controller{

    //CLIENTS
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function clientsAction(ApisManager $apis_manager, Request $request){
        //on récupère la liste des clients
        $em = $this->getDoctrine()->getManager();
        $clientRepo = $em->getRepository('MesClicsEspaceClientBundle:Client');
        $clients = $clientRepo->getClientsList();
        
        $trello_api = $apis_manager->getApi('trello');

        //DEBUG: remise à zero des éléments enregistrés en tant que paramètres de session pour l'api Trello
        // $this->container->get('session')->remove('_trello');
        //GET CLIENTS BOARD
        $boards_options = array(
            'fields' => array(
                'name',
                'id',
                'url',
                'shortUrl',
            ),
            'lists' => 'open',
            'lists_fields' => array(
                'id',
                'name',
            )
        );
        $trelloClientsBoard = $trello_api->getBoardByName("CLIENTS", $boards_options);

        //WIDGET Ajouter un client
        //on crée un objet qui sera hydraté par notre formulaire
        $client = new Client();
        //création du formulaire
        $clientForm = $this->createForm(ClientType::class, $client);
               

        if(!$request->isMethod('POST')){
            //on génère la vue
            $args = array(
                'clients' => $clients,
                'client_new_form' => $clientForm->createView(),
                'currentSection' => 'clients',
                'trelloClientsBoard' => $trelloClientsBoard
            );

            return $this->render('MesClicsAdminBundle:Panel:clients.html.twig', $args);
        }

        //si le formulaire est soumis
        else{
            //gestion du formulaire si requête de type post, on gère le formulaire
            $clientFormManager = $this->get('mesclics_espace_client.form_manager.client.new');
            $clientFormManager->handle($clientForm);

                //on redirige vers la page client
                $args = array(
                    'client_id' => $clientFormManager->getForm()->getData()->getId()
                );
                return $this->redirectToRoute('mesclics_admin_client', $args);
        }
    }
}