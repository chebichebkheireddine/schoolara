<?php
require_once '../auth_check.php'; // Inclure la vérification de session
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM teachers WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        // Reorder the IDs
        $result = $conn->query("SELECT id FROM teachers ORDER BY id");
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            $stmt = $conn->prepare("UPDATE teachers SET id = ? WHERE id = ?");
            $stmt->bind_param("ii", $i, $row['id']);
            $stmt->execute();
            $i++;
        }

        // Reset the auto-increment value
        $conn->query("ALTER TABLE teachers AUTO_INCREMENT = $i");

        echo "Teacher deleted and IDs reordered successfully";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
    exit();
} else {
    echo "Invalid request";
}
?>
