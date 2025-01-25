<?php
include('dbconnect.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$studentId = $data['studentId'];
$email = $data['email'];

// Check if student ID and email match in tb_user
$query = "SELECT * FROM tb_user WHERE u_sno = ? AND u_email = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ss", $studentId, $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) > 0) {
    // Create reset link
    $reset_link = "http://localhost/newpassword.php?id=" . $studentId;
    
    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'chauyingjia04@gmail.com'; // Your actual Gmail
        $mail->Password = '0175678907jia'; // Your Gmail App Password (16-character code)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('chauyingjia04@gmail.com', 'Course Management'); // Remove the extra @gmail.com
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "Click the following link to reset your password: <a href='$reset_link'>Reset Password</a>";
        $mail->AltBody = "Click the following link to reset your password: $reset_link";

        $mail->send();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "Mail could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Student ID and email do not match our records']);
}

mysqli_close($con);
?> 