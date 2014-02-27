$(document).ready(function() {
	var select = $('#region');
	$.getJSON("crimes/6-2013/json", function(data) {
		function appendRegionToList(value, elem) {
			id = value.replace(/\s/ig	, "_").toLowerCase();
			elem.append($("<option></option").attr("value", id).text(value));
		}

		$.each(data.response.crimes.region, function(key, value) {
			appendRegionToList(value.id, select);
		});
		$.each(data.response.crimes.national, function(key, value) {
			appendRegionToList(value.id, select);
		});
	});

	select.on('change', function() {
		$.getJSON("crimes/6-2013/" + $(this).val() + "/json", function(data) {

			var chartLabels = [],
				chartData = [];

			console.log(data.response.crimes.region);

			$.each(data.response.crimes.region.area, function(key, value) {
				chartLabels.push(value.id);
				chartData.push(value.total);
			});

			console.log(chartLabels);
			console.log(chartData);

			// on completion, replace the canvases in order to clear the data
			$('#bar').replaceWith('<canvas id="bar" width="600" height="400"></canvas>');
			$('#pie').replaceWith('<canvas id="pie" width="600" height="400"></canvas>');
			var barCanvasContext = $('#bar').get(0).getContext("2d");
			var barData = {
				labels : chartLabels,
				datasets : [
					{
						fillColor : "rgba(220,220,220,0.5)",
						strokeColor : "rgba(220,220,220,1)",
						data : chartData
					},
				]
			};
			new Chart(barCanvasContext).Bar(barData,{});

			var pieCanvasContext = $('#pie').get(0).getContext("2d");

			var pieData = [];
			$.each(chartData, function(key, value) {
				// http://www.paulirish.com/2009/random-hex-color-code-snippets/
				randomColor = '#'+Math.floor(Math.random()*16777215).toString(16);
				pieData.push({value: parseInt(value, 10), color: randomColor});
			});

			console.log(pieData);

			new Chart(pieCanvasContext).Pie(pieData,{});
		});
	});
});
