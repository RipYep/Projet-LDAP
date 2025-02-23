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

$userUid = $idUser;

// Data received from AJAX (filtered)
if (isset($_POST['data'])) $data = json_decode($_POST['data'], true);
$countryCode = NULL;
if (preg_match("/^\+?[0-9]{1,5}$/", $data['countryCode'])) $countryCode = $data['countryCode'];
$newPhone = NULL;
if (preg_match("/^\+?[0-9]{7,17}$/", $data['newPhone'])) $newPhone = $data['newPhone'];
$pwd = NULL;
if (preg_match("/^.{6,25}$/", $data['pwd'])) $pwd = $data['pwd'];

// Validation
$success = true;
if ($userUid == NULL || $countryCode == NULL || $newPhone == NULL || $pwd == NULL) {
  echo json_encode(["success" => false, "error" => "Required fields are missing."]);
  exit();
}

// Validation
$success = true;
if ($userUid == NULL || $countryCode == NULL || $newPhone == NULL) $success = false;

// Connexion à LDAP sans utiliser le fichier de config admin
$ldap_host = 'ldap://localhost';
$ldap_conn = ldap_connect($ldap_host);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

// Check LDAP connection
if (!$ldap_conn) {
    echo json_encode(["success" => false, "error" => "Failed to connect to LDAP"]);
    exit();
}

// Vérification de la connexion LDAP
if (!ldap_bind($ldap_conn, $ldap_admin_dn, $ldap_admin_password)) {
    echo json_encode(["success" => false, "error" => "LDAP authentication failed"]);
    exit();
}

// Lier à LDAP avec les informations de l'utilisateur
$dn = "uid=$userUid,ou=People,dc=iut5-kourou,dc=fr";
if (!ldap_bind($ldap_conn, $dn, $pwd)) {
  echo json_encode(["success" => false, "error" => "Invalid password."]);
  ldap_unbind($ldap_conn);
  exit();
}

// Préparer la modification
$fullPhone = $countryCode . $newPhone;
$entry = ["mobile" => $fullPhone];

// Debugging log before modification
error_log("Attempting to modify phone for $userUid: " . json_encode($entry));

// Effectuer la modification dans LDAP
if (!ldap_modify($ldap_conn, $dn, $entry)) {
  echo json_encode(["success" => false, "error" => ldap_error($ldap_conn)]);
  ldap_unbind($ldap_conn);
  exit();
}

// Fermer la connexion LDAP
ldap_unbind($ldap_conn);

// Send a success response back to AJAX
echo json_encode(["success" => true, "fullPhone"=>$fullPhone, "modifiedEntry"=>$userUid]);
?>
