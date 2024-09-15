<?php
$db_host = 'localhost'; // Remplacez par votre hôte
$db_user = 'root'; // Remplacez par votre utilisateur de base de données
$db_pass = ''; // Remplacez par votre mot de passe de base de données
$db_name = 'school_management'; // Nom de votre base de données
$backup_dir = 'C:\xampp3\htdocs\school_management\backups'; // Répertoire pour sauvegardes

// Créez le répertoire si non existant
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Nom du fichier de sauvegarde
$backup_file = $backup_dir . '/backup_' . date('Y-m-d_H-i-s') . '.sql';

// Chemin complet vers mysqldump (assurez-vous que ce chemin est correct)
$mysqldump_path = 'C:\xampp3\mysql\bin\mysqldump.exe';

// Commande mysqldump
$command = "\"$mysqldump_path\" --opt --host=$db_host --user=$db_user --password=$db_pass $db_name > \"$backup_file\"";

// Exécution de la commande
exec($command, $output, $result);

// Vérifiez le résultat
if ($result === 0) {
    echo "Sauvegarde réussie : $backup_file";
} else {
    echo "Erreur lors de la sauvegarde. Code d'erreur : $result";
    echo "<br>Sortie de la commande : " . implode("\n", $output);
}
?>
