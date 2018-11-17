<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class User extends Model
{
	const SESSION = "User";

	# CHAVE DE ENCRIPTAÇÃO TEM QUE TER PELO MENOS 16 CARACTERES
	const SECRET = "HcodePhp7_Secret";

	const ERROR = "UserError";
	const ERROR_REGISTER = "UserErrorRegister";

	public static function getFromSession()
	{
		$user = new User();

		if(isset($_SESSION[User::SESSION])
			&& 
			(int)$_SESSION[User::SESSION]['iduser'] > 0)
		{

			$user->setData($_SESSION[User::SESSION]);

		}#end if

		return $user;

	}#END getFromSession


	public static function checkLogin($inadmin = true)
	{
		if(
			!isset($_SESSION[User::SESSION])
			|| 
			!$_SESSION[User::SESSION]
			|| 
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		)
		{

			# EM QUALQUER DESTAS CONDIÇOES ACIMA, NÃO ESTÁ LOGADO
			return false;
	
		}#end if
		else
		{
			if(
				$inadmin === true 
				&& 
				(bool)$_SESSION[User::SESSION]['inadmin'] === true
			)
			{

				return true;

			}#end if
			else if($inadmin === false)
			{

				return true;

			}#end else if
			else
			{

				return false;

			}#end else

		}#end else

	}#END checkLogin


	/*
	# ANTES DA AULA 117
	public static function login($login, $password)
	{
		$sql = new Sql();

		$results = $sql->select("

			SELECT * FROM tb_users 
			WHERE deslogin = :LOGIN

			", array(

			":LOGIN"=>$login
		));

		if(count($results) === 0)
		{
			throw new \Exception("Usuário inexistente ou senha inválida");
			
		}#END if

		$data = $results[0];

		if(password_verify($password, $data["despassword"]) === true)
		{
			$user = new User();

			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		} else

		{
			throw new \Exception("Usuário inexistente ou senha inválida");
			
		}

	}#END login
	*/

	# AULA 117
	public static function login($login, $password)
	{
		$sql = new Sql();

		$results = $sql->select("

			SELECT * FROM tb_users a
			INNER JOIN tb_persons b
			ON a.idperson = b.idperson
			WHERE a.deslogin = :LOGIN

			", array(

			":LOGIN"=>$login
		));

		if(count($results) === 0)
		{
			throw new \Exception("Usuário inexistente ou senha inválida");
			
		}#end if

		$data = $results[0];

		if(password_verify($password, $data["despassword"]) === true)
		{
			$user = new User();

			$data['desperson'] = utf8_encode($data['desperson']);

			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		}#end if
		else

		{
			throw new \Exception("Usuário inexistente ou senha inválida");
			
		}#end else

	}#END login


	/* 
	# Antes da aula 117
	public static function verifyLogin($inadmin = true)
	{
		if(!User::checkLogin($inadmin))		
		{
			header("Location: /admin/login/");
			exit;

		}#END if verifyLogin

	}#END verifyLogin
	*/

	# Aula 117
	public static function verifyLogin($inadmin = true)
	{
		if(!User::checkLogin($inadmin))		
		{
			
			if( $inadmin )
			{

				header("Location: /admin/login");

			}#end if
			else
			{

				header("Location: /login");

			}#end else

			exit;

		}#END if verifyLogin

	}#END verifyLogin



	public static function logout()
	{
		$_SESSION[User::SESSION] = NULL;
		
	}#END logout



	public static function listAll()
	{
		$sql = new Sql();

		return $sql->select("

			SELECT * FROM tb_users a 
			INNER JOIN tb_persons b USING(idperson) 
			ORDER BY b.desperson;

			");
		
	}#END listAll

	public function save()
	{
		$sql = new Sql();
		/* 
		pdesperson VARCHAR(64), 
		pdeslogin VARCHAR(64), 
		pdespassword VARCHAR(256), 
		pdesemail VARCHAR(128), 
		nrphone BIGINT, 
		pinadmin TINYINT 
		*/

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(

			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

	}#END save


	# Aula 117
	public function get($iduser)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(

			":iduser"=>$iduser

		));

		$data = $results[0];

		$data['desperson'] = utf8_encode($data['desperson']);

		$this->setData($results[0]);

	}#END get


	/*
	# Antes da aula 117
	public function get($iduser)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(

			":iduser"=>$iduser

		));

		$this->setData($results[0]);

	}#END get
	*/


	public function update()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(

			":iduser"=>$this->getiduser(),
			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);
	}#END update

	public function delete()
	{
		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)", array(

			":iduser"=>$this->getiduser()
			
		));
	}#END delete


public static function getForgot($email, $inadmin = true)
{
    $sql = new Sql();
     
    $results = $sql->select("
    	SELECT *
    	FROM tb_persons a
    	INNER JOIN tb_users b USING(idperson)
    	WHERE a.desemail = :email;
    	", array(
    		":email"=>$email
    ));

    if (count($results) === 0)
    {
        throw new \Exception("Não foi possível recuperar a senha");
    }#end if
    else
    {
        $data = $results[0];
         
        $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
        	":iduser"=>$data['iduser'],
            ":desip"=>$_SERVER['REMOTE_ADDR']
        ));

        if (count($results2) === 0)
        {
            throw new \Exception("Não foi possível recuperar a senha.");
        }#end if
        else
        {
            $dataRecovery = $results2[0];
            
            $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            
            $code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
            
            $result = base64_encode($iv.$code);
            
            if ($inadmin === true) 
            {
            	$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$result";
            }#end if
            else
            {
            	$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$result";
            }#end else

            $mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
            	"name"=>$data['desperson'],
                "link"=>$link
            )); 
            
            $mailer->send();
            
            return $link;

        }#end else

    }#end else

}#END getForgot

/*
	public static function getForgot($email, $inadmin = true)
	{
		$sql = new Sql();

		$results = $sql->select("
			SELECT * 
			FROM tb_persons a
			INNER JOIN tb_users b USING(idperson)
			WHERE a.desemail = :email;
			", array(
				":email"=>$email
			));

		if(count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha");
			
		}#end if
		else
		{
			$data = $results[0];

			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data["iduser"],
				":desip"=>$_SERVER["REMOTE_ADDR"],
			));

			if(count($results2) === 0)
			{
				throw new \Exception("Não foi possível recuperar a senha");
				
			}#end if
			else
			{
				$dataRecovery = $results2[0];

				/*	# DEPRECATED #
					base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));
				

					$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

					$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir senha da Hcode Store", "forgot", array(
						"name"=>$data["desperson"],
						"link"=>$link
					));

					$mailer->send();

					return $data;
				

				$iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
		        
		        $code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
		        
		        $result = base64_encode($iv.$code);
		        
		        if($inadmin === true)
		        {
		        	$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$result";
		        }#end if
		        else
		        {
		        	$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$result";
		        }#end else
		        
		        $mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
		        	"name"=>$data['desperson'],
		            "link"=>$link
		        )); 
		        
		        $mailer->send();
		        
		        return $link;

			}#end else

		}#end else

	}#END getForgot
*/

	public static function validForgotDecrypt($result)
	{
	    $result = base64_decode($result);
	    
	    $code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
	    
	    $iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');
    
	    $idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0, $iv);
	    
	    $sql = new Sql();
	    
	    $results = $sql->select("
	        SELECT *
	        FROM tb_userspasswordsrecoveries a
	        INNER JOIN tb_users b USING(iduser)
	        INNER JOIN tb_persons c USING(idperson)
	        WHERE
	        	a.idrecovery = :idrecovery
	        	AND
	        	a.dtrecovery IS NULL
	        	AND
	        	DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();", array(

	        		":idrecovery"=>$idrecovery

	        	));

	    if (count($results) === 0)
	    {
	        throw new \Exception("Não foi possível recuperar a senha");
	    }#end if
	    else
	    {
	    	return $results[0];

	    }#end else

	}#END validForgotDecrypt

	public static function setForgotUsed($idrecovery)
	{
		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries 
			SET dtrecovery = NOW()
			WHERE idrecovery = :idrecovery", array(

				":idrecovery"=>$idrecovery

			));

	}#END setForgotUsed


	public function setPassword($password)
	{
		$sql = new Sql();

		$sql->query("UPDATE tb_users 
			SET despassword = :password
			WHERE iduser = :iduser", array(

				"password"=>$password,
				":iduser"=>$this->getiduser()

			));

	}#END setPassword



	public static function setError($msg)
	{

		$_SESSION[User::ERROR] = $msg;

	}#END setError


	public static function getError()
	{

		$msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';

		User::clearError();

		return $msg;

	}#END getError


	public static function clearError()
	{
		$_SESSION[User::ERROR] = NULL;

	}#END clearError
	


	public static function setErrorRegister($msg)
	{
		$_SESSION[User::ERROR_REGISTER] = $msg;
		
	}#END setErrorRegister



	public static function getErrorRegister()
	{
		$msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[User::ERROR_REGISTER] : '';

		User::clearErrorRegister();

		return $msg;

	}#END getErrorRegister



	public static function clearErrorRegister()
	{
		$_SESSION[User::ERROR_REGISTER] = NULL;
		
	}#END clearErrorRegister



	public static function checkLoginExist($login)
	{
		$sql = new Sql();

		$results = $sql->select("

			SELECT * FROM tb_users
			WHERE deslogin = :deslogin;

			", [

				':deslogin'=>$login

			]);

		return ( count($results) > 0 );

	}#END checkLoginExist



	public static function getPasswordHash($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, [

			'cost'=>12

		]);

	}#END getPasswordHash


}#END class User

 ?>