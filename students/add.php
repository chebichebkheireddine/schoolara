<?php
require_once '../auth_check.php'; // Inclure la vérification de session
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $birthday = $_POST['birthday'];
    $phone_number = $_POST['phone_number'];
    $parent_phone_number = $_POST['parent_phone_number'];

    // Handle file upload
    $photo_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['photo']['type'], $allowed_types)) {
            $upload_dir = '../uploads/'; // Ensure this directory exists and is writable
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = basename($_FILES['photo']['name']);
            $photo_path = $upload_dir . $file_name;

            // Move the file to the upload directory
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Invalid file type.";
            exit();
        }
    }

    // Insert student data
    $sql = "INSERT INTO students (name, birthday, phone_number, parent_phone_number, photo) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $name, $birthday, $phone_number, $parent_phone_number, $photo_path);

    if ($stmt->execute()) {
        $student_id = $stmt->insert_id;

        // Redirect to the add classes page after successful insertion
        header("Location: add_classes.php?student_id=" . $student_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
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
    <div class="container mt-3">

        <form action="add.php" method="post" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="name" class="form-label">Student Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-4">
                    <label for="birthday" class="form-label">Birthday</label>
                    <input type="date" class="form-control" id="birthday" name="birthday" required>
                </div>
                <div class="col-md-4">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" required
                        pattern="[0-9]{10}" placeholder="1234567890">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="parent_phone_number" class="form-label">Parent Phone Number</label>
                    <input type="tel" class="form-control" id="parent_phone_number" name="parent_phone_number" required
                        pattern="[0-9]{10}" placeholder="1234567890">
                </div>

                <div class="col-md-4">
                    <label for="photo" class="form-label">Student Photo</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>