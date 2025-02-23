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
  <script type='text/javascript' src='./js/ajxUserModifyInfo.js'></script>

  <meta charset="utf-8">
  <title>User Panel</title>

  <!-- Icon -->
  <link rel='icon' type='image/png' href='./medias/ldapLogo.jpeg' />

</head>

<header>
  <nav>
    <ul>
      <li><a href='./wordpress/wp-admin/'>Sign in on wordpress account</a></li>
      <li><a href="./logout.php">Logout <i class='logoutIcon'>&#xe9ba;</i></a></li>
    </ul>
  </nav>
</header>

<body>

  <?php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);

  // Start the session to get the current user
  session_start();

  // Check if the user is logged in
  if (!isset($_SESSION['idUser'])) {
    header("Location: logout.php");
    exit();
  }

  // Retrieve the username from session
  $userId = $_SESSION['idUser'];  // Format: firstName.lastName
  list($firstName, $lastName) = explode('.', $userId);

  // LDAP connection parameters
  $ldapHost = "ldap://localhost";  // LDAP server address
  $ldapPort = 389;                 // Port for non-secure LDAP connection
  $baseDn = "dc=iut5-kourou,dc=fr";  // Base DN for search

  // Connect to the LDAP server
  $ldapConn = ldap_connect($ldapHost, $ldapPort);
  if (!$ldapConn) {
    die("Could not connect to LDAP server");
  }

  // Set LDAP options
  ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
  ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);  // Disable referrals

  // Search for the user's information in the People branch
  $userDn = "uid=$firstName.$lastName,ou=People,$baseDn";
  $searchFilter = "(uid=$firstName.$lastName)";
  $searchResult = ldap_search($ldapConn, $baseDn, $searchFilter);
  $userInfo = ldap_get_entries($ldapConn, $searchResult);

  // Check if user is found
  if ($userInfo['count'] == 0) {
    header("Location: logout.php");
  }

  // Get user attributes from People branch (e.g., firstName, lastName, email, etc.)
  $userAttributes = $userInfo[0];

  // Get groups from Groups branch
  $groupsFilter = "(memberUid=$firstName.$lastName)";
  $groupsSearch = ldap_search($ldapConn, "ou=Groups,$baseDn", $groupsFilter);
  $groupsInfo = ldap_get_entries($ldapConn, $groupsSearch);

  // Display user information
  ?>

  <h1>Welcome, <?php echo htmlspecialchars($userAttributes['cn'][0]); ?>!</h1>

  <h2>Your Information</h2>
  <p><strong>Full Name:</strong> <?php echo htmlspecialchars($userAttributes['cn'][0]); ?></p>

  <p><strong>Email:</strong>
    <?php
    if (isset($userAttributes['mail'])) {
      echo htmlspecialchars($userAttributes['mail'][0]);
    } else {
      echo 'No email registered.';
    }
    ?></p>

    <p><strong>Phone:</strong>
    <?php
    if (isset($userAttributes['mobile'])) {
      $phoneNumber = $userAttributes['mobile'][0];

      // Use preg_match to check and replace '00' at the beginning with '+'
      if (preg_match('/^00/', $phoneNumber)) {
        $phoneNumber = preg_replace('/^00/', '+', $phoneNumber);
      }

      // Affichage du numéro de téléphone avec l'icône d'édition
      echo "
      <span id='newPhone'>" . htmlspecialchars($phoneNumber) . "</span>
      <button class='editButton' data-uid='$userId'>
      <i class='editIcon'>&#xe3c9;</i><span class='editText'>Edit</span>
      </button>
      </p>";
    } else {
      echo '<p>No phone number registered.</p>';
    }
    ?>

    <h2>Group you belong to :</h2>
    <ul>
      <?php
      if ($groupsInfo['count'] > 0) {
        for ($i = 0; $i < $groupsInfo['count']; $i++) {
          if (isset($groupsInfo[$i]['cn'])) {
            echo '<li>' . htmlspecialchars($groupsInfo[$i]['cn'][0]) . '</li>';
          }
        }
      }
      ?>
    </ul>

    <!-- Modal for password -->
    <div id='passwordModal' class='modal'>
      <div class="modalContent">
          <h2>Enter your password to confirm modification</h2>
          <input id='passwordInputModal' class='passwordInputModal' type="password" name='pwd' placeholder='Enter your password' required>
          <button class="submitPassword">Confirm</button>
          <button class="cancelPassword">Cancel</button>
      </div>
    </div>

  </body>
  </html>
