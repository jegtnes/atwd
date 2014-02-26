$(document).ready(function() {
	var select = $('#region');
	$.getJSON("crimes/6-2013/json", function(data) {
		function appendRegionToList(value, elem) {
			id = value.replace(/\s/i, "_").toLowerCase();
			elem.append($("<option></option").attr("value", id).text(value));
		}

		$.each(data.response.crimes.region, function(key, value) {
			appendRegionToList(value.id, select);
		});
		$.each(data.response.crimes.national, function(key, value) {
			appendRegionToList(value.id, select);
		});
	});
});
