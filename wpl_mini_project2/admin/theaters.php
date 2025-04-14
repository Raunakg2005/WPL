<?php
$page_title = "Manage Theaters";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$success = $error = "";

if (isset($_GET['action']) && ($_GET['action'] == 'activate' || $_GET['action'] == 'deactivate') && isset($_GET['id'])) {
    $theater_id = intval($_GET['id']);
    $status = ($_GET['action'] == 'activate') ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE theaters SET status = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $theater_id);
    
    if ($stmt->execute()) {
        $success = "Theater " . ($_GET['action'] == 'activate' ? 'activated' : 'deactivated') . " successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $theater_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT id FROM shows WHERE theater_id = ?");
    $stmt->bind_param("i", $theater_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = "Cannot delete theater with associated shows. Please delete the shows first or deactivate the theater instead.";
    } else {
        $stmt = $conn->prepare("DELETE FROM theaters WHERE id = ?");
        $stmt->bind_param("i", $theater_id);
        
        if ($stmt->execute()) {
            $success = "Theater deleted successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM theaters ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
$theaters = [];
while ($row = $result->fetch_assoc()) {
    $theaters[] = $row;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Theaters</h1>
                <a href="add-theater.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Theater
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
                    <i class="fas fa-building me-1"></i>
                    Theaters List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Capacity</th>
                                    <th>Facilities</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($theaters as $theater): ?>
                                    <tr>
                                        <td><?php echo $theater['id']; ?></td>
                                        <td><?php echo $theater['name']; ?></td>
                                        <td><?php echo $theater['location']; ?></td>
                                        <td><?php echo $theater['capacity']; ?> seats</td>
                                        <td><?php echo $theater['facilities']; ?></td>
                                        <td>
                                            <?php if ($theater['status'] == 1): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit-theater.php?id=<?php echo $theater['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($theater['status'] == 1): ?>
                                                <a href="theaters.php?action=deactivate&id=<?php echo $theater['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="theaters.php?action=activate&id=<?php echo $theater['id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="theaters.php?action=delete&id=<?php echo $theater['id']; ?>" class="btn btn-sm btn-danger btn-delete">
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