<?php
// Start the session and check for authentication
session_start();
$idUser = NULL;
if (isset($_SESSION['idUser'])) $idUser = $_SESSION['idUser'];

// Check if the user is authenticated
if ($idUser == NULL) {
    header("Location: logout.php");
    exit();
}

// Data received from AJAX (filtered)
if (isset($_POST['data'])) $data = json_decode($_POST['data'], true);
$userUid = NULL;
if (preg_match("/^.{3,50}$/", $data['userUid'])) $userUid = $data['userUid'];
$countryCode = NULL;
if (preg_match("/^\+?[0-9]{1,3}$/", $data['countryCode'])) $countryCode = $data['countryCode'];
$newPhone = NULL;
if (preg_match("/^\+?[0-9]{6,17}$/", $data['newPhone'])) $newPhone = $data['newPhone'];

// Validation for phone number length
if (strlen($newPhone) < 7) {
    echo json_encode(["success" => false, "error" => "Phone number must be at least 7 characters long."]);
    exit();
}

// Validation
$success = true;
if ($userUid == NULL || $countryCode == NULL || $newPhone == NULL) $success = false;

// LDAP connection
$config = require('ldapConfig.php');
$ldap_host = $config['ldap_host'];
$ldap_admin_dn = $config['ldap_admin_dn'];
$ldap_admin_password = $config['ldap_admin_password'];

$ldap_conn = ldap_connect($ldap_host);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

// Check LDAP connection
if (!$ldap_conn) {
    echo json_encode(["success" => false, "error" => "Failed to connect to LDAP"]);
    exit();
}

// Bind to LDAP with admin credentials
if (!ldap_bind($ldap_conn, $ldap_admin_dn, $ldap_admin_password)) {
    echo json_encode(["success" => false, "error" => "LDAP authentication failed"]);
    exit();
}

// Prepare the modification
$dn = "uid=$userUid,ou=People,dc=iut5-kourou,dc=fr";
$fullPhone = $countryCode . $newPhone;
$entry = ["mobile" => $fullPhone];

// Perform the modification in LDAP
if (!ldap_modify($ldap_conn, $dn, $entry)) {
    ldap_unbind($ldap_conn);
    echo json_encode(["success" => false, "error" => ldap_error($ldap_conn)]);
    exit();
}

// Close LDAP connection
ldap_unbind($ldap_conn);

// Send a success response back to AJAX
echo json_encode(["success" => true, "fullPhone"=>$fullPhone, "modifiedEntry"=>$userUid]);
?>
