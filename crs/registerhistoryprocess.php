<?php
include('crssession.php');
include('dbconnect.php');

if(isset($_GET['tid'])) {
    $tid = $_GET['tid'];
    $query = "SELECT r_section, r_status FROM tb_registration WHERE r_tid = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $tid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $registration = mysqli_fetch_assoc($result);
    
    header('Content-Type: application/json');
    echo json_encode([
        'section' => $registration['r_section'],
        'status' => $registration['r_status']
    ]);
}
?>