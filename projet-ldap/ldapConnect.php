<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Data from client
$login = NULL;
if (isset($_POST['login'])) $login = $_POST['login'];
$pwd = NULL;
if (isset($_POST['pwd'])) $pwd = $_POST['pwd'];

// Check
if ($login == NULL || $pwd == NULL) {
  header("Location: ldapConnect.html");
  exit();
}

// LDAP server address
$ldap_host = "10.188.94.171"; // Change to your LDAP server address

// Connect to LDAP server
$ldap_conn = ldap_connect($ldap_host);

?>
