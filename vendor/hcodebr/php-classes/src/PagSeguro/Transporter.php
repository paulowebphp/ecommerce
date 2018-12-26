<?php 

namespace Hcode\PagSeguro;

use \GuzzleHttp\Client;


class Transporter
{

	public static function createSession()
	{

		$client = new Client();

		$res = $client->request(

			'POST', 
			Config::getUrlSessions()."?".http_build_query(Config::getAuthentication()),
			['verify'=> false]#verify false desabilita a verificação de certificado SSL

		);

		# CARREGA UM XML A PARTIR DE UMA STRING, TRANSFORMA EM UM OBJETO AO RETORNAR
		$xml = simplexml_load_string($res->getBody()->getContents());

		return ((string)$xml->id);

	}#END createSession


}#END class Transporter


 ?> 