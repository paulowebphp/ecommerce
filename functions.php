<?php 

use \Hcode\Model\User;
use \Hcode\Model\Cart;



function formatPrice($vlprice)
{

	if( !$vlprice > 0 ) $vlprice = 0;

	return number_format($vlprice, 2, ",",".");
	
}#END formatPrice



function formatDate($date)
{

	return date('d/m/Y', strtotime($date));
	
}#END formatDate




function checkLogin($inadmin = true)
{
	return User::checkLogin($inadmin);

}#END checkLogin



function getUserName()
{
	$user = User::getFromSession();

	return $user->getdesperson();

}#END getUserName



function getCartNrQtd()
{

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

	return $totals['nrqtd'];

}#END getCartNrQtd



function getCartVlSubTotal()
{

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

	return formatPrice($totals['vlprice']);
	
}#END getCartVlSubTotal


 ?>