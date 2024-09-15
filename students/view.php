<?php
require_once '../config.php';

if (isset($_GET['id'])) {
    $studentId = intval($_GET['id']);
    $sql = "SELECT students.*, classes.name as class_name, classes.num_group FROM students JOIN classes ON students.class_id = classes.id WHERE students.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    if ($student) {
        echo "<div class='container mt-5'>";
        echo "<h1>Student Details</h1>";
        echo "<div class='row'>";
        echo "<div class='col-md-8'>"; // Colonne pour les détails
        echo "<p><strong>Name:</strong> " . htmlspecialchars($student['name']) . "</p>";
        echo "<p><strong>Birthday:</strong> " . htmlspecialchars($student['birthday']) . "</p>";
        echo "<p><strong>Phone Number:</strong> " . htmlspecialchars($student['phone_number']) . "</p>";
        echo "<p><strong>Parent Phone Number:</strong> " . htmlspecialchars($student['parent_phone_number']) . "</p>";
        echo "<p><strong>Class:</strong> " . htmlspecialchars($student['class_name']) . " (" . htmlspecialchars($student['num_group']) . ")</p>";
        echo "<p><strong>Enrollment Date:</strong> " . htmlspecialchars($student['enrollment_date']) . "</p>";
        echo "<p><strong>Payment End:</strong> " . htmlspecialchars($student['end_date']) . "</p>";
        echo "<p><strong>Payment Status:</strong> " . ($student['paid'] ? 'Paid' : 'Not Paid') . "</p>";
        echo "</div>";

        echo "<div class='col-md-4'>"; // Colonne pour la photo
        if (!empty($student['photo'])) {
            $photoPath = '../uploads/' . htmlspecialchars($student['photo']);
            if (file_exists($photoPath)) {
                echo "<p><strong>Photo:</strong></p>";
                echo "<img src='" . $photoPath . "' alt='Student Photo' class='img-fluid' style='max-width: 100%;'>";
            } else {
                echo "<p>File does not exist at path: " . htmlspecialchars($photoPath) . "</p>";
            }
        } else {
            echo "<p>No photo available in the database.</p>";
        }
        echo "</div>";
        echo "</div>"; // Fin de la ligne
        echo "</div>"; // Fin du conteneur
    } else {
        echo "<p>No details found for this student.</p>";
    }
} else {
    echo "<p>No student ID provided.</p>";
}

$conn->close();
?>
