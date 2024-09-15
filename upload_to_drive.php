<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

// Chemin vers le fichier des informations d'identification
$credentialsPath = 'C:\\xampp3\\htdocs\\school_management\\credentials.json';

// Initialisation du client Google
$client = new Client();
$client->setAuthConfig($credentialsPath);
$client->addScope(Drive::DRIVE_FILE);

// Autorisation du client
$tokenPath = 'C:\\xampp3\\htdocs\\school_management\\token.json';
if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
}

if ($client->isAccessTokenExpired()) {
    if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    } else {
        $authUrl = $client->createAuthUrl();
        printf("Ouvrez le lien suivant dans votre navigateur :\n%s\n", $authUrl);
        print('Entrez le code de vérification : ');
        $authCode = trim(fgets(STDIN));

        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        $client->setAccessToken($accessToken);

        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($accessToken));
    }
}

// Initialisation du service Google Drive
$service = new Drive($client);

// Chemin vers le fichier de sauvegarde
$backupFilePath = 'C:\\xampp3\\htdocs\\school_management\\backups\\backup_' . date('Y-m-d_H-i-s') . '.sql';

// Création du fichier sur Google Drive
$fileMetadata = new Drive\DriveFile(array(
    'name' => basename($backupFilePath),
    'parents' => array('appDataFolder') // Utilisez 'appDataFolder' pour stocker dans le dossier de l'application
));

$content = file_get_contents($backupFilePath);
$file = $service->files->create($fileMetadata, array(
    'data' => $content,
    'mimeType' => 'application/octet-stream',
    'uploadType' => 'multipart',
    'fields' => 'id'
));

printf("Fichier de sauvegarde téléchargé sur Google Drive avec l'ID : %s\n", $file->id);
?>
