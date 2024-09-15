<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../admin/login.php");
    exit();
}

require_once '../config.php';

$result = $conn->query("
    SELECT absences.id, students.name AS student_name, classes.name AS class_name, absences.date 
    FROM absences 
    JOIN students ON absences.student_id = students.id 
    JOIN classes ON absences.class_id = classes.id
");

$students = $conn->query("SELECT id, name FROM students");
$classes = $conn->query("SELECT id, name FROM classes");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absence List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding: 20px;
            position: fixed; /* Make sidebar fixed */
            top: 0;
            left: 0;
        }
        .main-content {
            margin-left: 250px; /* Adjust for the fixed sidebar width */
            padding: 20px;
            width: calc(100% - 250px); /* Adjust the width of main content */
        }
        .nav-link {
            text-decoration: none; /* Remove underline from nav links */
            color: white; /* Set text color to white */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><a href="../index.php" class="nav-link text-white">Dashboard</a></h2>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="../classes/list.php" class="nav-link text-white">Manage Classes</a>
            </li>
            <li class="nav-item">
                <a href="../students/list.php" class="nav-link text-white">Manage Students</a>
            </li>
            <li class="nav-item">
                <a href="../teachers/list.php" class="nav-link text-white">Manage Teachers</a>
            </li>
            <li class="nav-item">
                <a href="list.php" class="nav-link text-white">Manage Absences</a>
            </li>
            <li class="nav-item">
                <a href="../backup.php" class="nav-link text-white">Backup & Restore</a>
            </li>
            <li class="nav-item">
                <a href="../logout.php" class="nav-link text-white">Logout</a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Absence List</h1>
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAbsenceModal">
            Add Absence
        </button>
        <table class="table table-bordered">
            <thead>
                <tr>
                   
                    <th>Student</th>
                    <th>Class</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        
                        <td><?php echo $row['student_name']; ?></td>
                        <td><?php echo $row['class_name']; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td>
                            <form method="POST" action="delete.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="../index.php" class="btn btn-secondary mt-3">Back to Menu</a>

        <!-- Modal -->
        <div class="modal fade" id="addAbsenceModal" tabindex="-1" aria-labelledby="addAbsenceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAbsenceModalLabel">Add Absence</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="add.php">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student</label>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <?php while ($student = $students->fetch_assoc()): ?>
                                        <option value="<?php echo $student['id']; ?>"><?php echo $student['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="class_id" class="form-label">Class</label>
                                <select class="form-select" id="class_id" name="class_id" required>
                                    <?php while ($class = $classes->fetch_assoc()): ?>
                                        <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Absence</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
