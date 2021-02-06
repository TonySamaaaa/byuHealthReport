<?php
require '../byuLogin.class.php';

$l = new byuLogin();

printf(json_encode(array(
	'genCode' => $l->genCode(),
	'loginCode' => $l->getloginCode()
)));
?>