<html>
<body>
<div class="headwrapper">
<!-- -------------------start header------------------- -->
  <div class="headcontainer">
    <div class="headnavbar">
      <div style="float: left;">
          <nav>
        <ul>
          <li><b><a style="font-size: 30px; color: #645bff;">TAB18</a></b></li>
        </ul>
      </nav>
      </div>  


    <center><div class="headlogo"><br>
      <b><a style="font-size: 40px; color: black;">ADMIN</a></b>
    </div></center>

      <nav>
  <ul>
    <li><a style="font-size: 30px;">Hello <u><span onclick="redirectToProfile()"><?php echo $rowuser['username']; ?></span></u>!</a></li> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <li><b><a style="font-size: 30px;" href="#help">Help</a></b></li>
  </ul>
</nav>

    </div>
  </div>

<center>

  <div class="headcontainer">
    <div class="headnavbarMenu">
      <table align="center">
        <tr>

          <td><a href="adminHome.php">Home</a></td>

          <td>
            <div class="headdropdown">
              <button class="headdropbtn">User
                <i class="fa fa-caret-down"></i>
              </button>
              <div class="headdropdown-content">
                <div class="headrow">
                  <div class="headcolumn">
                      <a href='userList.php?user_level=admin'>Admin List</a>
                      <a href='userList.php?user_level=staff'>Staff List</a>
                      <a href='userList.php'>All Users List</a>
                  </div>
                </div>
              </div>
            </div>
          </td>

        </tr>
      </table>
    </div>
  </div>
</center>
</div>
<!-- -------------------end header------------------- -->
<script>
  function redirectToProfile() {
    window.location.href = 'userProfile.php';
  }
</script>
</body>
</html>