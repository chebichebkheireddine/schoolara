<?php
require_once '../auth_check.php'; // Inclure la vérification de session

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $class_id = $_POST['class_id'];
    $enrollment_date = $_POST['enrollment_date'];
    $months = $_POST['months'];
    $paid = $_POST['paid'];

    // Calculate end_date based on enrollment_date and number of months
    $end_date = date('Y-m-d', strtotime("+$months months", strtotime($enrollment_date)));

    $sql = "INSERT INTO students (name, class_id, enrollment_date, end_date, paid) VALUES ('$name', $class_id, '$enrollment_date', '$end_date', '$paid')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the students list page after successful insertion
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
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Add New Student</h1>
        <form action="add.php" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Student Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="class_id" class="form-label">Class</label>
                <select class="form-control" id="class_id" name="class_id" required>
                    <?php
                    $sql = "SELECT id, name, num_group FROM classes";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . " (" . $row['num_group'] . ")</option>";
                        }
                    } else {
                        echo "<option value=''>No classes available</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="enrollment_date" class="form-label">Enrollment Date</label>
                <input type="date" class="form-control" id="enrollment_date" name="enrollment_date" required>
            </div>
            <div class="mb-3">
                <label for="months" class="form-label">Months</label>
                <input type="number" class="form-control" id="months" name="months" required>
            </div>
            <div class="mb-3">
                <label for="paid" class="form-label">Payment Status</label>
                <select class="form-control" id="paid" name="paid" required>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
