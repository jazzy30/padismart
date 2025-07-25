<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Staff Registration</title>
    <link rel="stylesheet" href="loginNregister.css" />
  </head>
  <body>
    <div class="left-side">
      <img src="padismart_logo.png" alt="PADiSMART Logo" />
    </div>

    <div class="right-side">
      <div class="register-box">
        <h2>REGISTRATION</h2>

        <label>Department</label>
        <div class="account-type">
          <label><input type="radio" name="dept" value="DOA" /> DOA</label>
          <label><input type="radio" name="dept" value="JTS" /> JTS</label>
        </div>

        <label for="ic_num">User ID</label>
        <input
          type="text"
          id="ic_num"
          name="ic_num"
          placeholder="XXXXXX13XXXX"
          title="Enter your User ID"
          required
        />

        <label for="fullname">Full Name (as per MyKad)</label>
        <input
          type="text"
          id="fullname"
          name="fullname"
          placeholder=""
          title="Enter your full name as per MyKad"
          required
        />

        <label for="email">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder=""
          title="Enter your email address"
          required
        />

        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder=""
          title="Enter a secure password"
          required
        />

        <button type="submit">Register</button>
        
        </div>

        
      </div>
    </div>

    <script>
      function redirectToHomepage() {
        if (jabatan.value == "DOA") {
          window.location.href = "homepage_doa.html"; // Redirect to the specified page
        } else if (jabatan.value == "JTS") {
          window.location.href = "homepage_jts.html"; // Redirect to the specified page
        }
      }
    </script>
  </body>
</html>
