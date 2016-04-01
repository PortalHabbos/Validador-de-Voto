Validador-de-Voto
==============

Verifique se o usuário ja votou para a sua página.

####Exemplo

    <?php
    
    require_once 'portalhabbos_config.php';
    require_once 'portalhabbos.php';
    
    $PortalHabbos = new PortalHabbos();
    
    if($PortalHabbos->hasClientVoted()) {
    
        echo 'Você votou!';

    }else{
    
        // echo 'Você ainda tem que votar!';

        $PortalHabbos->redirectClientToVote();
    
    }
