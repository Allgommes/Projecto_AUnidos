<?php
require_once 'config/database.php';
require_once 'src/classes/User.php';

$user = new User();
$user->logout();

$_SESSION['success_message'] = 'Logout efetuado com sucesso!';
redirect(SITE_URL . '/login.php');
?>