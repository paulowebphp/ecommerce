<?php 

namespace Hcode\PagSeguro;

class Config
{
	const SANDBOX = true;

	const SANDBOX_EMAIL = "jpccambraia@gmail.com";

	const PRODUCTION_EMAIL = "jpccambraia@gmail.com";

	const SANDBOX_TOKEN = "9F1494D9364F46BF8056DA6DE39974C9";

	const PRODUCTION_TOKEN = "AA7A260344E9434CACA8AD2E29B9A177";

	const SANDBOX_SESSIONS = "https://ws.sandbox.pagseguro.uol.com.br/v2/sessions";	

	const PRODUCTION_SESSIONS = "https://ws.pagseguro.uol.com.br/v2/sessions";




	public static function getAuthentication():array
	{

		if( Config::SANDBOX === true )
		{
			return [

				"email" => Config::SANDBOX_EMAIL,
				"token" => Config::SANDBOX_TOKEN

			];

		}#end if
		else
		{

			return [

				"email"=>Config::PRODUCTION_EMAIL,
				"token"=>Config::PRODUCTION_TOKEN

			];

		}#end else

	}#END getAuthentication


	public static function getUrlSessions():string
	{

		return (Config::SANDBOX === true) ? Config::SANDBOX_SESSIONS : Config::PRODUCTION_SESSIONS;

	}#END getUrlSessions

}#END class Config


 ?>