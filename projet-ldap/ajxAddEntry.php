<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Data from session
session_start();
$idUser = NULL;
if (isset($_SESSION['idUser'])) $idUser = $_SESSION['idUser'];

// Check
if ($idUser == NULL) {
  header("Location: logout.php");
  exit();
}

// Data received from the client (filtered)
if (isset($_POST['data'])) $data = json_decode($_POST['data'], true);
$firstName = NULL;
if (preg_match("/^.{3,50}$/", $data['firstName'])) $firstName = $data['firstName'];
$lastName = NULL;
if (preg_match("/^.{3,50}$/", $data['lastName'])) $lastName = $data['lastName'];
$fullPhoneNumber = NULL;
if (preg_match("/^.{12,22}$/", $data['fullPhoneNumber'])) $fullPhoneNumber = $data['fullPhoneNumber'];
$formation = NULL;
if (preg_match("/^.{5,14}$/", $data['formation'])) $formation = $data['formation'];
$pwd = NULL;
if (preg_match("/^.{6,35}$/", $data['pwd'])) $pwd = $data['pwd'];

// Check if all fields are filled
if (!$firstName || !$lastName || !$fullPhoneNumber || !$formation || !$pwd) {
  echo json_encode(["html" => "All fields must be filled correctly.", "success" => false]);
  exit();
}

// Function to clean up special characters (accents, spaces -> underscores)
function cleanString($string, $isLastName = false, $capitalizeFirstLetter = false) {
  // Remove accents
  $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

  // If it's the last name, convert it to uppercase
  if ($isLastName) {
    $string = mb_strtoupper($string, 'UTF-8');
  } else {
    // First name and given name remain as they are (lowercase or original casing)
    $string = mb_strtolower($string, 'UTF-8');
  }

  // Replace spaces with underscores
  $string = strtr($string, [
    ' ' => '_',  // Replace spaces with underscores
  ]);

  // Capitalize the first letter for the first name (if requested)
  if ($capitalizeFirstLetter) {
    $string = ucfirst($string);  // Capitalize the first letter
  }

  return $string;
}

// Clean the firstName, lastName, and formation
$firstNameClean = cleanString($firstName, false, false);  // Capitalize the first letter of the first name
$lastNameClean = cleanString($lastName, true);  // Convert last name to uppercase

// Create a unique UID for the user (in lowercase)
$uid = strtolower($firstNameClean . '.' . $lastNameClean);
$dn_people = "uid=$uid,ou=People,dc=iut5-kourou,dc=fr";

// Check email format based on formation
$email = "";
if (in_array($formation, ["BUT1RT", "BUT2RT", "BUT3RT", "BUT1GEII", "BUT2GEII", "BUT3GEII", "BUT1GCCD", "BUT2GCCD", "BUT3GCCD"])) {
  $email = strtolower($firstNameClean . '.' . $lastNameClean . "@etu.iut5-kourou.fr"); // For students
} elseif ($formation == "Administratifs" || $formation == "Profs") {
  $email = strtolower($firstNameClean . '.' . $lastNameClean . "@iut5-kourou.fr"); // For administrative or professors
} else {
  echo json_encode(["success"=>false, "html"=>"Invalid information."]);
}

// Function to hash the password
function hashPassword($password) {
  // Générer un sel unique
  $salt = uniqid(rand(), true);

  // Créer le hash SHA-1 du mot de passe avec le sel
  $sha1Hash = sha1($password . $salt, true);

  // Concaténer le hash et le sel
  $saltedHash = $sha1Hash . $salt;

  // Encoder en base64 pour être stocké dans LDAP
  return "{SSHA}" . base64_encode($saltedHash);
}

// Hash the password before adding it to LDAP
$hashedPwd = hashPassword($pwd);  // Hash the password

// Prepare the LDAP entry for the user
$entry_people = [
"objectClass" => ["top", "person", "inetOrgPerson"],
"uid" => $uid,
"sn" => "$lastNameClean",
"gn" => $firstNameClean,
"cn" => $firstNameClean . ' ' . $lastNameClean,  // Full name as firstName lastName
"mobile" => $fullPhoneNumber,
"mail" => $email,
"userPassword" => $hashedPwd
];

// Connect to LDAP
$config = require('ldapConfig.php');
$ldap_host = $config['ldap_host'];
$ldap_admin_dn = $config['ldap_admin_dn'];
$ldap_admin_password = $config['ldap_admin_password'];

// Connect to the LDAP server
$ldap_conn = ldap_connect($ldap_host);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);  // Disable LDAP referrals
if (!$ldap_conn) {
  echo json_encode(["success"=>false, "html"=>"Failed to connect to server"]);
}

// Bind to the LDAP server using admin credentials
if (!ldap_bind($ldap_conn, $ldap_admin_dn, $ldap_admin_password)) {
  echo json_encode(["success"=>false, "html"=>"Authentication to server failed."]);
}

// Add the user to People
if (!ldap_add($ldap_conn, $dn_people, $entry_people)) {
  echo json_encode(["html"=>"Error adding user to People. " . ldap_error($ldap_conn)]);
  ldap_unbind($ldap_conn);
  exit();
}

// Determine the groups the user should be added to
$groups = [];
if (in_array($formation, ["BUT1RT", "BUT2RT", "BUT3RT", "BUT1GEII", "BUT2GEII", "BUT3GEII", "BUT1GCCD", "BUT2GCCD", "BUT3GCCD"])) {
  // Add user to their specific formation group and the general Etudiants group
  $groups[] = "cn=$formation,ou=Groups,dc=iut5-kourou,dc=fr";  // Specific formation group
  $groups[] = "cn=Etudiants,ou=Groups,dc=iut5-kourou,dc=fr";    // General student group
}
elseif ($formation == "Administratifs" || $formation == "Profs") {
  $groups[] = "cn=$formation,ou=Groups,dc=iut5-kourou,dc=fr";
}
else {
  echo json_encode(["html"=>"Invalid formation.", "success"=>false]);
  exit();
}

// Add the user to the groups
foreach ($groups as $group_dn) {
  $entry_group = [
  "memberUid" => $uid
  ];

  if (!ldap_mod_add($ldap_conn, $group_dn, $entry_group)) {
    echo json_encode(["html"=>"Couldn't add entry to the database.", "success"=>false]);
    ldap_unbind($ldap_conn);
    exit();
  }
}

// Close the LDAP connection
ldap_unbind($ldap_conn);

// Send the response back to the client
$data = array("html"=>"Entry successfully added to the database.", "success"=>true, "addedEntry"=>$uid);
echo json_encode($data);
exit();
?>
