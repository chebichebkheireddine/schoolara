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

    echo "Database backup successful. Download your <a href='$backup_name'>backup file</a>.";
}

// Fonction pour restaurer la base de données à partir d'un fichier SQL
function restoreDatabase($host, $user, $pass, $name, $file) {
    $conn = new mysqli($host, $user, $pass, $name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = file_get_contents($file);
    $queries = explode(';', $sql);

    foreach ($queries as $query) {
        $result = $conn->query($query);
        if (!$result) {
            die("Error executing query: " . $conn->error);
        }
    }

    echo "Database restored successfully from $file.";
}

// Vérifier si une action de sauvegarde ou de restauration est demandée
if (isset($_POST['backup'])) {
    backupDatabase($servername, $username, $password, $dbname);
} elseif (isset($_POST['restore'])) {
    if ($_FILES['file']['error'] > 0) {
        die("Error uploading file: " . $_FILES['file']['error']);
    } else {
        restoreDatabase($servername, $username, $password, $dbname, $_FILES['file']['tmp_name']);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup and Restore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Database Backup and Restore</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <button type="submit" class="btn btn-primary" name="backup">Backup Database</button>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#restoreModal">Restore Database</button>
        </form>
    </div>

    <!-- Restore Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="restoreModalLabel">Restore Database</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="restore">Upload & Restore</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
