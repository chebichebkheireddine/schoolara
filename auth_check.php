<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: admin/login.php");
    exit();
}
?>
