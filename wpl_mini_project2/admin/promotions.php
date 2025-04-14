<?php
$page_title = "Manage Promotions";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$success = $error = "";

if (isset($_GET['action']) && ($_GET['action'] == 'activate' || $_GET['action'] == 'deactivate') && isset($_GET['id'])) {
    $promotion_id = intval($_GET['id']);
    $status = ($_GET['action'] == 'activate') ? 'active' : 'inactive';
    
    $stmt = $conn->prepare("UPDATE promotions SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $promotion_id);
    
    if ($stmt->execute()) {
        $success = "Promotion " . ($_GET['action'] == 'activate' ? 'activated' : 'deactivated') . " successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $promotion_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM promotions WHERE id = ?");
    $stmt->bind_param("i", $promotion_id);
    
    if ($stmt->execute()) {
        $success = "Promotion deleted successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

$stmt = $conn->prepare("SELECT * FROM promotions ORDER BY end_date DESC");
$stmt->execute();
$result = $stmt->get_result();
$promotions = [];
while ($row = $result->fetch_assoc()) {
    $promotions[] = $row;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Promotions</h1>
                <a href="add-promotion.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Promotion
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
                    <i class="fas fa-percentage me-1"></i>
                    Promotions List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Code</th>
                                    <th>Discount</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($promotions as $promotion): ?>
                                    <tr>
                                        <td><?php echo $promotion['id']; ?></td>
                                        <td><?php echo $promotion['title']; ?></td>
                                        <td><code><?php echo $promotion['code']; ?></code></td>
                                        <td>
                                            <?php if ($promotion['discount_type'] == 'percentage'): ?>
                                                <?php echo $promotion['discount_value']; ?>%
                                            <?php else: ?>
                                                $<?php echo number_format($promotion['discount_value'], 2); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($promotion['start_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($promotion['end_date'])); ?></td>
                                        <td>
                                            <?php if ($promotion['status'] == 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit-promotion.php?id=<?php echo $promotion['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($promotion['status'] == 'active'): ?>
                                                <a href="promotions.php?action=deactivate&id=<?php echo $promotion['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-ban"></i>
                                                
                                                </a>
                                            <?php else: ?>
                                                <a href="promotions.php?action=activate&id=<?php echo $promotion['id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="promotions.php?action=delete&id=<?php echo $promotion['id']; ?>" class="btn btn-sm btn-danger btn-delete">
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