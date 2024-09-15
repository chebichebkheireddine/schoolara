<?php
require_once '../auth_check.php'; // Inclure la vérification de session
require_once '../config.php';     // Inclure les informations de configuration pour la connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous que l'ID est fourni et est un entier
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = $_POST['id'];

        // Préparer et exécuter la requête SQL pour supprimer l'absence
        $sql = "DELETE FROM absences WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Suppression réussie, rediriger vers la liste des absences
            header("Location: list.php");
            exit();
        } else {
            // Erreur lors de la suppression
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    } else {
        // L'ID n'est pas valide
        echo "Invalid ID";
    }
} else {
    // Mauvaise méthode de requête
    echo "Invalid request method";
}

$conn->close();
?>
