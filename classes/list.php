<?php
require_once '../auth_check.php'; // Inclure la vérification de session
require_once '../config.php';

$sql = "SELECT classes.*, teachers.name AS teacher_name 
        FROM classes 
        LEFT JOIN teachers ON classes.teacher_id = teachers.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes List</title>
    <link href="../assets/boostarb/style.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <?php
    include("../sidebar.php");
    ?>
    <div class="main-content">
        <h1>Classes List</h1>
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addClassModal">Add New
            Class</a>

        <div class="mb-3">
            <label for="search" class="form-label">Search:</label>
            <input type="text" id="search" class="form-control" placeholder="Search classes by name or teacher">
        </div>

        <div class="mb-3">
            <label for="sort" class="form-label">Sort By:</label>
            <select id="sort" class="form-select">
                <option value="id">ID</option>
                <option value="name">Class Name</option>
                <option value="num_group">Group Number</option>
                <option value="start_date">Start Date</option>
                <option value="number_of_months">Number of Months</option>
                <option value="teacher_name">Responsible Teacher</option>
            </select>
        </div>

        <table class="table table-bordered" id="classesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Group Number</th>
                    <th>Start Date</th>
                    <th>Number of Months</th>
                    <th>Responsible Teacher</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['num_group']; ?></td>
                    <td><?php echo $row['start_date']; ?></td>
                    <td><?php echo $row['number_of_months']; ?></td>
                    <td><?php echo $row['teacher_name'] ?: 'Not Assigned'; ?></td>
                    <td>
                        <a href="#" class="btn btn-warning edit-class" data-id="<?php echo $row['id']; ?>"
                            data-bs-toggle="modal" data-bs-target="#editClassModal">Edit</a>

                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7">No classes found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Class Modal -->
    <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClassModalLabel">Add New Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- The form will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Class Modal -->
    <div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClassModalLabel">Edit Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- The form will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Load Add Class Form in Modal
        $('#addClassModal').on('show.bs.modal', function() {
            var modal = $(this);
            $.ajax({
                url: 'add.php',
                success: function(data) {
                    modal.find('.modal-body').html(data);
                }
            });
        });

        // Load Edit Class Form in Modal
        $('.edit-class').click(function() {
            var classId = $(this).data('id');
            var modal = $('#editClassModal');
            $.ajax({
                url: 'edit.php',
                data: {
                    id: classId
                },
                success: function(data) {
                    modal.find('.modal-body').html(data);
                }
            });
        });

        // Handle Delete Class
        $('.delete-class').click(function() {
            if (confirm('Are you sure you want to delete this class?')) {
                var classId = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: 'delete.php',
                    data: {
                        id: classId
                    },
                    success: function(response) {
                        alert(response); // Display the response message
                        location.reload(); // Reload the page after deletion
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        // Real-time search functionality
        $('#search').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('#classesTable tbody tr').each(function() {
                var className = $(this).find('td:nth-child(2)').text().toLowerCase();
                var teacherName = $(this).find('td:nth-child(6)').text().toLowerCase();
                if (className.includes(searchTerm) || teacherName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Sorting functionality
        $('#sort').on('change', function() {
            var sortBy = $(this).val();
            var rows = $('#classesTable tbody tr').get();

            rows.sort(function(a, b) {
                var A = $(a).children('td').eq(getColumnIndex(sortBy)).text().toUpperCase();
                var B = $(b).children('td').eq(getColumnIndex(sortBy)).text().toUpperCase();

                if (A < B) {
                    return -1;
                }
                if (A > B) {
                    return 1;
                }
                return 0;
            });

            $.each(rows, function(index, row) {
                $('#classesTable').children('tbody').append(row);
            });
        });

        function getColumnIndex(sortBy) {
            switch (sortBy) {
                case 'id':
                    return 0;
                case 'name':
                    return 1;
                case 'num_group':
                    return 2;
                case 'start_date':
                    return 3;
                case 'number_of_months':
                    return 4;
                case 'teacher_name':
                    return 5;
            }
        }
    });
    </script>
</body>

</html>

<?php
$conn->close();
?>