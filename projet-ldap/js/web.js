$(document).ready(function(){
	//==============================================================================





	/*==============================================================================
	jQuery events
	==============================================================================*/

	// Change wrapper and <li> color on wrapper dblclick
	jQuery("body").on("click", ".deleteEntry", function() {
		// Empêcher le lien de s'exécuter immédiatement
		event.preventDefault();

		// Afficher le pop-up de confirmation
		if (confirm("Are you sure you want to delete this entry?")) {
			// URL de suppression à partir du lien
			var deleteUrl = jQuery(this).attr('href');
			// Rediriger vers l'URL pour la suppression
			window.location.href = deleteUrl;
		} else {
			// Annuler la suppression (aucune action)
			return false;
		}
	});

	jQuery("body").on("keyup", "#searchForm", function(key) {
		// Check if the key pressed is Enter (key code 13)
		if (key.which == 13) {
			// Get the search query value
			var searchQuery = jQuery("#searchInput").val().trim();

			// Check if the search query is not empty
			if (searchQuery) {
				// Reload the page with the search query as a URL parameter
				window.location.href = window.location.pathname + '?search=' + encodeURIComponent(searchQuery);
			}
		}
	});

	// Toggle the visibility of the search bar and table when clicking on the "people" heading
	// jQuery("body").on("click", "h2.people", function() {
  //   // Toggle visibility with slide, fade, and rotate effect
  //   jQuery(this).next().stop(true, true).fadeToggle(300);
	//
  //   // Rotate the icon (toggle between 0° and 180°)
  //   jQuery(this).find("i").toggleClass("rotate");
	// });
	//
	// // // Toggle the visibility of the group table when clicking on the "group" heading
	// jQuery("body").on("click", "h2.groups", function() {
  //   // Toggle visibility of the section using slideToggle
  //   jQuery(this).next().fadeToggle(300);
	//
	// 	// Rotate the icon (toggle between 0° and 180°)
	// 	jQuery(this).find("i").toggleClass("rotate");
	// });


	//==============================================================================
});
