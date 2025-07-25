<?php
// padismart login
require_once "config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Login'])) {
  // post user id and password
  $ic_no = mysqli_real_escape_string($conn, $_POST['ic_no']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  // search in applicant table first
  $sql = "SELECT * FROM applicant WHERE ic_no = '$ic_no' AND password = '$password'";
  $query = $conn->query($sql);

  if ($query->num_rows > 0) {
    $row = $query->fetch_assoc();
    $_SESSION['applicant_id'] = $row['applicant_id'];
    $_SESSION['role'] = $row['role'];

    if ($row['role'] == "individual") {
      header("Location: individual_info.php?id=" . $row['applicant_id']);
    } else if ($row['role'] == "company") {
      header("Location: company_info.php?id=" . $row['applicant_id']);
    }
    exit();
  }
  // if not found in applicant, search in staff table
  else {
    $sql = "SELECT * FROM staff WHERE staff_ic_no = '$staff_ic_no' AND staff_password = '$staff_password'";
    $query = $conn->query($sql);

    if ($query->num_rows > 0) {
      $row = $query->fetch_assoc();
      $_SESSION['staff_id'] = $row['staff_id'];
      $_SESSION['dept'] = $row['dept'];

      if ($row['dept'] == "doa") {
        header("Location: homepage_doa.php?id=" . $row['staff_id']);
      } else if ($row['dept'] == "jts") {
        header("Location: homepage_jts.php?id=" . $row['staff_id']);
      }
      exit();
    } else {
      $_SESSION['error'] = "You have entered the wrong user ID/password!";
      header("Location: login_page.php"); // redirect back to login page
      exit();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="loginNregister.css">
  <script src="js/sweetalert.min.js"></script>
</head>

<body>
  <div class="left-side">
    <img src="padismart_logo.png" alt="PADiSMART Logo" />
  </div>

  <div class="right-side">
    <div class="login-box">
      <h2>LOGIN</h2>
      <?php if (isset($error)): ?>
        <script>
          setTimeout(function() {
            swal({
              title: '<?php echo $error; ?>',
              icon: 'warning',
              timer: 3000
            });
          }, 1);
        </script>
      <?php endif; ?>

      <form action="login_page.php" method="POST">
        <input type="text" name="ic_no" placeholder="User ID" title="Masukkan ID pengguna anda" required />
        <input type="password" name="password" placeholder="Password" title="Masukkan kata laluan anda" required />
        <button type="submit" name="Login" class="login">Login</button>
      </form>

      <p>No account yet? <a href="public_register.php">Register Now!</a></p>
      <p>Register as staff? <a href="staff_register.php">Click here!</a></p>
    </div>
  </div>
</body>

</html>