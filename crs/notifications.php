<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}
include('dbconnect.php');

// Mark all notifications as read
$mark_read_query = "UPDATE tb_notifications SET n_status = 'read' 
                   WHERE n_user = ? AND n_status = 'unread'";
$mark_stmt = mysqli_prepare($con, $mark_read_query);
mysqli_stmt_bind_param($mark_stmt, "s", $_SESSION['u_sno']);
mysqli_stmt_execute($mark_stmt);

// Get all notifications
$notifications_query = "SELECT * FROM tb_notifications 
                      WHERE n_user = ? 
                      ORDER BY n_date DESC";
$notify_stmt = mysqli_prepare($con, $notifications_query);
mysqli_stmt_bind_param($notify_stmt, "s", $_SESSION['u_sno']);
mysqli_stmt_execute($notify_stmt);
$notifications = mysqli_stmt_get_result($notify_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .notification-item {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        .notification-item.unread {
            border-left-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .notification-time {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .notification-type-Approved {
            color: #198754;
        }
        .notification-type-Rejected {
            color: #dc3545;
        }
        .notification-reason {
            font-size: 0.9rem;
            color: #dc3545;
            margin-top: 0.5rem;
            padding: 0.5rem;
            background-color: #f8d7da;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include('headerstudent.php'); ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <h2> Notifications</h2>
            <div style="width: 100px;"></div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Notifications</h5>
                <?php 
                $total = mysqli_num_rows($notifications);
                if($total > 0): 
                ?>
                    <span class="badge bg-light text-primary">
                        Total: <?php echo $total; ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if(mysqli_num_rows($notifications) > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php while($notification = mysqli_fetch_assoc($notifications)): ?>
                            <div class="list-group-item notification-item <?php echo $notification['n_status'] == 'unread' ? 'unread' : ''; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1 notification-type-<?php echo $notification['n_type']; ?>">
                                        <i class="fas <?php echo $notification['n_type'] == 'Approved' ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                        <?php echo $notification['n_type']; ?>
                                    </h6>
                                    <small class="notification-time">
                                        <i class="far fa-clock"></i>
                                        <?php echo date('M d, Y h:i A', strtotime($notification['n_date'])); ?>
                                    </small>
                                </div>
                                <p class="mb-1 mt-2"><?php echo $notification['n_message']; ?></p>
                                <?php if($notification['n_reason']): ?>
                                    <div class="notification-reason">
                                        <i class="fas fa-info-circle"></i> 
                                        Reason: <?php echo $notification['n_reason']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No notifications yet</h5>
                        <p class="text-muted mb-0">When you receive notifications, they will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 