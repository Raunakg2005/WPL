<?php
$page_title = "Manage Reviews";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$success = $error = "";

if (isset($_GET['action']) && ($_GET['action'] == 'approve' || $_GET['action'] == 'reject') && isset($_GET['id'])) {
    $review_id = intval($_GET['id']);
    $status = ($_GET['action'] == 'approve') ? 'approved' : 'rejected';
    
    $stmt = $conn->prepare("UPDATE reviews SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $review_id);
    
    if ($stmt->execute()) {
        $success = "Review " . $status . " successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $review_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review_id);
    
    if ($stmt->execute()) {
        $success = "Review deleted successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

$stmt = $conn->prepare("
    SELECT r.*, u.username, u.email, m.title as movie_title
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN movies m ON r.movie_id = m.id
    ORDER BY r.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Reviews</h1>
            </div>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-star me-1"></i>
                    Reviews List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Movie</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr class="<?php echo ($review['status'] == 'pending') ? 'table-warning' : ''; ?>">
                                        <td><?php echo $review['id']; ?></td>
                                        <td><?php echo $review['username']; ?></td>
                                        <td><?php echo $review['movie_title']; ?></td>
                                        <td>
                                            <div class="rating-stars">
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <?php if ($i <= $review['rating']): ?>
                                                        <i class="fas fa-star text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                                <span class="ms-2"><?php echo $review['rating']; ?>/10</span>
                                            </div>
                                        </td>
                                        <td><?php echo substr($review['comment'], 0, 100) . (strlen($review['comment']) > 100 ? '...' : ''); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($review['created_at'])); ?></td>
                                        <td>
                                            <?php if ($review['status'] == 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php elseif ($review['status'] == 'rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($review['status'] == 'pending' || $review['status'] == 'rejected'): ?>
                                                <a href="reviews.php?action=approve&id=<?php echo $review['id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($review['status'] == 'pending' || $review['status'] == 'approved'): ?>
                                                <a href="reviews.php?action=reject&id=<?php echo $review['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="reviews.php?action=delete&id=<?php echo $review['id']; ?>" class="btn btn-sm btn-danger btn-delete">
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