$(document).ready(function() {
	$.getJSON("http://atwd.dev/crimes/6-2013/json", function(data) {
		$.each(data.response.crimes.region, function(key, value) {
			var itemId = value.id;
			itemId = itemId.replace(/\s/i, "_").toLowerCase();
			$('#region').append($("<option></option").attr("value", itemId).text(value.id));
		});
		// console.log(regions);
	});
});
