<?php
include("config.php");
require_once 'session_check.php';

if (isset($_POST['Register'])) {
    // Get form data
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $ic_no = mysqli_real_escape_string($conn, $_POST['ic_no']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Hash the password (important for security)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Validate inputs
    if (empty($role) || empty($ic_no) || empty($fullname) || empty($email) || empty($password)) {
        echo "<script>alert('All fields are required'); window.location.href='public_register.php';</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format'); window.location.href='public_register.php';</script>";
        exit();
    }

    // Check if email already exists
    $check_email = "SELECT email FROM applicant WHERE email = '$email'";
    $result = $conn->query($check_email);
    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered'); window.location.href='public_register.php';</script>";
        exit();
    }

    // Check if ic number already exists
    $check_ic = "SELECT ic_no FROM applicant WHERE ic_no = '$ic_no'";
    $result = $conn->query($check_ic);
    if ($result->num_rows > 0) {
        echo "<script>alert('IC number already registered'); window.location.href='public_register.php';</script>";
        exit();
    }

    // Prepare the SQL statement with prepared statements
    $stmt = $conn->prepare("INSERT INTO applicant (role, ic_no, fullname, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $role, $ic_no, $fullname, $email, $hashed_password);

    if ($stmt->execute()) {
        // Get the newly inserted user ID
        $applicant_id = $stmt->insert_id;

        // Store user data in session
        $_SESSION['applicant_id'] = $applicant_id;
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $email;

        // Redirect based on role
        if ($role == 'individual') {
            header("Location: individual_info.php");
        } elseif ($role == 'company') {
            header("Location: company_info.php");
        }
        exit();
    } else {
        echo "<script>alert('Registration failed: " . $conn->error . "'); window.history.back();</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Public Registration</title>
    <link rel="stylesheet" href="loginNregister.css">
    <script src="js/sweetalert.min.js"></script>
    <script>
        // Display any error messages from URL parameters
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            if (error) {
                alert(error);
            }
        };
    </script>
</head>

<body>
    <div class="left-side">
        <img src="padismart_logo.png" alt="PADiSMART Logo" />
    </div>

    <div class="right-side">
        <div class="register-box">
            <h2>REGISTRATION</h2>
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

            <form action="public_register.php" method="POST">
                <label>Type of Account</label>
                <div class="account-type">
                    <label>
                        <input type="radio" name="role" value="individual" required />
                        Individual
                    </label>
                    <label>
                        <input type="radio" name="role" value="company" required />
                        Private Company
                    </label>
                </div>

                <label for="ic_no">User ID</label>
                <input
                    type="text"
                    id="ic_no"
                    name="ic_no"
                    placeholder=""
                    title="Enter your User ID"
                    required />

                <label for="fullname">Full Name (as per MyKad)</label>
                <input
                    type="text"
                    id="fullname"
                    name="fullname"
                    placeholder=""
                    title="Enter your full name as per MyKad"
                    required />

                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder=""
                    title="Enter your email address"
                    required />

                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder=""
                    title="Enter a secure password"
                    required
                    minlength="8" />

                <button type="submit" name="Register">Register</button>
            </form>
                <p class="auth-link">Already have an account? <a href="login_page.php">Login Here!</a></p> 
        </div>
    </div>
</body>

</html>