<?php
require_once dirname(__FILE__).'/web3/vendor/autoload.php';


use Web3\Web3;
use Web3\Contract;

class eth
{
  /**
   * funzione che recupera il blocco attuale in decimali
   * @param null
   */
   public function latestBlockNumberDec()
   {
        //Carico i parametri della webapp
        $settings=Settings::load();
        // if ($settings === null || empty($settings->poa_url)){// || empty($settings->poa_port)){
        //   throw new CHttpException(404,'The requested settings page does not exist.');
        // }
        // if( !webRequest::url_test( $settings->poa_url ) ) {
        //   return hexdec(0);
        // }
        // mi connetto al nodo poa
        // $web3 = new Web3($settings->poa_url);
        // $web3 = new Web3(WebApp::getPoaNode());
        $poaNode = WebApp::getPoaNode();
		if (!$poaNode){
			$save = new Save;
			$save->WriteLog('libs','eth','latestBlockNumberDec',"All Nodes are down.");
			return hexdec(0);
		}
		$web3 = new Web3($poaNode);
        $eth = $web3->eth;

        $response = null;
        $eth->getBlockByNumber('latest',false, function ($err, $block) use (&$response){
          if ($err !== null) {
            throw new CHttpException(404,'Errore: '.$err->getMessage());
          }
          $response = $block;
        });
        return hexdec($response->number);
   }


   //per gli ethereum inserisco il fiat rate del momento
   public function getFiatRate($type){
       if ($type == 'eth'){
           $url = 'https://www.bitstamp.net/api/v2/ticker/etheur';
           $result = json_decode(webRequest::getUrl($url,$url,array(),'GET'),true);
           $value = $result['last'];
       }else{
           //qui deve avvenire la connessione all'exchange o al valore del Token
           //al momento 1 token = 1 euro
           $value = 1;
       }
       return $value;
   }
}
