document.addEventListener("DOMContentLoaded", function () {
	
	// on form submit
	$("#reviewForm").on('submit', function(event) {
	
		// validate form
		const inputs = document.querySelectorAll("input[required]");
		for (let i = 0; i < inputs.length; i++) {
			if (!inputs[i].value) {
				alert("Please fill in rating field.");
				return; // Prevent form submission
			}
		}

		// get url parameters (user_id and form info)
		const urlParams = new URLSearchParams(window.location.search);
		const params = {};
		for (const [key, value] of urlParams) {
			params[key] = value;
		}

		let fetchUrl = "post_reviews.php";
		if (Object.keys(params).length > 0) {
			fetchUrl += "?" + new URLSearchParams(params).toString();
		}

		// make request to php file
		$.ajax({
			url: fetchUrl,
			type: "POST",
			success: function (data) {
				console.log(JSON.parse(data));
				$("body").append("<p> Successfully posted review.</p>");
			},
			error: function (xhr, status, error) {
				console.error("Error making post:", error);
				$("body").append("<p> Error posting review.</p>");
			}
		});
	});
});
