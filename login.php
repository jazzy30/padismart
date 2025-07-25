<?php
// padismart login
require_once "config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Login'])) {
    // post user id and password
    $ic_num = mysqli_real_escape_string($conn, $_POST['ic_num']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // search in applicant table first
    $sql = "SELECT * FROM applicant WHERE ic_num = '$ic_num' AND password = '$password'";
    $query = $conn->query($sql);
    
    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $_SESSION['applicant_id'] = $row['applicant_id'];
        $_SESSION['role'] = $row['role'];
        
        if ($row['role'] == "individual") {
            header("Location: individual_info.php");
        } else if ($row['role'] == "company") {
            header("Location: company_info.php");
        }
        exit();
    } 
    // if not found in applicant, search in staff table
    else {
        $sql = "SELECT * FROM staff WHERE ic_num = '$ic_num' AND password = '$password'";
        $query = $conn->query($sql);
        
        if ($query->num_rows > 0) {
            $row = $query->fetch_assoc();
            $_SESSION['staff_id'] = $row['staff_id'];
            $_SESSION['dept'] = $row['dept'];
            
            if ($row['dept'] == "doa") {
                header("Location: homepage_doa.php");
            } else if ($row['dept'] == "jts") {
                header("Location: homepage_jts.php");
            }
            exit();
        } else {
            $error = "You have entered the wrong user ID/password!";
        }
    }
}
?>