<html>
<head>
  <!-- CSS files -->
  <link rel='stylesheet' type='text/css' href='./css/web.css' media='screen' />
  <link rel='stylesheet' type='text/css' href='./css/addEntry.css' media='screen' />
  <link rel='stylesheet' type='text/css' href='./css/icon.css' media='screen' />
  <link rel='stylesheet' type='text/css' href='./css/01_mobile.css' media='screen' />
  <link rel='stylesheet' type='text/css' href='./css/02_fonts.css' media='screen' />
  <link rel='stylesheet' type='text/css' href='./css/03_icons.css' media='screen' />

  <!-- JS files -->
  <script type='text/javascript' src='./js/jquery-3.7.0.min.js'></script>
  <script type='text/javascript' src='./js/jquery-ui.min.js'></script>
  <script type='text/javascript' src='./js/web.js'></script>
  <script type='text/javascript' src='./js/ajxAddEntry.js'></script>


  <meta charset="utf-8">
  <title>Add entry</title>

  <!-- Icon -->
  <link rel='icon' type='image/png' href='./medias/ldapLogo.jpeg' />

</head>

<header>
  <nav>
    <ul>
      <li><a href="./adminPanelLdap.php">Go back to admin panel</a></li>
      <li><a href='./wordpress/wp-admin/'>Sign in on wordpress account</a></li>
      <li><a href="./logoutAdmin.php">Logout <i class='logoutIcon'>&#xe9ba;</i></a></li>
    </ul>
  </nav>
</header>

<?php
// Data from session
session_start();

$idUser = NULL;
if (isset($_SESSION['idUser'])) $idUser = $_SESSION['idUser'];

// Check
if ($idUser == NULL) {
  header("Location: logout.php");
  exit();
}

// Use preg_match to extract the part before the comma in the DN
preg_match('/cn=([^,]+)/', $idUser, $matches);

// The extracted username is stored in $matches[1]
$login = $matches[1];

// Check if the user is an admin (cn=admin)
if ($login !== 'admin') {
    echo "<h2>You are not authorized to access this page. Admin access required.</h2>";
    // Exit if the user is not an admin
    exit();
}
?>
<div class='container'>

  <form>
    <h2>Add entry</h2>

    <!-- Div pour afficher le résultat -->
    <div id="result"></div>

    <p>First name</p>
    <p><input type="text" name="firstName" placeholder='First name' required /></p>

    <p>Last name</p>
    <p><input type="text" name="lastName" placeholder='Last name' required /></p>

    <!-- Liste déroulante pour la sélection du pays -->
    <span>
      <select name='country' id='countrySelect' class='countrySelect' required>
        <option value="+1" dataCountryCode="CA">🇨🇦 +1</option>
        <option value="+7" dataCountryCode="RU">🇷🇺 +7</option>
        <option value="+27" dataCountryCode="ZA">🇿🇦 +27</option>
        <option value="+33" dataCountryCode="FR">🇫🇷 +33</option>
        <option value="+34" dataCountryCode="ES">🇪🇸 +34</option>
        <option value="+39" dataCountryCode="IT">🇮🇹 +39</option>
        <option value="+44" dataCountryCode="GB">🇬🇧 +44</option>
        <option value="+49" dataCountryCode="DE">🇩🇪 +49</option>
        <option value="+52" dataCountryCode="MX">🇲🇽 +52</option>
        <option value="+54" dataCountryCode="AR">🇦🇷 +54</option>
        <option value="+55" dataCountryCode="BR">🇧🇷 +55</option>
        <option value="+61" dataCountryCode="AU">🇦🇺 +61</option>
        <option value="+66" dataCountryCode="TH">🇹🇭 +66</option>
        <option value="+82" dataCountryCode="KR">🇰🇷 +82</option>
        <option value="+86" dataCountryCode="CN">🇨🇳 +86</option>
        <option value="+91" dataCountryCode="IN">🇮🇳 +91</option>
        <option value="+234" dataCountryCode="NG">🇳🇬 +234</option>
        <option value="+594" dataCountryCode="GF">🇬🇫 +594</option>
        <option value="+689" dataCountryCode="NC">🇳🇨 +689</option>
        <option value="+966" dataCountryCode="SA">🇸🇦 +966</option>
      </select>
    </span>

    <!-- Champ de saisie du numéro de téléphone -->
    <span><input type='tel' name='phone' id='phone' placeholder="Ex : 0694 123 456" required /></span>

    <!-- Liste déroulante pour la sélection de la formation -->
    <p>
      Formation :
      <select name='formation' class='formation' required>
        <option value="BUT1RT">BUT1RT</option>
        <option value="BUT2RT">BUT2RT</option>
        <option value="BUT3RT">BUT3RT</option>
        <option value="BUT1GEII">BUT1GEII</option>
        <option value="BUT2GEII">BUT2GEII</option>
        <option value="BUT1GCCD">BUT1GCCD</option>
        <option value="BUT2GCCD">BUT2GCCD</option>
        <option value="BUT3GCCD">BUT3GCCD</option>
        <option value="Administratifs">Administratifs</option>
        <option value="Profs">Profs</option>
      </select>
    </p>
    <p>Password (min 6 characters)</p>
    <p><input type='password' name='pwd' placeholder='Password' required /></p>

    <p><button type="button" class="add">Add user<i class='sendIcon'>&#xe163;</i></button></p>
  </form>

  <aside>
    <iframe id="adminIframe" src="adminPanelLdap.php" width='100%' height='600px'></iframe>
  </aside>
</div>

</html>
