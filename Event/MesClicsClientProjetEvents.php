<?php

namespace MesClics\EspaceClientBundle\Event;

final class MesClicsClientProjetEvents{
    const CREATION = "mesclics_client.projet.creation";
    const UPDATE = "mesclics_client.projet.update";
    const REMOVAL = "mesclics_client.projet.removal";
    const ASSOCIATION_TO_CONTRACT = "mesclics_client.projet.association_to_contract";
}