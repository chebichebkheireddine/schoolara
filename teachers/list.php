<?php
require_once '../auth_check.php'; // Inclure la vérification de session

require_once '../config.php';

$sql = "SELECT * FROM teachers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers List</title>
    <link href="../assets/boostarb/style.css" rel="stylesheet">
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
        position: fixed;
    }

    .main-content {
        flex: 1;
        margin-left: 250px;
        padding: 20px;
    }

    .nav-link {
        text-decoration: none;
        color: white;
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
                <a href="list.php" class="nav-link text-white">Manage Teachers</a>
            </li>
            <li class="nav-item">
                <a href="../absence/list.php" class="nav-link text-white">Manage Absences</a>
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
        <h1>Teachers List</h1>
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTeacherModal">Add New
            Teacher</a>

        <div class="mb-3">
            <label for="search" class="form-label">Search:</label>
            <input type="text" id="search" class="form-control" placeholder="Search teachers by name">
        </div>

        <div class="mb-3">
            <label for="sort" class="form-label">Sort By:</label>
            <select id="sort" class="form-select">
                <option value="id">ID</option>
                <option value="name">Teacher Name</option>
            </select>
        </div>

        <table class="table table-bordered" id="teachersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Teacher Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td>
                        <a href="#" class="btn btn-warning edit-teacher" data-id="<?php echo $row['id']; ?>"
                            data-bs-toggle="modal" data-bs-target="#editTeacherModal">Edit</a>
                        <a href="#" class="btn btn-danger delete-teacher" data-id="<?php echo $row['id']; ?>">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="3">No teachers found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel"
        aria-hidden="true">
        <div class="modal-dialog model-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTeacherModalLabel">Add New Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- The form will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Teacher Modal -->
    <div class="modal fade" id="editTeacherModal" tabindex="-1" aria-labelledby="editTeacherModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTeacherModalLabel">Edit Enseignant</h5>
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
        // Load Add Teacher Form in Modal
        $('#addTeacherModal').on('show.bs.modal', function() {
            var modal = $(this);
            $.ajax({
                url: 'add.php',
                success: function(data) {
                    modal.find('.modal-body').html(data);
                }
            });
        });

        // Load Edit Teacher Form in Modal
        $('.edit-teacher').click(function() {
            var teacherId = $(this).data('id');
            var modal = $('#editTeacherModal');
            $.ajax({
                url: 'edit.php',
                data: {
                    id: teacherId
                },
                success: function(data) {
                    modal.find('.modal-body').html(data);
                }
            });
        });

        // Handle Delete Teacher
        $('.delete-teacher').click(function() {
            if (confirm('Are you sure you want to delete this teacher?')) {
                var teacherId = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: 'delete.php',
                    data: {
                        id: teacherId
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
            $('#teachersTable tbody tr').each(function() {
                var teacherName = $(this).find('td:nth-child(2)').text().toLowerCase();
                if (teacherName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Sorting functionality
        $('#sort').on('change', function() {
            var sortBy = $(this).val();
            var rows = $('#teachersTable tbody tr').get();

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
                $('#teachersTable').children('tbody').append(row);
            });
        });

        function getColumnIndex(sortBy) {
            switch (sortBy) {
                case 'id':
                    return 0;
                case 'name':
                    return 1;
            }
        }
    });
    </script>
</body>

</html>

<?php
$conn->close();
?>