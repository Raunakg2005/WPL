<?php
$page_title = "Manage Movies";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$success = $error = "";

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $movie_id = intval($_GET['id']);
    $result = deleteMovie($movie_id);
    
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}

$stmt = $conn->prepare("SELECT * FROM movies ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();
$movies = [];
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Movies</h1>
                <a href="add-movie.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Movie
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
                    <i class="fas fa-film me-1"></i>
                    Movies List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Poster</th>
                                    <th>Title</th>
                                    <th>Genre</th>
                                    <th>Release Date</th>
                                    <th>Duration</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movies as $movie): ?>
                                    <tr>
                                        <td><?php echo $movie['id']; ?></td>
                                        <td>
                                            <img src="../<?php echo $movie['poster']; ?>" alt="<?php echo $movie['title']; ?>" class="movie-poster">
                                        </td>
                                        <td><?php echo $movie['title']; ?></td>
                                        <td><?php echo $movie['genre']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($movie['release_date'])); ?></td>
                                        <td><?php echo $movie['duration']; ?> mins</td>
                                        <td><?php echo $movie['rating']; ?>/10</td>
                                        <td>
                                            <?php if ($movie['status'] == 'now_showing'): ?>
                                                <span class="badge bg-success">Now Showing</span>
                                            <?php elseif ($movie['status'] == 'coming_soon'): ?>
                                                <span class="badge bg-primary">Coming Soon</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Archived</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit-movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="movies.php?action=delete&id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-danger btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="../movie-details.php?id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-eye"></i>
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
