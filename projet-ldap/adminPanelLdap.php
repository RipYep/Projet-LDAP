<!DOCTYPE html>
<html>
  <head>
    <!-- CSS files -->
    <link rel='stylesheet' type='text/css' href='./css/web.css' media='screen' />
    <link rel='stylesheet' type='text/css' href='./css/panel.css' media='screen' />
    <link rel='stylesheet' type='text/css' href='./css/icon.css' media='screen' />
    <link rel='stylesheet' type='text/css' href='./css/01_mobile.css' media='screen' />
    <link rel='stylesheet' type='text/css' href='./css/02_fonts.css' media='screen' />
    <link rel='stylesheet' type='text/css' href='./css/03_icons.css' media='screen' />

    <!-- JS files -->
    <script type='text/javascript' src='./js/jquery-3.7.0.min.js'></script>
    <script type='text/javascript' src='./js/web.js'></script>
    <script type='text/javascript' src='./js/ajxModifyEntry.js'></script>

    <meta charset="utf-8">
    <title>Admin Panel</title>

    <!-- Icon -->
    <link rel='icon' type='image/png' href='./medias/ldapLogo.jpeg' />

  </head>

  <header>
    <nav>
      <ul>
        <li><a href="./adminAddLdap.php">Add entry</a></li>
        <li><a href='./wordpress/wp-admin/'>Sign in on wordpress account</a></li>
        <li><a href="./logoutAdmin.php">Logout <i class='logoutIcon'>&#xe9ba;</i></a></li>
      </ul>
    </nav>
  </header>
  <body>
<?php
// Start the session and check for user authentication
session_start();
$idUser = NULL;
if (isset($_SESSION['idUser'])) $idUser = $_SESSION['idUser'];

// Check if the user is authenticated
if ($idUser == NULL) {
    header("Location: logoutAdmin.php");
    exit();
}

// Use preg_match to extract the part before the comma in the DN
preg_match('/cn=([^,]+)/', $idUser, $matches);

// The extracted username is stored in $matches[1]
$login = $matches[1];

// Check if the user is an admin (cn=admin)
if ($login !== 'admin') {
  header("Location: logoutAdmin.php");
    exit();
}

// Display the username without the cn= prefix
echo "<h1>Welcome $login !</h1>";

// LDAP connection
$ldapHost = "ldap://localhost";
$ldapConn = ldap_connect($ldapHost);
if (!$ldapConn) {
    die("Failed to connect to LDAP server.");
}
ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);

// Search entries in People
$baseDnPeople = "ou=People,dc=iut5-kourou,dc=fr";
$filterPeople = "(objectClass=inetOrgPerson)";
$attributesPeople = ["uid", "cn", "mail", "mobile"]; // Attributes to retrieve

// Search groups in Groups
$baseDnGroups = "ou=Groups,dc=iut5-kourou,dc=fr";
$filterGroups = "(objectClass=posixGroup)";
$attributesGroups = ["cn", "memberUid"]; // Attributes to retrieve

// Apply search filter if available
$searchQuery = NULL;
if (preg_match("/^.{1,50}$/", $_GET['search'])) $searchQuery = $_GET['search'];

if (!empty($searchQuery)) {
    // Only search by uid in People
    $filterPeople = "(uid=*$searchQuery*)";
    // Search for groups by memberUid or group name
    $filterGroups = "(|(cn=*$searchQuery*)(memberUid=*$searchQuery*))";
}

// Search users in People
$searchResultPeople = ldap_search($ldapConn, $baseDnPeople, $filterPeople, $attributesPeople);
if (!$searchResultPeople) {
    echo "No one was found in People, are you sure your database isn't empty?";
}
$entriesPeople = ldap_get_entries($ldapConn, $searchResultPeople);

// Search groups in Groups
$searchResultGroups = ldap_search($ldapConn, $baseDnGroups, $filterGroups, $attributesGroups);
if (!$searchResultGroups) {
  echo "No one was found in Groups, are you sure your database isn't empty?";
}
$entriesGroups = ldap_get_entries($ldapConn, $searchResultGroups);

// Display users in People
echo "<h2 class='people'>Users in People <i class='rotateIcon'>&#xe313</i></h2>";
echo "<section class='peopleSection'>";

// Search form
echo "<form method='GET' id='searchForm' class='searchForm'>
        <input type='text' name='search' id='searchInput' placeholder='Search...' value='" . htmlspecialchars($searchQuery) . "'/>
        <input type='submit' value='Search'/>
      </form>";

echo "<table border='1' class='usersTable'>";
echo "<tr><th>UID</th><th>Name</th><th>Email</th><th>Phone</th><th>Action</th></tr>";
for ($i = 0; $i < $entriesPeople["count"]; $i++) {
    $uid = $entriesPeople[$i]["uid"][0];
    $cn = $entriesPeople[$i]["cn"][0];

    // Check if email is set, otherwise assign "Not available"
    if (isset($entriesPeople[$i]["mail"][0])) {
        $email = $entriesPeople[$i]["mail"][0];
    } else {
        $email = "No email registered.";
    }
    // Check if phone is set, otherwise assign "Not available"
    if (isset($entriesPeople[$i]["mobile"][0])) {
        $phone = $entriesPeople[$i]["mobile"][0];
    } else {
        $phone = "No phone number registered.";
    }

    // Replace "00" with "+" in the phone number for display
    if ($phone !== "No phone number registered.") {
        $phone = preg_replace('/^00/', '+', $phone);
    }
    // Display user information
    echo "<tr><td>" . htmlspecialchars($uid) . "</td><td>" . htmlspecialchars($cn) . "</td><td>" . htmlspecialchars($email) . "</td>";
    echo "<td>
            <span id='newPhone'>" . htmlspecialchars($phone) . "</span>
            <button class='editButton' dataUid='$uid'><i class='editIcon'>&#xe3c9;</i><span class='editText'>Edit</span></button>
          </td>";

    // Link to delete the user
    echo "<td class='deleteCell'><a class='deleteEntry' href='deleteEntry.php?uid=" . urlencode($uid) . "'><i class='deleteIcon'>&#xe5cd;</i><span class='deleteText'>Delete</span></a></td>";
    echo "</tr>";
}
echo "</table>";
echo "</section>";

// Display Groups results
echo "<h2 class='groups'>Groups in Groups <i class='rotateIcon'>&#xe313</i></h2>";
echo "<section class='groupsSection'>";

if ($entriesGroups['count'] > 0) {
  // Create the table
  echo "<table border='1' class='groupsTable'>";

  // Create the first row for group headers
  echo "<tr>";

  // Loop through all groups in LDAP and create table headers for each group
  $groupNames = [];  // Array to store group names
  foreach ($entriesGroups as $entry) {
    if (isset($entry['cn'])) {
      $groupNames[] = $entry['cn'][0];  // Collect all group names
    }
  }

  // Display table headers dynamically for each group
  foreach ($groupNames as $groupName) {
    echo "<th class='groupHeader'>" . htmlspecialchars($groupName) . "</th>";
  }

  echo "</tr>"; // Close the header row

  // Loop through all groups and prepare a list of members for each group
  $groupMembers = [];

  // Initialize empty arrays for each group
  foreach ($groupNames as $groupName) {
    $groupMembers[$groupName] = [];
  }

  // Loop through all entries to find members of each group
  foreach ($entriesGroups as $entry) {
    if (isset($entry["cn"])) {
      $groupName = $entry["cn"][0];  // Get the group name
      if (isset($entry["memberuid"])) {
        // Add each member to the corresponding group array
        foreach ($entry["memberuid"] as $memberUid) {
          $groupMembers[$groupName][] = $memberUid;
        }
      }
    }
  }

  // Prepare the rows: each row corresponds to a single member across multiple groups
  $rows = [];

  // For each member in each group, only add it to the row if it matches the search query
  foreach ($groupMembers as $groupName => $members) {
    foreach ($members as $memberUid) {
      // Filter members by search query
      if (empty($searchQuery) || strpos(strtolower($memberUid), strtolower($searchQuery)) !== false) {
        $rows[] = [
        'group' => $groupName,
        'member' => htmlspecialchars($memberUid)
        ];
      }
    }
  }

  // Now, create a table row for each matching member, ensuring no empty cells
  // We will group by member and display them under their respective group columns
  $membersPerGroup = [];

  foreach ($rows as $row) {
    $group = $row['group'];
    $member = $row['member'];

    // Collect members per group
    $membersPerGroup[$group][] = $member;
  }

  // Find the maximum number of members in any group
  $maxMembers = max(array_map('count', $membersPerGroup));

  // Display the rows dynamically
  for ($i = 0; $i < $maxMembers; $i++) {
    echo "<tr>";

      // Display each group's member in its own column
      foreach ($groupNames as $groupName) {
        if (isset($membersPerGroup[$groupName][$i])) {
          // Display the member in the current column
          echo "<td class='membersColumn'>" . $membersPerGroup[$groupName][$i] . "</td>";
        } else {
          // If no member for this group, leave the cell empty
          echo "<td class='membersColumn'></td>";
        }
      }

      echo "</tr>";  // Close the row
    }

    echo "</table>";
  } else {
    echo "No matching groups found.";
  }
  echo "</section>";

// Close LDAP connection
ldap_unbind($ldapConn);
?>
</body>

</html>
