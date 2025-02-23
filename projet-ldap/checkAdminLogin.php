<?php
// Paramètres de connexion LDAP
$ldap_host = "ldap://localhost";
$ldap_port = 389;
$base_dn = "dc=iut5-kourou,dc=fr";

// Data from client
$login = NULL;
if (isset($_POST['login'])) $login = $_POST['login'];
$pwd = NULL;
if (isset($_POST['pwd'])) $pwd = $_POST['pwd'];

// Check
if ($login == NULL || $pwd == NULL) {
  header("Location: adminLogin.html");
  exit();
}

$admin_dn = "cn=$login,$base_dn";

// Connect to the LDAP server
$ldap_conn = ldap_connect($ldap_host, $ldap_port);
if (!$ldap_conn) {
    header("Location: adminLogin.html");
    exit();
}

// Set options to avoid deprecated warnings
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

// Bind to the LDAP server as an admin to perform operations
if (ldap_bind($ldap_conn, $admin_dn, $pwd)) {

    // Start the session
    session_start();

    // Store user ID and other session information
    $_SESSION['idUser'] = $admin_dn;

    // Redirect the user to the admin panel
    header("Location: adminPanelLdap.php");
    exit();
} else {
    // If authentication fails, redirect to logout (which will then redirect to index.html)
    header("Location: logoutAdmin.php");
    exit();
}

?>
