<?php
namespace MesClics\EspaceClientBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Form\ClientType;
use MesClics\UtilsBundle\ApisManager\ApisManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MesClics\EspaceClientBundle\Controller\ClientController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientCreationEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminController extends Controller{
}