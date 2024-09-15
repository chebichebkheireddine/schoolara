<?php
require_once '../auth_check.php'; // Inclure la vérification de session
require_once '../config.php';

// Variables pour gérer les erreurs
$uploadError = "";
$photoPath = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $birthday = $_POST['birthday'];
    $phone_number = $_POST['phone_number'];
    $parent_phone_number = $_POST['parent_phone_number'];
    $class_id = $_POST['class_id'];
    $enrollment_date = $_POST['enrollment_date'];
    $months = $_POST['months'];
    $paid = $_POST['paid'];

    // Convert paid status to numeric value
    $paid = ($paid === 'Paid') ? 1 : 0;
    // Calculate end_date based on enrollment_date and number of months
    $end_date = date('Y-m-d', strtotime("+$months months", strtotime($enrollment_date)));

    // Check if a new photo is uploaded
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileSize = $_FILES['photo']['size'];
        $fileType = $_FILES['photo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validate file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            // Make sure the uploads directory exists
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate a new unique name for the photo
            $newFileName = uniqid() . '.' . $fileExtension;
            $uploadFileDir = $uploadDir . $newFileName;

            // Move the file to the upload directory
            if (move_uploaded_file($fileTmpPath, $uploadFileDir)) {
                $photoPath = $newFileName;
            } else {
                $uploadError = "Error moving the file.";
            }
        } else {
            $uploadError = "Invalid file type.";
        }
    }

    // Update the student's information in the database
    $sql = "UPDATE students SET name=?, birthday=?, phone_number=?, parent_phone_number=?, class_id=?, enrollment_date=?, end_date=?, paid=?";
    
    // If a new photo is uploaded, include it in the query
    if ($photoPath) {
        $sql .= ", photo=?";
    }
    $sql .= " WHERE id=?";

    $stmt = $conn->prepare($sql);

    // Bind parameters based on whether a photo is being updated
    if ($photoPath) {
        $stmt->bind_param('ssssissssi', $name, $birthday, $phone_number, $parent_phone_number, $class_id, $enrollment_date, $end_date, $paid, $photoPath, $id);
    } else {
        $stmt->bind_param('ssssisssi', $name, $birthday, $phone_number, $parent_phone_number, $class_id, $enrollment_date, $end_date, $paid, $id);
    }

    if ($stmt->execute()) {
        // Redirect to the students list page after successful update
        header("Location: list.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM students WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        echo "Student not found";
        exit();
    }
} else {
    echo "No student ID provided";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Student</h1>
        <form action="edit.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Student Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="birthday" class="form-label">Birthday</label>
                <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($row['birthday']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($row['phone_number']); ?>" required
                       pattern="[0-9]{10}" placeholder="1234567890">
            </div>

            <div class="mb-3">
                <label for="parent_phone_number" class="form-label">Parent Phone Number</label>
                <input type="tel" class="form-control" id="parent_phone_number" name="parent_phone_number" value="<?php echo htmlspecialchars($row['parent_phone_number']); ?>" required
                       pattern="[0-9]{10}" placeholder="1234567890">
            </div>

            <div class="mb-3">
                <label for="class_id" class="form-label">Class</label>
                <select class="form-control" id="class_id" name="class_id" required>
                    <?php
                    $sql = "SELECT id, name, num_group FROM classes";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($class = $result->fetch_assoc()) {
                            $selected = ($class['id'] == $row['class_id']) ? 'selected' : '';
                            echo "<option value='" . $class['id'] . "' $selected>" . $class['name'] . " (" . $class['num_group'] . ")</option>";
                        }
                    } else {
                        echo "<option value=''>No classes available</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="enrollment_date" class="form-label">Enrollment Date</label>
                <input type="date" class="form-control" id="enrollment_date" name="enrollment_date" value="<?php echo htmlspecialchars($row['enrollment_date']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="months" class="form-label">Months</label>
                <input type="number" class="form-control" id="months" name="months" value="<?php echo ceil((strtotime($row['end_date']) - strtotime($row['enrollment_date'])) / (30 * 24 * 60 * 60)); ?>" required>
            </div>

            <div class="mb-3">
                <label for="paid" class="form-label">Payment Status</label>
                <select class="form-control" id="paid" name="paid" required>
                    <option value="Paid" <?php if($row['paid'] == 'Paid') echo 'selected'; ?>>Paid</option>
                    <option value="Pending" <?php if($row['paid'] == 'Pending') echo 'selected'; ?>>Pending</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="photo" class="form-label">Student Photo</label>
                <input type="file" class="form-control" id="photo" name="photo">
                <?php
                if (!empty($row['photo'])) {
                    $photoPath = 'uploads/' . htmlspecialchars($row['photo']);
                    if (file_exists($photoPath)) {
                        echo "<p>Current Photo:</p>";
                        echo "<img src='" . $photoPath . "' alt='Current Student Photo' class='img-fluid' style='max-width: 150px;'>";
                    }
                }
                ?>
            </div>

            <button type="submit" class="btn btn-primary">Update Student</button>
        </form>
        <?php
        if ($uploadError) {
            echo "<div class='alert alert-danger mt-3'>$uploadError</div>";
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
