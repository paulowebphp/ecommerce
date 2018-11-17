<?php 

use \Hcode\Model\User;



function formatPrice($vlprice)
{
	return number_format($vlprice, 2, ",",".");
	
}#END formatPrice



function checkLogin($inadmin = true)
{
	return User::checkLogin($inadmin);

}#END checkLogin



function getUserName()
{
	$user = User::getFromSession();

	return $user->getdesperson();

}#END getUserName



 ?>