<!DOCTYPE html>
<html>
<head>
  <!-- CSS files -->
  <link rel='stylesheet' type='text/css' href='./css/web.css' media='screen' />
  <link rel='stylesheet' type='text/css' href='./css/userPanel.css' media='screen' />
  <link rel='stylesheet' type='text/css' href='./css/icon.css' media='screen' />
  <link rel='stylesheet' type='text/css' href='./css/01_mobile.css' media='screen' />
  <link rel='stylesheet' type='text/css' href='./css/02_fonts.css' media='screen' />
  <link rel='stylesheet' type='text/css' href='./css/03_icons.css' media='screen' />

  <!-- JS files -->
  <script type='text/javascript' src='./js/jquery-3.7.0.min.js'></script>
  <script type='text/javascript' src='./js/web.js'></script>

  <meta charset="utf-8">
  <title>Guest</title>

  <!-- Favicon -->
  <link rel='icon' type='image/png' href='./medias/ldapLogo.jpeg' />

</head>

<header>
  <nav>
    <ul>
      <li><a href="./logout.php">Logout <i class='logoutIcon'>&#xe9ba;</i></a></li>
    </ul>
  </nav>
</header>

<body>
  <?php
  // LDAP connection parameters
  $ldapHost = "ldap://localhost";
  $ldapPort = 389;
  $baseDn = "ou=People,dc=iut5-kourou,dc=fr";

  // Connect to the LDAP server
  $ldapConn = ldap_connect($ldapHost, $ldapPort);
  if (!$ldapConn) {
    die("Unable to connect to the LDAP server.");
  }

  // Set options to avoid deprecation warnings
  ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);

  // Check if the connection was successful
  if (ldap_bind($ldapConn)) {
    echo "<h1>List of users :</h1>";
  } else {
    header("Location: logout.php");
    exit();
  }

  // Perform the search in the People branch to retrieve all users
  $searchFilter = "(objectClass=person)";
  $result = ldap_search($ldapConn, $baseDn, $searchFilter);

  if (!$result) {
    echo "There may be no one registered in the server.";
  }

  // Retrieve the entries returned by the search
  $entriesPeople = ldap_get_entries($ldapConn, $result);

  if ($entriesPeople["count"] > 0) {
    // Start table
    echo "<table border='1' class='usersTable'>";
      echo "<tr><th>Name</th><th>Email</th><th>Phone</th></tr>";

      // Loop through each entry and display in table
      for ($i = 0; $i < $entriesPeople["count"]; $i++) {
        $cn = $entriesPeople[$i]["cn"][0]; // Full name

        // Check if email is set, otherwise use default value
        if (isset($entriesPeople[$i]["mail"][0])) {
          $email = $entriesPeople[$i]["mail"][0];
        } else {
          $email = "No email registered.";
        }

        // Check if phone number is set, otherwise use default value
        if (isset($entriesPeople[$i]["mobile"][0])) {
          $phone = $entriesPeople[$i]["mobile"][0];
        } else {
          $phone = "No phone number registered.";
        }

        // Replace "00" with "+" for the phone number if it's not the default value
        if ($phone !== "No phone number registered.") {
          $phone = preg_replace('/^00/', '+', $phone);
        }

        // Display user information
        echo "<tr>";
          echo "<td>" . htmlspecialchars($cn) . "</td>";
          echo "<td>" . htmlspecialchars($email) . "</td>";
          echo "<td>" . htmlspecialchars($phone) . "</td>";
          echo "</tr>";
        }

        echo "</table>";
      } else {
        echo "No users found.";
      }

      // Close the LDAP connection
      ldap_unbind($ldapConn);
      ?>
    </body>
    </html>
