<?php
require_once '../auth_check.php'; // Inclure la vérification de session
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $class_ids = $_POST['class_id'];
    $enrollment_dates = $_POST['enrollment_date'];
    $months = $_POST['months'];
    $paid_statuses = $_POST['paid'];
    $payment_dates = $_POST['payment_date'];

    for ($i = 0; $i < count($class_ids); $i++) {
        $class_id = $class_ids[$i];
        $enrollment_date = $enrollment_dates[$i];
        $end_date = date('Y-m-d', strtotime("+{$months[$i]} months", strtotime($enrollment_date)));
        $paid = ($paid_statuses[$i] === 'Paid') ? 1 : 0;
        $payment_date = $payment_dates[$i];

        $sql = "INSERT INTO student_classes (student_id, class_id, start_date, end_date, paid, payment_date) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iissis', $student_id, $class_id, $enrollment_date, $end_date, $paid, $payment_date);
        $stmt->execute();
    }

    // Redirect to the students list page after successful insertion
    header("Location: list.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Classes for Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Add Classes for Student</h1>
        <form action="add_classes.php" method="post">
            <input type="hidden" name="student_id" value="<?php echo $_GET['student_id']; ?>">
            <div id="class-container">
                <div class="row mb-3 class-entry">
                    <div class="col-md-2">
                        <label for="class_id_0" class="form-label">Class</label>
                        <select class="form-control" id="class_id_0" name="class_id[]" required>
                            <?php
                            // Connexion à la base de données pour récupérer les classes
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
                    <div class="col-md-2">
                        <label for="enrollment_date_0" class="form-label">Enrollment Date</label>
                        <input type="date" class="form-control" id="enrollment_date_0" name="enrollment_date[]"
                            required>
                    </div>
                    <div class="col-md-2">
                        <label for="months_0" class="form-label">Months</label>
                        <input type="number" class="form-control" id="months_0" name="months[]" required>
                    </div>
                    <div class="col-md-2">
                        <label for="paid_0" class="form-label">Payment Status</label>
                        <select class="form-control" id="paid_0" name="paid[]" required>
                            <option value="Paid">Paid</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="payment_date_0" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date_0" name="payment_date[]" required>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-secondary" id="addClass">Add Another Class</button>
            <button type="submit" class="btn btn-primary">Add Classes</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('addClass').addEventListener('click', function() {
        const classContainer = document.getElementById('class-container');
        const classEntry = document.querySelector('.class-entry').cloneNode(true);
        const index = classContainer.children.length;

        // Update IDs and names for the cloned elements
        classEntry.querySelectorAll('select, input').forEach(function(element) {
            element.id = element.id.replace(/_\d+$/, `_${index}`);
            element.name = element.name.replace(/\[\d+\]$/, `[]`);
            if (element.tagName === 'INPUT') {
                element.value = ''; // Clear the value for input fields
            }
        });

        classContainer.appendChild(classEntry);
    });
    </script>
</body>

</html>