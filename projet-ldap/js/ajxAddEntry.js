$(document).ready(function(){
//==============================================================================

/*==============================================================================
	Send ajax to server
==============================================================================*/





	// Send ajax data on keyup : button submit
	jQuery("body").on("click", "button.add", function() {
		var firstName = $("input[name='firstName']").val();
    var lastName = $("input[name='lastName']").val();
    var countryCode = $("select[name='country']").val().replace(/^(\+)/, "00");
		var phoneNumber = $("input[name='phone']").val().replace(/[\s\-]/g, "");
		var pwd = $("input[name='pwd']").val();
    var formation = $("select[name='formation']").val()
    var fullPhoneNumber = countryCode + phoneNumber;

		if (countryCode.startsWith("+")) {
			countryCode = countryCode.replace("+", "00");
		}
		countryCode = "00" + countryCode;

		if (phoneNumber.startsWith("+")) {
			alert("Choose a country number from the drop down list.");
			return;
		}
		if (!phoneNumber.trim()) {
			alert("Phone number cannot be empty.");
			return;
		}
    if (phoneNumber.replace(/\D/g, "").length < 8) {
        alert("Phone number must be at least 8 digits long.");
        return;
    }
    if (phoneNumber.replace(/\D/g, "").length > 17) {
        alert("Phone number can't be longer than 17 digits.");
        return;
    }
		if (/[a-zA-Z]/.test(phoneNumber)) {
			alert("Phone number must not contain letters.");
			return;
		}

		sendAjax("ajxAddEntry.php", {firstName: firstName, lastName: lastName, fullPhoneNumber: fullPhoneNumber, formation: formation, pwd: pwd});
	});





/*==============================================================================
	Receive ajax from server
==============================================================================*/

// Receive ajax data
function receiveAjax(data) {
	// Ici, vous pouvez afficher la réponse du serveur, par exemple
	if (data.success) {
		// Afficher le message de succès dans une div ou alerter
		jQuery("#result").html("<p class='successAddEntry'>" + data.html + "</p>");

		// Mise à jour du searchForm dans l'iframe après l'ajout de l'entrée
		var iframe = jQuery('#adminIframe')[0];
		var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

		var searchInput = jQuery(iframeDoc).find('#searchInput');
		if (searchInput.length > 0) {
			// Insérer l'uid dans le champ de recherche
			searchInput.val(data.addedEntry);

			// Soumettre automatiquement le formulaire de recherche
			var searchForm = jQuery(iframeDoc).find('#searchForm');
			if (searchForm.length > 0) {
				// Soumettre le formulaire pour chercher l'utilisateur
				searchForm.submit();
			}
		}
	} else {
		// Afficher un message d'erreur en cas d'échec
		jQuery("#result").html(data.html);
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
