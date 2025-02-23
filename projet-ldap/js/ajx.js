$(document).ready(function(){
//==============================================================================

/*==============================================================================
	Send ajax to server
==============================================================================*/

	// Send ajax on page load
	sendAjax("ajxPage.php", {});



	// Send ajax data every 60s
	window.setInterval(sendAjax, 60000, "ajxPage.php", {});



	// Send ajax data on keyup : input login
	jQuery("body").on("keyup", "input[name='login']", function(key) {
		var login = jQuery(this).val();
		if (key.which == 13) alert("Vous avez appuyé sur ENTREE : login=" + login);
		sendAjax("ajxPage.php", {login: login});
	});





/*==============================================================================
	Receive ajax from server
==============================================================================*/

	// Receive ajax data
	function receiveAjax(data) {
		// TODO
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
