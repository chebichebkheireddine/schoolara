<?php
require_once './auth_check.php'; // Inclure la vérification de session

require_once './config.php';

// Fonction pour sauvegarder la base de données
function backupDatabase($host, $user, $pass, $name, $tables = '*') {
    $conn = new mysqli($host, $user, $pass, $name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Obtenir toutes les tables
    if ($tables == '*') {
        $tables = array();
        $result = $conn->query("SHOW TABLES");
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
    } else {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
    }

    $return = '';

    // Parcourir toutes les tables
    foreach ($tables as $table) {
        $result = $conn->query("SELECT * FROM $table");
        $num_fields = $result->field_count;

        $return .= "DROP TABLE IF EXISTS $table;";
        $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
        $return .= "\n\n" . $row2[1] . ";\n\n";

        for ($i = 0; $i < $num_fields; $i++) {
            while ($row = $result->fetch_row()) {
                $return .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = $conn->real_escape_string($row[$j]);
                    $row[$j] = "'$row[$j]'";
                    if ($row[$j] == 'NULL') {
                        $row[$j] = 'NULL';
                    }
                }
                $return .= implode(',', $row) . ");\n";
            }
        }
        $return .= "\n\n\n";
    }

    // Sauvegarde dans un fichier
    $dbname = "school_management";
    $backup_name = $dbname . '_backup_' . date("Y-m-d_H-i-s") . '.sql';
    $handle = fopen($backup_name, 'w+');
    fwrite($handle, $return);
    fclose($handle);

    // Upload to Google Drive
    uploadToGoogleDrive($backup_name);

    echo "Database backup successful. Backup file: $backup_name";
}

function uploadToGoogleDrive($file) {
    // TODO: Implement Google Drive upload
    echo "File $file uploaded to Google Drive (stub)";
}

// Exécuter la sauvegarde
backupDatabase($servername, $username, $password, $dbname);
?>
