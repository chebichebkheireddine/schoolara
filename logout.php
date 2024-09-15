<?php
session_start();

// Supprimer toutes les variables de session
$_SESSION = array();

// Supprimer les cookies de session si utilisés
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header("Location: admin/login.php");
exit();
?>
