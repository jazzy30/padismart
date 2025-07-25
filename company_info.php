<?php
include("config.php");
require_once 'session_check.php';

// Initialize variables
$uploadSuccess = false;
$uploadError = '';
$fileName = '';
$company_name = '';
$company_reg_no = '';
$application_no = '';
$applicant_id = $_SESSION['applicant_id'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // File upload handling
  if (isset($_FILES['ic_file']) && $_FILES['ic_file']['error'] == UPLOAD_ERR_OK) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["ic_file"]["name"]);
    $targetFile = $targetDir . uniqid() . '_' . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Validate file
    if ($fileType != "pdf") {
      $uploadError = "Only PDF files are allowed.";
    } elseif ($_FILES["ic_file"]["size"] > 5000000) {
      $uploadError = "File is too large. Maximum size is 5MB.";
    } elseif (move_uploaded_file($_FILES["ic_file"]["tmp_name"], $targetFile)) {
      $uploadSuccess = true;
      $fileName = $targetFile;
    } else {
      $uploadError = "Sorry, there was an error uploading your file.";
    }
  }

  // Start transaction
  $conn->begin_transaction();

  try {
    // First, check the current application_no in the database
    $check_sql = "SELECT application_no FROM applicant WHERE applicant_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $applicant_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
      $row = $check_result->fetch_assoc();
      $current_app_no = $row['application_no'];

      // Generate new application number if current is empty/0
      if (empty($current_app_no) || $current_app_no == '0') {
        // Get the next available ID
        $max_sql = "SELECT MAX(applicant_id) as max_id FROM applicant";
        $max_result = $conn->query($max_sql);
        $next_id = 1;

        if ($max_result && $max_result->num_rows > 0) {
          $max_row = $max_result->fetch_assoc();
          $next_id = $max_row['max_id'] + 1;
        }

        $application_no = $next_id . '/PSMART/' . date('Y');
      } else {
        // Keep existing application number
        $application_no = $current_app_no;
      }
    } else {
      // Handle case where applicant isn't found
      die("Applicant record not found");
    }

    // Then proceed with your update
    $stmt = $conn->prepare("
        UPDATE applicant SET 
            company_name = ?,
            company_reg_no = ?,
            phone_no = ?,
            address = ?,
            application_no = ?,
            ic_file = ?
        WHERE applicant_id = ?
    ");

    $stmt->bind_param(
      "ssssssi",
      $_POST['company_name'],
      $_POST['company_reg_no'],
      $_POST['phone_no'],
      $_POST['address'],
      $application_no,  // This will be either newly generated or existing
      $fileName,
      $applicant_id
    );

    $stmt->execute();

    // Prepare the SQL statement for next of kin info
    $stmt_kin = $conn->prepare("
            INSERT INTO kin (
                kin_id,
                kin_company_name,
                kin_company_reg_no,
                kin_phone_no,
                kin_address,
                relationship
            ) VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                kin_company_name = VALUES(kin_company_name),
                kin_company_reg_no = VALUES(kin_company_reg_no),
                kin_phone_no = VALUES(kin_phone_no),
                kin_address = VALUES(kin_address),
                relationship = VALUES(relationship)
        ");

    $stmt_kin->bind_param(
      "isssss",
      $applicant_id,
      $_POST['kin_company_name'],
      $_POST['kin_company_reg_no'],
      $_POST['kin_phone_no'],
      $_POST['kin_address'],
      $_POST['relationship']
    );

    $stmt_kin->execute();

    $conn->commit();

    // Redirect to next page after successful submission
    header("Location: land_info.php");
    exit();
  } catch (Exception $e) {
    $conn->rollback();
    $uploadError = "Database error: " . $e->getMessage();
  }
}

// Fetch existing data if available
$sql = "SELECT * FROM applicant WHERE applicant_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $company_name = $row['company_name'] ?? '';
  $company_reg_no = $row['company_reg_no'] ?? '';
  $application_no = $row['application_no'] ?? '';
  $phone_no = $row['phone_no'] ?? '';
  $address = $row['address'] ?? '';
  $dependent = $row['dependents'] ?? '';
  $income = $row['income'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Company Information</title>
  <link rel="stylesheet" href="default.css" />
</head>

<body>
  <div class="sidebar">
    <div>
      <img src="ingenuity.png" alt="Logo" />
      <nav>
        <ul>
          <li><a href="#" class="active">COMPANY INFORMATION</a></li>
          <li><a href="#">LAND INFORMATION</a></li>
          <li><a href="#">DECLARATION</a></li>
          <li><a href="#">STATUS</a></li>
        </ul>
      </nav>
    </div>
    <div class="logout-container">
      <a href="logout.php" class="logout"> <!-- Changed from # to logout.php -->
        <img src="logout.png" alt="Logout" />
        <span>Log Out</span>
      </a>
    </div>
  </div>

  <div class="main-content">
    <div class="steps">
      <div class="step active">1</div>
      <div class="step">2</div>
      <div class="step">3</div>
    </div>

    <h2>COMPANY INFORMATION</h2>

    <div class="form-container">
      <form action="individual_info.php" method="POST" enctype="multipart/form-data">
        <div class="form-row">
          <div class="form-group">
            <label for="application_no">APPLICATION NO.</label>
            <input type="text" id="application_no" name="application_no"
              value="<?php echo htmlspecialchars($application_no); ?>" readonly />
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="company_name">COMPANY NAME</label>
            <input type="text" id="company_name" name="company_name" />
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="company_reg_no">COMPANY REGISTRATION NO.</label>
            <input type="text" id="company_reg_no" name="company_reg_no" />
          </div>
          <div class="form-group">
            <label for="company_phone_no">PHONE NO.</label>
            <input
              type="text"
              id="company_phone_no"
              name="company_phone_no" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="address">ADDRESS</label>
            <textarea
              id="address"
              name="address"
              rows="1"
              style="resize: none; overflow: hidden"></textarea>
          </div>
        </div>
        <!-- Add this in your form where you want the upload to appear -->
        <div class="form-row">
          <div class="form-group">
            <label for="ic_file">UPLOAD SUPPORTING DOCUMENTS (PDF only)</label>
            <input type="file" id="ic_file" name="ic_file" accept=".pdf" required>
            <small class="file-hint">Maximum file size: 5MB</small>
          </div>
        </div>

        <?php if ($uploadError): ?>
          <div class="error-message" style="color: red; margin: 10px 0;">
            <?php echo htmlspecialchars($uploadError); ?>
          </div>
        <?php endif; ?>

        <?php if ($uploadSuccess): ?>
          <div class="success-message" style="color: green; margin: 10px 0;">
            File uploaded successfully!
          </div>
        <?php endif; ?>

        <div class="button-group">
          <button type="submit">NEXT</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-expanding textareas
      const addressFields = document.querySelectorAll('textarea[id="address"], textarea[id="kin_address"]');
      addressFields.forEach(textarea => {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
        textarea.addEventListener('input', function() {
          this.style.height = 'auto';
          this.style.height = this.scrollHeight + 'px';
        });
      });

      // Form validation before submission
      const form = document.querySelector('form');
      form.addEventListener('submit', function(e) {
        // Add any client-side validation here
        // Example: Check required fields
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
          if (!field.value.trim()) {
            field.style.borderColor = 'red';
            isValid = false;
          }
        });

        if (!isValid) {
          e.preventDefault();
          alert('Please fill in all required fields');
        }
      });
    });
  </script>
</body>

</html>