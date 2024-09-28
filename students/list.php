<?php
require_once '../auth_check.php'; // Inclure la vérification de session
require_once '../config.php';

// Récupérer les classes pour le champ de sélection dynamique
$sql_classes = "SELECT id, name FROM classes";
$result_classes = $conn->query($sql_classes);

$sql = "SELECT students.id, students.name, students.birthday, students.phone_number, students.parent_phone_number, student_classes.start_date,student_classes.class_id, student_classes.end_date, classes.name as class_name, classes.num_group, student_classes.paid, student_classes.payment_date 
        FROM students 
        JOIN student_classes ON students.id = student_classes.student_id 
        JOIN classes ON student_classes.class_id = classes.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students List</title>
    <link href="../assets/boostarb/style.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>

    <!-- Php code Index  -->
    <?php
    include("../sidebar.php");
    ?>
    <div class="main-content">
        <h1>Students List</h1>

        <!-- Button to open PDF filter modal -->
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#pdfOptionsModal">Download
            PDF</a>

        <!-- PDF Options Modal -->
        <div class="modal fade" id="pdfOptionsModal" tabindex="-1" aria-labelledby="pdfOptionsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pdfOptionsModalLabel">Filter PDF Options</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="pdfOptionsForm" action="generate_pdf.php" method="GET">
                            <div class="mb-3">
                                <label for="filter" class="form-label">Filter PDF:</label>
                                <select id="filter" name="filter" class="form-select">
                                    <option value="all">All</option>
                                    <option value="class">By Class</option>
                                    <option value="payment_end_red">Payment End Red</option>
                                </select>
                            </div>
                            <div class="mb-3" id="classSelect">
                                <label for="class_id" class="form-label">Select Class:</label>
                                <select id="class_id" name="class_id" class="form-select">
                                    <?php while ($row_class = $result_classes->fetch_assoc()): ?>
                                        <option value="<?php echo $row_class['id']; ?>"><?php echo $row_class['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-secondary mb-3">Generate PDF</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add New
            Student</a>

        <div class="mb-3">
            <label for="search" class="form-label">Search:</label>
            <input type="text" id="search" class="form-control" placeholder="Search students by name or class">
        </div>

        <div class="mb-3">
            <label for="sort" class="form-label">Sort By:</label>
            <select id="sort" class="form-select">
                <option value="id">ID</option>
                <option value="name">Student Name</option>
                <option value="class_name">Class</option>
                <option value="start_date">Enrollment Date</option>
                <option value="end_date">Payment End</option>
                <option value="paid">Payment Status</option>
            </select>
        </div>

        <table class="table table-bordered" id="studentsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Enrollment Date</th>
                    <th>Payment End</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $payment_end = strtotime($row['end_date']);
                        $today = strtotime(date("Y-m-d"));
                        $diff = ($payment_end - $today) / (60 * 60 * 24);
                        $color_class = '';
                        if ($diff > 2) {
                            $color_class = 'green';
                        } elseif ($diff > 0 && $diff <= 2) {
                            $color_class = 'orange';
                        } else {
                            $color_class = 'red';
                        }
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['class_name'] . " (" . $row['num_group'] . ")"; ?></td>
                            <td><?php echo $row['start_date']; ?></td>
                            <td class="<?php echo $color_class; ?>"><?php echo $row['end_date']; ?></td>
                            <td><?php echo $row['paid'] ? 'Paid' : 'Not Paid'; ?></td>
                            <td>
                                <a href="#" class="btn btn-info view-student" data-id="<?php echo $row['id']; ?>"
                                    data-bs-toggle="modal" data-bs-target="#viewStudentModal">View</a>
                                <a href="#" class="btn btn-warning edit-student" data-id="<?php echo $row['id']; ?>"
                                    data-bs-toggle="modal" data-bs-target="#editStudentModal">Edit</a>
                                <a href="#" class="btn btn-danger delete-student"
                                    data-id="<?php echo $row['class_id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No students found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- The form will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- The form will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- View Student Modal -->
    <div class="modal fade" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStudentModalLabel">Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- The student details will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Show/Hide class select based on filter option
            $('#filter').on('change', function() {
                if ($(this).val() === 'class') {
                    $('#classSelect').show();
                } else {
                    $('#classSelect').hide();
                }
            });

            // Load Add Student Form via AJAX
            $('#addStudentModal').on('show.bs.modal', function(e) {
                var modal = $(this);
                $.ajax({
                    url: 'add.php',
                    method: 'GET',
                    success: function(data) {
                        modal.find('.modal-body').html(data);
                    }
                });
            });

            // Load Edit Student Form via AJAX
            $('#editStudentModal').on('show.bs.modal', function(e) {
                var modal = $(this);
                var studentId = $(e.relatedTarget).data('id');
                $.ajax({
                    url: 'edit.php',
                    method: 'GET',
                    data: {
                        id: studentId
                    },
                    success: function(data) {
                        modal.find('.modal-body').html(data);
                    }
                });
            });

            // Load View Student Details via AJAX
            $('#viewStudentModal').on('show.bs.modal', function(e) {
                var modal = $(this);
                var studentId = $(e.relatedTarget).data('id');
                $.ajax({
                    url: 'view.php',
                    method: 'GET',
                    data: {
                        id: studentId
                    },
                    success: function(data) {
                        modal.find('.modal-body').html(data);
                    }
                });
            });

            // Handle delete student action
            $('#studentsTable').on('click', '.delete-student', function(e) {
                e.preventDefault();
                var studentId = $(this).data('id');
                if (confirm('Are you sure you want to delete this student?')) {
                    $.ajax({
                        url: 'delete.php',
                        method: 'POST',
                        data: {
                            id: studentId
                        },
                        success: function(response) {
                            if (response === 'success') {
                                location.reload();
                            } else {
                                alert('' + response);
                                location.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred: ' + error);
                        }
                    });
                }
            });

            // Real-time search functionality
            $('#search').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                $('#studentsTable tbody tr').each(function() {
                    var studentName = $(this).find('td').eq(1).text().toLowerCase();
                    var className = $(this).find('td').eq(2).text().toLowerCase();
                    if (studentName.includes(searchText) || className.includes(searchText)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Sort functionality
            $('#sort').on('change', function() {
                var sortBy = $(this).val();
                var rows = $('#studentsTable tbody tr').get();
                rows.sort(function(a, b) {
                    var keyA = $(a).children('td').eq($('#sort option').index($(
                        '#sort option[value="' + sortBy + '"]'))).text().toLowerCase();
                    var keyB = $(b).children('td').eq($('#sort option').index($(
                        '#sort option[value="' + sortBy + '"]'))).text().toLowerCase();
                    return keyA.localeCompare(keyB);
                });
                $.each(rows, function(index, row) {
                    $('#studentsTable').children('tbody').append(row);
                });
            });
        });
    </script>
</body>

</html>