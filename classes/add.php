<?php
require_once '../auth_check.php'; // Inclure la vérification de session

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $start_date = $_POST['start_date'];
    $number_of_months = $_POST['number_of_months'];
    $num_group = $_POST['num_group'];
    $teacher_id = $_POST['teacher_id'];

    $sql = "INSERT INTO classes (name, start_date, number_of_months, num_group, teacher_id) VALUES ('$name', '$start_date', '$number_of_months', '$num_group', '$teacher_id')";

    if ($conn->query($sql) === TRUE) {
        header("Location: list.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Add New Class</h1>
        <form action="add.php" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Class Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="num_group" class="form-label">Group Number</label>
                <input type="number" class="form-control" id="num_group" name="num_group" required>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>
            <div class="mb-3">
                <label for="number_of_months" class="form-label">Number of Months</label>
                <input type="number" class="form-control" id="number_of_months" name="number_of_months" required>
            </div>
            <div class="mb-3">
                <label for="teacher_id" class="form-label">Responsible Teacher</label>
                <select class="form-control" id="teacher_id" name="teacher_id" required>
                    <option value="">Select Teacher</option>
                    <?php
                    $teachersResult = $conn->query("SELECT * FROM teachers");
                    while ($teacher = $teachersResult->fetch_assoc()) {
                        echo "<option value='{$teacher['id']}'>{$teacher['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Class</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
