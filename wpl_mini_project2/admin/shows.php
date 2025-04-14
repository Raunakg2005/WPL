<?php
$page_title = "Manage Shows";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$success = $error = "";

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $show_id = intval($_GET['id']);
    $result = deleteShow($show_id);
    
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}

$stmt = $conn->prepare("
    SELECT s.*, m.title as movie_title, t.name as theater_name, t.location as theater_location
    FROM shows s
    JOIN movies m ON s.movie_id = m.id
    JOIN theaters t ON s.theater_id = t.id
    ORDER BY s.show_date DESC, s.show_time ASC
");
$stmt->execute();
$result = $stmt->get_result();
$shows = [];
while ($row = $result->fetch_assoc()) {
    $shows[] = $row;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Shows</h1>
                <a href="add-show.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Show
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
                    <i class="fas fa-calendar-alt me-1"></i>
                    Shows List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Movie</th>
                                    <th>Theater</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Price</th>
                                    <th>Available Seats</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shows as $show): ?>
                                    <tr>
                                        <td><?php echo $show['id']; ?></td>
                                        <td><?php echo $show['movie_title']; ?></td>
                                        <td><?php echo $show['theater_name']; ?> (<?php echo $show['theater_location']; ?>)</td>
                                        <td><?php echo date('M d, Y', strtotime($show['show_date'])); ?></td>
                                        <td><?php echo date('h:i A', strtotime($show['show_time'])); ?></td>
                                        <td>$<?php echo number_format($show['price'], 2); ?></td>
                                        <td><?php echo $show['available_seats']; ?></td>
                                        <td>
                                            <?php if (strtotime($show['show_date'] . ' ' . $show['show_time']) > time()): ?>
                                                <span class="badge bg-success">Upcoming</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Past</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit-show.php?id=<?php echo $show['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="shows.php?action=delete&id=<?php echo $show['id']; ?>" class="btn btn-sm btn-danger btn-delete">
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