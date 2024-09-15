<?php
require_once '../config.php';

$sql = "SELECT * FROM classes";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes List</title>
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
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="list.php" class="nav-link text-white">Manage Classes</a>
            </li>
            <li class="nav-item">
                <a href="../students/list.php" class="nav-link text-white">Manage Students</a>
            </li>
            <li class="nav-item">
                <a href="../teachers/list.php" class="nav-link text-white">Manage Teachers</a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Classes List</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addClassModal">Add New Class</button>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                                <a href="#" class="btn btn-danger delete-class" data-id="<?php echo $row['id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No classes found</td>
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
                    <form id="addClassForm">
                        <div class="mb-3">
                            <label for="className" class="form-label">Class Name</label>
                            <input type="text" class="form-control" id="className" name="className" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Class</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Ajouter une classe via AJAX
            $('#addClassForm').submit(function(e) {
                e.preventDefault();
                var className = $('#className').val();

                $.ajax({
                    type: 'POST',
                    url: 'add.php',
                    data: { name: className },
                    success: function(response) {
                        location.reload(); // Recharger la page après l'ajout
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            // Supprimer une classe via AJAX
            $('.delete-class').click(function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this class?')) {
                    var classId = $(this).data('id');

                    $.ajax({
                        type: 'POST',
                        url: 'delete.php',
                        data: { id: classId },
                        success: function(response) {
                            location.reload(); // Recharger la page après la suppression
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
