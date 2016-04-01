<?php

///////////////////////////////////////////////////////////////
// # PortalHabbos.biz   API Script -   Release 1.0.3         //
// # © Copyright PortalHabbos.biz 2016. All rights reserved. //
///////////////////////////////////////////////////////////////

class PortalHabbos {

    private $paginaNome, $callTimeout, $usingCloudFlare, $apiPath;

    function __construct() {

        global $_CONFIG;

        $this->paginaNome      = $_CONFIG['usuariopagina'];
        $this->requestTimeout  = $_CONFIG['timeout'];
        $this->usingCloudFlare = $_CONFIG['cloudflare'];
        $this->apiPath         = $_CONFIG['api'];

        if($this->usingCloudFlare) {

            if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) { 

                $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP']; 

            }

        }

    }

    public function hasClientVoted() {
    
        if(!$this->_isVoteCookieSet()) {

            $urlRequest = $this->apiPath . 'validate.php?user=' . $this->paginaNome . '&ip=' . $_SERVER['REMOTE_ADDR'];

            $dataRequest = $this->_makeCurlRequest($urlRequest);

            if(in_array($dataRequest, array(1, 2))) {

                $this->_setVoteCookie();

                return true;

            }else if($dataRequest == 3) {

                return false;

            }else{

                /* Há algo de errado com PortalHabbos, por isso vamos marcar o usuário como votado e proceder como se ja tivesse votado */

                $this->_setVoteCookie();

                return true;

            }

        }

        return true;

    }

    public function redirectClientToVote() {

        header('Location: ' . $this->apiPath . 'rankings/vote/' . $this->paginaNome);

        exit;

    }    

    private function _makeCurlRequest($url) {

        if(function_exists('curl_version')) {

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->requestTimeout);
            curl_setopt($curl, CURLOPT_USERAGENT, 'PortalHabbos Validador de Voto');

            $requestData = curl_exec($curl);

            curl_close($curl);

        }else{

            $requestData = stream_context_create(array('http' => array('timeout' => $this->requestTimeout))); 

            return @file_get_contents($url, 0, $requestData); 

        }

        return $requestData;

    }

    private function _setVoteCookie() {

        $rankingsResetTime = $this->_getRankingsResetTime();

        setcookie('voting_stamp', $rankingsResetTime, $rankingsResetTime);

    }

    private function _isVoteCookieSet() {

        if(isset($_COOKIE['voting_stamp'])) {

            if($_COOKIE['voting_stamp'] == $this->_getRankingsResetTime()) {

                return true;

            }else{

                setcookie('voting_stamp', '');

                return false;

            }

        }

        return false;

    }

    private function _getRankingsResetTime() {

        $serverDefaultTime = date_default_timezone_get();

        date_default_timezone_set('America/Sao_Paulo');

        $rankingsResetTime = mktime(0, 0, 0, date('n'), date('j') + 1);
    
        date_default_timezone_set($serverDefaultTime);
        
        return $rankingsResetTime;

    }

}