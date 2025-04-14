<?php
$page_title = "View Message";

require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('messages.php');
}

$message_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM messages WHERE id = ?");
$stmt->bind_param("i", $message_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('messages.php');
}

$message = $result->fetch_assoc();

if ($message['status'] == 'unread') {
    $stmt = $conn->prepare("UPDATE messages SET status = 'read' WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    
    $message['status'] = 'read';
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">View Message</h1>
                <a href="messages.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Messages
                </a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-envelope me-1"></i>
                    Message Details
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <strong>From:</strong>
                        </div>
                        <div class="col-md-10">
                            <?php echo $message['name']; ?> (<?php echo $message['email']; ?>)
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <strong>Subject:</strong>
                        </div>
                        <div class="col-md-10">
                            <?php echo $message['subject']; ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <strong>Date:</strong>
                        </div>
                        <div class="col-md-10">
                            <?php echo date('F d, Y h:i A', strtotime($message['created_at'])); ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-10">
                            <?php if ($message['status'] == 'unread'): ?>
                                <span class="badge bg-primary">Unread</span>
                            <?php else: ?>
                                <span class="badge bg-success">Read</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Message:</strong>
                            <div class="message-content mt-3 p-3 bg-light rounded">
                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="mailto:<?php echo $message['email']; ?>" class="btn btn-primary">
                                <i class="fas fa-reply me-2"></i>Reply
                            </a>
                            <?php if ($message['status'] == 'read'): ?>
                                <a href="messages.php?action=unread&id=<?php echo $message['id']; ?>" class="btn btn-warning">
                                    <i class="fas fa-undo me-2"></i>Mark as Unread
                                </a>
                            <?php endif; ?>
                        </div>
                        <div>
                            <a href="messages.php?action=delete&id=<?php echo $message['id']; ?>" class="btn btn-danger btn-delete">
                                <i class="fas fa-trash me-2"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
include 'includes/footer.php';
?>