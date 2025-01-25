<?php
// Add this near the top of the file
$notification_query = "SELECT COUNT(*) as count FROM tb_notifications 
                      WHERE n_user = ? AND n_status = 'unread'";
$notify_stmt = mysqli_prepare($con, $notification_query);
mysqli_stmt_bind_param($notify_stmt, "s", $_SESSION['u_sno']);
mysqli_stmt_execute($notify_stmt);
$notify_result = mysqli_stmt_get_result($notify_stmt);
$unread_count = mysqli_fetch_assoc($notify_result)['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Course Registration System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


<style>
.footer {
   position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   background-color: mediumaquamarine;
   color: white;
   text-align: center;
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="student.php">CRS Student</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor01">
      <ul class="navbar-nav me-auto">
      <li class="nav-item">
          <a class="nav-link" href="profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="student.php">Course Registration
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active " href="courseview.php">View Your Courses
            <span class="visually-hidden">(current)</span>
          </a>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="notifyDropdown" role="button" 
               data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell" style="margin-left: 970px;"></i>
                <?php if($unread_count > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notifyDropdown">
                <?php
                $notifications_query = "SELECT * FROM tb_notifications 
                                      WHERE n_user = ? 
                                      ORDER BY n_date DESC LIMIT 5";
                $notify_stmt = mysqli_prepare($con, $notifications_query);
                mysqli_stmt_bind_param($notify_stmt, "s", $_SESSION['u_sno']);
                mysqli_stmt_execute($notify_stmt);
                $notifications = mysqli_stmt_get_result($notify_stmt);
                
                while($notification = mysqli_fetch_assoc($notifications)):
                ?>
                    <a class="dropdown-item <?php echo $notification['n_status'] == 'unread' ? 'bg-light' : ''; ?>" 
                       href="notifications.php">
                        <small class="text-muted"><?php echo date('M d, H:i', strtotime($notification['n_date'])); ?></small>
                        <div><?php echo $notification['n_message']; ?></div>
                        <?php if($notification['n_reason']): ?>
                            <small class="text-danger">Reason: <?php echo $notification['n_reason']; ?></small>
                        <?php endif; ?>
                    </a>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($notifications) == 0): ?>
                    <div class="dropdown-item">No notifications</div>
                <?php endif; ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-center" href="notifications.php">View All</a>
            </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
