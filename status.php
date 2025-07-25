<?php
include("config.php");
require_once 'session_check.php';

// Initialize variables
$uploadSuccess = false;
$uploadError = '';
$fileName = '';
$fullname = '';
$ic_no = '';
$application_no = '';
$applicant_id = $_SESSION['applicant_id'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Start transaction with proper isolation level
  $conn->begin_transaction();

  try {
    // Fetch updated user info
    $sqluser = "SELECT fullname, ic_no, application_no FROM applicant WHERE applicant_id = ?";
    $stmt = $conn->prepare($sqluser);
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $rowuser = $result->fetch_assoc();
      $fullname = $rowuser['fullname'];
      $ic_no = $rowuser['ic_no'];
      $application_no = $rowuser['application_no'];
    }

    $conn->commit();
  } catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
  }
}

// Allow URL parameters to override (if needed)
if (isset($_GET['fullname'])) {
  $fullname = $_GET['fullname'];
}
if (isset($_GET['ic_no'])) {
  $ic_no = $_GET['ic_no'];
}

// Fetch existing data if available
$sql = "SELECT * FROM applicant WHERE applicant_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $fullname = $row['fullname'] ?? '';
  $ic_no = $row['ic_no'] ?? '';
  //$application_no = $row['application_no'] ?? '';
  $phone_no = $row['phone_no'] ?? '';
  $address = $row['address'] ?? '';
  $dependent = $row['dependents'] ?? '';
  $income = $row['income'] ?? '';
}

// Fetch next of kin data if available
$sql_kin = "SELECT * FROM kin WHERE applicant_id = ?";
$stmt_kin = $conn->prepare($sql_kin);
$stmt_kin->bind_param("i", $applicant_id);
$stmt_kin->execute();
$result_kin = $stmt_kin->get_result();

if ($result_kin->num_rows > 0) {
  $row_kin = $result_kin->fetch_assoc();
  $kin_fullname = $row_kin['kin_fullname'] ?? '';
  $kin_ic_no = $row_kin['kin_ic_no'] ?? '';
  $kin_phone_no = $row_kin['kin_phone_no'] ?? '';
  $kin_address = $row_kin['kin_address'] ?? '';
  $relationship = $row_kin['relationship'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Land Owner Information</title>
  <link rel="stylesheet" href="default.css" />
</head>

<body>
  <div class="sidebar">
    <div>
      <img src="ingenuity.png" alt="Logo" />
      <nav>
        <ul>
          <li><a href="#" class="active">LAND OWNER INFORMATION</a></li>
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

    <h2>LAND OWNER INFORMATION</h2>

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
            <label for="fullname">FULL NAME</label>
            <input type="text" id="fullname" name="fullname"
              value="<?php echo htmlspecialchars($fullname); ?>" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="ic_no">IC NO.</label>
            <input type="text" id="ic_no" name="ic_no"
              value="<?php echo htmlspecialchars($ic_no); ?>" />
          </div>
          <div class="form-group">
            <label for="phone_no">PHONE NO.</label>
            <input type="text" id="phone_no" name="phone_no" />
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

        <div class="form-row">
          <div class="form-group">
            <label for="dependent">NO. OF DEPENDENTS</label>
            <input type="number" id="dependent" name="dependent" />
          </div>
          <div class="form-group">
            <label for="income">TOTAL INCOME (RM)</label>
            <input type="text" id="income" name="income" />
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

        <h2>NEXT OF KIN INFORMATION</h2>

        <div class="form-row">
          <div class="form-group">
            <label for="kin_fullname">FULL NAME</label>
            <input type="text" id="kin_fullname" name="kin_fullname" />
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="kin_ic_no">IC NO.</label>
            <input type="text" id="kin_ic_no" name="kin_ic_no" />
          </div>
          <div class="form-group">
            <label for="kin_phone_no">PHONE NO.</label>
            <input type="text" id="kin_phone_no" name="kin_phone_no" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="kin_address">ADDRESS</label>
            <textarea
              id="kin_address"
              name="kin_address"
              rows="1"
              style="resize: none; overflow: hidden"></textarea>
          </div>
        </div>

        <div class="form-group">
          <label for="relationship">RELATIONSHIP WITH OWNER</label>
          <input type="text" id="relationship" name="relationship" />
        </div>

        <div class="button-group">
          <button type="submit" name="submit">NEXT</button>
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