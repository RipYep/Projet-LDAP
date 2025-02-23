<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Data from client (filtered)
$firstName = NULL;
if (preg_match("/^.{0,100}$/", $_POST['firstName'])) $firstName = $_POST['firstName'];
$lastName = NULL;
if (preg_match("/^.{0,100}$/", $_POST['lastName'])) $lastName = $_POST['lastName'];
$pwd = NULL;
if (preg_match("/^.{0,100}$/", $_POST['pwd'])) $pwd = $_POST['pwd'];

// Check if the required fields are provided
if ($firstName == NULL || $lastName == NULL || $pwd == NULL) {
    header("Location: logout.php");
    exit();
}

// Paramètres de connexion LDAP
$ldap_host = "ldap://localhost";  // Adresse de votre serveur LDAP
$ldap_port = 389;                 // Port LDAP, généralement 389 pour une connexion non sécurisée
$base_dn = "dc=iut5-kourou,dc=fr";  // Base DN pour la recherche
$user_dn = "uid=$firstName.$lastName,ou=People,$base_dn";  // DN de l'utilisateur à partir de son prénom et nom

// Connect to the LDAP server
$ldap_conn = ldap_connect($ldap_host, $ldap_port);
if (!$ldap_conn) {
    header("Location: logout.php");
    exit();
}

// Set options to avoid deprecated warnings
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);  // Disable referrals

// Bind as the user to verify their credentials
$bind = ldap_bind($ldap_conn, $user_dn, $pwd);

// Check if the binding was successful
if ($bind) {
    // Start the session
    session_start();

    // Store user ID and other session information
    $_SESSION['idUser'] = "$firstName.$lastName";  // Store the username for later use

    // Redirect the user to the user panel
    header("Location: userPanel.php");
    exit();
} else {
    // If authentication fails, redirect to logout (which will then redirect to index.html)
    header("Location: logout.php");
    exit();
}

// Close LDAP connection
ldap_unbind($ldap_conn);
?>
