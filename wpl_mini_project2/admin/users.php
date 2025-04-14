<?php
$page_title = "Manage Users";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$success = $error = "";

if (isset($_GET['action']) && ($_GET['action'] == 'activate' || $_GET['action'] == 'deactivate') && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $status = ($_GET['action'] == 'activate') ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND is_admin = 0");
    $stmt->bind_param("ii", $status, $user_id);
    
    if ($stmt->execute()) {
        $conn->commit(); 
        $success = "User " . ($_GET['action'] == 'activate' ? 'activated' : 'deactivated') . " successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = "Cannot delete user with associated bookings. Please delete the bookings first or deactivate the user instead.";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $success = "User deleted successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}

$stmt = $conn->prepare("
    SELECT id, username, email, full_name, created_at, status
    FROM users
    WHERE is_admin = 0
    ORDER BY created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Users</h1>
                <a href="add-user.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New User
                </a>
            </div>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i>
                    Users List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Full Name</th>
                                    <th>Registered On</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo $user['username']; ?></td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td><?php echo $user['full_name']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <?php if ($user['status'] == 1): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user['status'] == 1): ?>
                                                <a href="users.php?action=deactivate&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="users.php?action=activate&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
include 'includes/footer.php';
?>