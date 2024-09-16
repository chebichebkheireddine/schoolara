<?php
require_once '../auth_check.php'; // Inclure la vérification de session

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sql = "UPDATE teachers SET name='$name',phone='$phone',email='$email' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Teacher updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
    header("Location: list.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM teachers WHERE id=$id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $teacher = $result->fetch_assoc();
} else {
    echo "Teacher not found";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Edit Teacher</h1>
        <form action="edit.php" method="post">
            <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
            <div class="row md-3">
                <div class="mb-3">
                    <label for="name" class="form-label">Enseignant</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?php echo $teacher['name']; ?>">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        value="<?php echo $teacher['phone']; ?>">
                </div>
                <div class="mb-3">
                    <label for="emsil" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email"
                        value="<?php echo $teacher['email']; ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Teacher</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>