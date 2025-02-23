$(document).ready(function(){
	//==============================================================================

	/*==============================================================================
	Send ajax to server
	==============================================================================*/



	// Lorsque le bouton "Modifier" est cliqué
	jQuery("body").on("click", ".editButton", function() {
		var phoneSpan = jQuery(this).closest("tr").find("#newPhone");

		// Récupérer le numéro de téléphone actuel
		var currentPhone = phoneSpan.text();

		// Créer la liste déroulante pour le pays et le champ input pour le numéro
		var countrySelect = `
		<select name="country" class="countrySelect" required>
		<option value="1" dataCountryCode="CA">🇨🇦 +1</option>
		<option value="7" dataCountryCode="RU">🇷🇺 +7</option>
		<option value="27" dataCountryCode="ZA">🇿🇦 +27</option>
		<option value="33" dataCountryCode="FR">🇫🇷 +33</option>
		<option value="34" dataCountryCode="ES">🇪🇸 +34</option>
		<option value="39" dataCountryCode="IT">🇮🇹 +39</option>
		<option value="44" dataCountryCode="GB">🇬🇧 +44</option>
		<option value="49" dataCountryCode="DE">🇩🇪 +49</option>
		<option value="52" dataCountryCode="MX">🇲🇽 +52</option>
		<option value="54" dataCountryCode="AR">🇦🇷 +54</option>
		<option value="55" dataCountryCode="BR">🇧🇷 +55</option>
		<option value="61" dataCountryCode="AU">🇦🇺 +61</option>
		<option value="66" dataCountryCode="TH">🇹🇭 +66</option>
		<option value="82" dataCountryCode="KR">🇰🇷 +82</option>
		<option value="86" dataCountryCode="CN">🇨🇳 +86</option>
		<option value="91" dataCountryCode="IN">🇮🇳 +91</option>
		<option value="234" dataCountryCode="NG">🇳🇬 +234</option>
		<option value="594" dataCountryCode="GF">🇬🇫 +594</option>
		<option value="689" dataCountryCode="NC">🇳🇨 +689</option>
		<option value="966" dataCountryCode="SA">🇸🇦 +966</option>
		</select>
		`;

		var phoneInput = `<input type="tel" class="editPhoneInput" placeholder="Numéro de téléphone" pattern="^[0-9]{7,15}$" required />`;

		// Remplacer le texte du numéro par le sélecteur de pays et l'input
		phoneSpan.html(countrySelect + phoneInput);

		// Remplacer le bouton "Modifier" par un bouton "Valider"
		jQuery(this).text("Valider").removeClass("editButton").addClass("saveButton");
	});

	jQuery("body").on("click", ".saveButton", function() {
		// Récupérer l'UID depuis l'attribut dataUid du bouton
		var userUid = jQuery(this).attr("dataUid");

		// Récupérer le code pays depuis la sélection
		var countryCode = jQuery(this).closest("td").find(".countrySelect").val();

		// Si le code pays commence par "+" (par exemple "+33"), remplacez-le par "00"
		if (countryCode.startsWith("+")) {
			countryCode = countryCode.replace("+", "00");
		}
		countryCode = "00" + countryCode;

		// Récupérer le numéro de téléphone et retirer les espaces et tirets
		var newPhone = jQuery(this).closest("td").find(".editPhoneInput").val().replace(/[\s\-]/g, "");

		if (newPhone.startsWith("+")) {
			alert("Choose a country number from the drop down list.");
			return;
		}

		if (!newPhone.trim()) {
			alert("Phone number cannot be empty.");
			return;
		}
		if (newPhone.replace(/\D/g, "").length < 7) {
        alert("Phone number must be at least 7 digits long.");
        return;
    }
		if (newPhone.replace(/\D/g, "").length > 17) {
				alert("Phone number can't be longer than 17 digits.");
				return;
		}
		if (/[a-zA-Z]/.test(newPhone)) {
			alert("Phone number must not contain letters.");
			return;
		}

		sendAjax("ajxModifyEntry.php", {userUid: userUid, countryCode: countryCode, newPhone: newPhone});
	});

	/*==============================================================================

	Receive ajax from server
	==============================================================================*/

	// Receive ajax data
	function receiveAjax(data) {
		if (data.success) {
			// Update the search input with the userUid from the response
			jQuery("#searchInput").val(data.modifiedEntry);
			jQuery("#searchForm").submit();
		} else {
			// Handle failure case (if necessary)
			alert("Failed to update phone number.");
		}
	}



















	/*==============================================================================
	Usefull functions
	==============================================================================*/

	// --- Send AJAX data to server
	function sendAjax(serverUrl, data) {
		jsonData = JSON.stringify(data);
		jQuery.ajax({type: 'POST', url: serverUrl, dataType: 'json', data: "data=" + jsonData,
		success: function(data) {
			receiveAjax(data);
		}
	});
}



// --- Test whether a variable is defined or not
function defined(myVar) {
	if (typeof myVar != 'undefined') return true;
	return false;
}

//==============================================================================
});
