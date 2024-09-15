<?php
require_once '../auth_check.php'; // Inclure la vérification de session

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $num_group = $_POST['num_group'];
    $start_date = $_POST['start_date'];
    $number_of_months = $_POST['number_of_months'];
    $teacher_id = $_POST['teacher_id'];

    $sql = "UPDATE classes SET name='$name', num_group='$num_group', start_date='$start_date', number_of_months='$number_of_months', teacher_id='$teacher_id' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Class updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
    header("Location: list.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM classes WHERE id=$id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $class = $result->fetch_assoc();
} else {
    echo "Class not found";
    exit();
}

$teachersResult = $conn->query("SELECT * FROM teachers");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Class</h1>
        <form action="edit.php" method="post">
            <input type="hidden" name="id" value="<?php echo $class['id']; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Class Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $class['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="num_group" class="form-label">Group Number</label>
                <input type="number" class="form-control" id="num_group" name="num_group" value="<?php echo $class['num_group']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $class['start_date']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="number_of_months" class="form-label">Number of Months</label>
                <input type="number" class="form-control" id="number_of_months" name="number_of_months" value="<?php echo $class['number_of_months']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="teacher_id" class="form-label">Responsible Teacher</label>
                <select class="form-control" id="teacher_id" name="teacher_id" required>
                    <option value="">Select Teacher</option>
                    <?php
                    while ($teacher = $teachersResult->fetch_assoc()) {
                        $selected = ($teacher['id'] == $class['teacher_id']) ? 'selected' : '';
                        echo "<option value='{$teacher['id']}' $selected>{$teacher['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Class</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
