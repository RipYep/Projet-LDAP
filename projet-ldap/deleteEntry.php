<?php
// Start the session and check authentication
session_start();
$idUser = NULL;
if (isset($_SESSION['idUser'])) $idUser = $_SESSION['idUser'];

// Check if the user is authenticated
if ($idUser == NULL) {
    header("Location: logout.php");
    exit();
}

// Check if UID is present in the URL
if (!isset($_GET['uid']) || empty($_GET['uid'])) {
    die("UID not specified.");
}

$uid = $_GET['uid'];

// LDAP connection setup
$config = require('ldapConfig.php');
$ldap_host = $config['ldap_host'];
$ldap_admin_dn = $config['ldap_admin_dn'];
$ldap_admin_password = $config['ldap_admin_password'];

$ldap_conn = ldap_connect($ldap_host);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);  // Disable LDAP referrals
if (!$ldap_conn) {
    die("Failed to connect to the LDAP server. " . ldap_error($ldap_conn));
}

// Bind to the LDAP server using admin credentials
if (!ldap_bind($ldap_conn, $ldap_admin_dn, $ldap_admin_password)) {
    die("LDAP authentication failed. " . ldap_error($ldap_conn));
}

// Retrieve all groups the user is a member of
$base_dn_groups = "ou=Groups,dc=iut5-kourou,dc=fr";
$filter_groups = "(memberUid=$uid)";
$attributes_groups = ["cn", "memberUid"];

$search_result_groups = ldap_search($ldap_conn, $base_dn_groups, $filter_groups, $attributes_groups);
if (!$search_result_groups) {
    die("Error while searching for groups for the user.");
}
$entries_groups = ldap_get_entries($ldap_conn, $search_result_groups);

// Remove the user from each group
for ($i = 0; $i < $entries_groups["count"]; $i++) {
    $group_dn = $entries_groups[$i]["dn"];  // Group DN
    $entry_group = [
        "memberUid" => $uid
    ];

    // Remove the user from the group
    if (!ldap_mod_del($ldap_conn, $group_dn, $entry_group)) {
        echo "Error while removing user from group $group_dn. " . ldap_error($ldap_conn);
        ldap_unbind($ldap_conn);
        exit();
    }
}

// Define the DN of the user to delete
$dn_people = "uid=$uid,ou=People,dc=iut5-kourou,dc=fr";

// Delete the user from People
if (!ldap_delete($ldap_conn, $dn_people)) {
    echo "Error while deleting the user from People: " . ldap_error($ldap_conn);
    ldap_unbind($ldap_conn);
    exit();
}

// Close the LDAP connection
ldap_unbind($ldap_conn);

// Redirect to the admin panel with a success message
header("Location: adminPanelLdap.php?success=true");
exit();
?>
