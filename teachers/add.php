<?php
require_once '../auth_check.php'; // Inclure la vérification de session

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $sql = "INSERT INTO teachers (name,phone,email) VALUES ('$name','$phone','$email')";

    if ($conn->query($sql) === TRUE) {
        // Redirection vers la liste des enseignants après insertion réussie
        header("Location: list.php");
        exit();
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un enseignant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Ajouter un nouvel enseignant</h1>
        <form action="add.php" method="post">
            <div class="row mb-3">

                <div class="col-md-4">
                    <label for="name" class="form-label">Enseignant :</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-4">
                    <label for="phone" class="form-label">Phone :</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="col-md-4">
                    <label for="email" class="form-label">Email :</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>