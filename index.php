<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin/login.php");
    exit();
}

require_once 'config.php';

// Requêtes pour obtenir le nombre d'étudiants, de professeurs et de classes
$student_count = $conn->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];
$teacher_count = $conn->query("SELECT COUNT(*) AS count FROM teachers")->fetch_assoc()['count'];
$class_count = $conn->query("SELECT COUNT(*) AS count FROM classes")->fetch_assoc()['count'];

// Requêtes pour obtenir le nombre d'étudiants qui ont payé et qui n'ont pas payé
$paid_student_count = $conn->query("SELECT COUNT(*) AS count FROM student_classes  WHERE paid = 1")->fetch_assoc()['count'];
$pending_student_count = $conn->query("SELECT COUNT(*) AS count FROM student_classes  WHERE paid = 0")->fetch_assoc()['count'];

// Requête pour obtenir le nombre d'étudiants par classe, trié du plus grand au plus petit
$class_student_counts = $conn->query("
    SELECT c.name AS class_name, c.num_group AS group_number, COUNT(s.id) AS student_count
    FROM classes c
    LEFT JOIN student_classes  s ON c.id = s.class_id
    GROUP BY c.name, c.num_group
    ORDER BY student_count DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schoolara</title> l
    <link href="assets/boostarb/style.css" rel="stylesheet">
    <!--Code css  -->
    <style>
    body {
        display: flex;
        background-color: #f8f9fa;
    }

    .sidebar {
        width: 250px;
        height: 100vh;
        background-color: #343a40;
        color: white;
        padding: 20px;
        position: fixed;
        top: 0;
        left: 0;
    }

    .main-content {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
        position: relative;
    }

    .nav-link {
        text-decoration: none;
        color: white;
        transition: color 0.3s ease;
    }

    .nav-link:hover {
        color: #adb5bd;
    }

    .logo {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 100px;
    }

    .card-body {
        background-color: #ffffff;
    }

    .table {
        background-color: #ffffff;
    }

    .table thead {
        background-color: #007bff;
        color: white;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .table tbody tr:hover {
        background-color: #e9ecef;
    }

    .btn-custom {
        background-color: #007bff;
        color: white;
        border: none;
    }

    .btn-custom:hover {
        background-color: #0056b3;
        color: white;
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2><a href="index.php" class="nav-link text-white">Dashboard</a></h2>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="classes/list.php" class="nav-link">Manage Classes</a>
            </li>
            <li class="nav-item">
                <a href="students/list.php" class="nav-link">Manage Students</a>
            </li>
            <li class="nav-item">
                <a href="teachers/list.php" class="nav-link">Manage Teachers</a>
            </li>
            <li class="nav-item">
                <a href="absence/list.php" class="nav-link">Manage Absences</a>
            </li>
            <li class="nav-item">
                <a href="backup.php" class="nav-link">Backup & Restore</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link">Logout</a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <img src="./assets/images/s.png" alt="Logo" class="logo">
        <h1 class="mb-4">Welcome to Schoolara</h1>
        <p class="mb-4">Select an option from the menu to get started.</p>

        <!-- Affichage de l'heure et de la date -->
        <div id="datetime" class="mb-4"></div>

        <!-- Section des statistiques -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <p class="card-text"><?php echo $student_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Teachers</h5>
                        <p class="card-text"><?php echo $teacher_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Classes</h5>
                        <p class="card-text"><?php echo $class_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Students Paid</h5>
                        <p class="card-text"><?php echo $paid_student_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Students Pending Payment</h5>
                        <p class="card-text"><?php echo $pending_student_count; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section du nombre d'étudiants par classe -->
        <div class="container mt-4">
            <h2>Number of Students per Class</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Group Number</th>
                        <th>Number of Students</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($class_student_counts as $class_student_count) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($class_student_count['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($class_student_count['group_number']); ?></td>
                        <td><?php echo htmlspecialchars($class_student_count['student_count']); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Fonction pour afficher l'heure et la date actuelles
    function updateDateTime() {
        var now = new Date();
        var datetime = now.toLocaleString();
        document.getElementById('datetime').innerHTML = '<h5>' + datetime + '</h5>';
    }
    setInterval(updateDateTime, 1000); // Mettre à jour chaque seconde
    </script>
</body>

</html>

<?php
$conn->close();
?>