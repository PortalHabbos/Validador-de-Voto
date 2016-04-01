<?php

///////////////////////////////////////////////////////////////
// # PortalHabbos.biz   API Script -   Release 1.0.3         //
// # © Copyright PortalHabbos.biz 2016. All rights reserved. //
///////////////////////////////////////////////////////////////

require_once 'portalhabbos_config.php';
require_once 'portalhabbos.php';

$PortalHabbos = new PortalHabbos();

if($PortalHabbos->hasClientVoted()) {

    echo 'Você votou!';

}else{

    // echo 'Você ainda tem que votar!';

    $PortalHabbos->redirectClientToVote();

}