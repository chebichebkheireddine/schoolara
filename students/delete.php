<?php
require_once '../auth_check.php'; // Inclure la vérification de session

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Supprimer l'étudiant
    $sql = "DELETE FROM students WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Réordonner les IDs
            $conn->query("SET @i := 0;");
            $conn->query("UPDATE students SET id = @i := @i + 1;");

            // Réinitialiser la valeur AUTO_INCREMENT
            $result = $conn->query("SELECT MAX(id) AS max_id FROM students");
            $row = $result->fetch_assoc();
            $max_id = $row['max_id'] ?? 0;
            $next_auto_increment = $max_id + 1;

            // Préparer et exécuter la commande ALTER TABLE
            $query = "ALTER TABLE students AUTO_INCREMENT = $next_auto_increment";
            if ($conn->query($query)) {
                echo "Student deleted and IDs reordered successfully";
            } else {
                echo "Error resetting AUTO_INCREMENT: " . $conn->error;
            }
        } else {
            echo "Error deleting student: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close();
    exit();
} else {
    echo "Invalid request";
}
?>
