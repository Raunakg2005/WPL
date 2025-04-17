<?php
$page_title = "Manage Messages";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$success = $error = "";

if (isset($_GET['action']) && ($_GET['action'] == 'read' || $_GET['action'] == 'unread') && isset($_GET['id'])) {
    $message_id = intval($_GET['id']);
    $status = ($_GET['action'] == 'read') ? 'read' : 'unread';
    
    $stmt = $conn->prepare("UPDATE messages SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $message_id);
    
    if ($stmt->execute()) {
        $success = "Message marked as " . $status . " successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $message_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    
    if ($stmt->execute()) {
        $success = "Message deleted successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

$stmt = $conn->prepare("SELECT id, name, email, subject, message, status, created_at FROM messages ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Messages</h1>
            </div>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-envelope me-1"></i>
                    Messages List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $message): ?>
                                    <tr class="<?php echo ($message['status'] == 'unread') ? 'table-primary' : ''; ?>">
                                        <td><?php echo $message['id']; ?></td>
                                        <td><?php echo $message['name']; ?></td>
                                        <td><?php echo $message['email']; ?></td>
                                        <td><?php echo $message['subject']; ?></td>
                                        <td><?php echo date('M d, Y h:i A', strtotime($message['created_at'])); ?></td>
                                        <td>
                                            <?php if ($message['status'] == 'unread'): ?>
                                                <span class="badge bg-primary">Unread</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Read</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="view-message.php?id=<?php echo $message['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($message['status'] == 'unread'): ?>
                                                <a href="messages.php?action=read&id=<?php echo $message['id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="messages.php?action=unread&id=<?php echo $message['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="mailto:<?php echo $message['email']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-reply"></i>
                                            </a>
                                            <a href="messages.php?action=delete&id=<?php echo $message['id']; ?>" class="btn btn-sm btn-danger btn-delete">
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