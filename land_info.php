<?php
include("config.php");
require_once 'session_check.php';

// Initialize variables
$uploadSuccess = false;
$uploadError = '';
$fileName = '';
$fullname = '';
$ic_num = '';
$application_no = '';
$applicant_id = $_SESSION['applicant_id'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Start transaction
  $conn->begin_transaction();

  try {
    // Prepare the SQL statement for land info
    $stmt = $conn->prepare("
            UPDATE land_info SET 
                case_app = ?,
                status = ?,
                date_applied = ?,
                location = ?,
                area = ?,
                ownership = ?,
                crop_type = ?,
                use_method = ?
                district = ?,
                soil_type = ?,
                crop_yield = ?,
                latitude = ?,
                longitude = ?
            WHERE applicant_id = ?
        ");

    $stmt->bind_param(
      "ssssdsssssddii",
      $_POST['case_app'],
      $_POST['status'],
      $_POST['date_applied'],
      $_POST['location'],
      $_POST['area'],
      $_POST['ownership'],
      $_POST['crop_type'],
      $_POST['use_method'],
      $_POST['district'],
      $_POST['soil_type'],
      $_POST['crop_yield'],
      $_POST['latitude'],
      $_POST['longitude'],
      //$_POST['staff_id'],  // Assuming you have this value
      $_POST['land_id']    // You need to identify which record to update
    );

    $stmt->execute();

    // After successful form submission and transaction commit:
    $conn->commit();

    // Check applicant role for redirection
    $role_sql = "SELECT role FROM applicant WHERE applicant_id = ?";
    $role_stmt = $conn->prepare($role_sql);
    $role_stmt->bind_param("i", $applicant_id);
    $role_stmt->execute();
    $role_result = $role_stmt->get_result();

    if ($role_result->num_rows > 0) {
      $role_row = $role_result->fetch_assoc();
      $role = strtolower($role_row['role']);

      if ($role == 'individu' || $role == 'individual') {
        header("Location: individual_summary.php");
      } elseif ($role == 'syarikat' || $role == 'company') {
        header("Location: company_summary.php");
      } else {
        header("Location: individual_summary.php"); // Default fallback
      }
    } else {
      header("Location: individual_summary.php"); // Fallback if role not found
    }
    exit();
  } catch (Exception $e) {
    $conn->rollback();
    $uploadError = "Database error: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Land Information</title>
  <link rel="stylesheet" href="default.css" />
</head>

<body>
  <div class="sidebar">
    <div>
      <img src="ingenuity.png" alt="Logo" />
      <nav>
        <ul>
          <li><a href="#">LAND OWNER INFORMATION</a></li>
          <li><a href="#" class="active">LAND INFORMATION</a></li>
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
      <div class="step">1</div>
      <div class="step active">2</div>
      <div class="step">3</div>
    </div>

    <h2>LAND INFORMATION</h2>

    <div class="form-container">
      <form action="land_info.php" method="POST" enctype="multipart/form-data">
        <div class="form-row">
          <div class="form-group">
            <label for="land_location">LAND LOCATION</label>
            <input type="text" id="land_location" name="land_location" />
          </div>
          <div class="form-group">
            <label for="area">ESTIMATED AREA (ha)</label>
            <input type="text" id="area" name="area" />
          </div>
        </div>

        <div class="form-group">
          <label for="ownership_status">OWNERSHIP STATUS</label>
          <select>
            <option>INDIVIDUAL</option>
            <option>INHERTITENCE</option>
            <option>PARTNERSHIP</option>
            <option>NATIVE CUSTOMARY RIGHTS (NCR)</option>
          </select>
        </div>
        <br>
        <div class="form-group">
          <label for="crop_type">CROP TYPE</label>
          <select>
            <option>PADDY</option>
            <option>OIL PALM</option>
            <option>FRUITS</option>
            <option>RUBBER</option>
          </select>
        </div>
        <br>
        <div class="form-group">
          <label for="land_use">LAND USE METHOD</label>
          <select>
            <option>PARTNERSHIP (LV)</option>
            <option>RENTAL</option>
            <option>CONTRACT</option>
            <option>RUBBER</option>
          </select>
        </div>

        <div class="button-group">
          <button type="button" onclick="history.back()">BACK</button>
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