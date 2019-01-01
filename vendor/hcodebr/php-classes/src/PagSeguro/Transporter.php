<?php 

namespace Hcode\PagSeguro;

use DOMDocument;
use DOMElement;
use \GuzzleHttp\Client;
use \Hcode\PagSeguro\Payment;
use \Hcode\PagSeguro\Config;


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



	public static function sendTransaction(Payment $payment)
	{

		$client = new Client();

		$res = $client->request(

			'POST', 
			Config::getUrlTransaction()."?".http_build_query(Config::getAuthentication()),
			[

				'verify'=> false,	
				'headers'=> [

					'Content-Type' => 'application/xml'

				],

				'body' => $payment->getDOMDocument()->saveXml()

			]#verify false desabilita a verificação de certificado SSL

		);

		# CARREGA UM XML A PARTIR DE UMA STRING, TRANSFORMA EM UM OBJETO AO RETORNAR
		$xml = simplexml_load_string($res->getBody()->getContents());


		//var_dump($xml);

	}#END sendTransaction



}#END class Transporter


 ?> 