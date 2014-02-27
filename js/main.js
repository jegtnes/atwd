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

			// Two separate arrays are needed for bar data: chartData and chartValues
			// this is so we are able to apply colours and find the
			// highest value in array for custom scale

			var chartLabels = [],
				chartData = [],
				chartValues = [],
				pieData = [];

			$.each(data.response.crimes.region.area, function(key, value) {
				chartLabels.push(value.id);
				randomColor = generateRandomRgbColor();
				labelColor = isTooBright(randomColor) === true ? 'black' : 'white';
				chartValues.push(parseInt(value.total, 10));
				chartData.push({
					value: parseInt(value.total, 10),
					fillColor: randomColor,
					strokeColor: "rgba(0,0,0,0)"
				});
				pieData.push({
					value: parseInt(value.total, 10),
					color: randomColor,
					label: value.id,
					labelColor: labelColor
				});
			});

			// on completion, replace the canvases in order to clear the data
			$('#bar').replaceWith('<canvas id="bar" width="900" height="450"></canvas>');
			$('#pie').replaceWith('<canvas id="pie" width="900" height="450"></canvas>');
			var barCanvasContext = $('#bar').get(0).getContext("2d");
			var barData = {
				labels : chartLabels,
				datasets : [
					{
						fillColor : "rgba(220,220,220,0.5)",
						strokeColor : "rgba(220,220,220,1)",
						data: chartData
					},
				]
			};

			var chartMax = highestArrayValue(chartValues);
			new Chart(barCanvasContext).Bar(barData,{
				scaleOverride: true,
				scaleSteps: 20,
				scaleStepWidth: Math.round(chartMax / 20) + 100,
				scaleStartValue: 0
			});

			var pieCanvasContext = $('#pie').get(0).getContext("2d");
			new Chart(pieCanvasContext).Pie(pieData,{});
		});
	});
});

// Returns a random RGB color represented as a string
function generateRandomRgbColor() {
	randomRed = Math.round(Math.random() * 255);
	randomBlue = Math.round(Math.random() * 255);
	randomGreen = Math.round(Math.random() * 255);
	return "rgb(" + randomRed + ", " + randomBlue + ", " + randomGreen + ")";
}

//http://stackoverflow.com/a/18003907/1430657
function highestArrayValue(arr) {
	var max = arr.reduce(function(previous,current){
		return previous > current ? previous:current;
	});
	return max;
}

// A function that returns true if the color is too bright to have a white label
// If a black label is preferable, returns false
// All credits go to:
// http://javascriptrules.com/2009/08/05/css-color-brightness-contrast-using-javascript/
function isTooBright(color) {
	var re = /rgb\((\d+), (\d+), (\d+)\)/;
	rgb = re.exec(color);
	var r = parseInt(rgb[1], 10),
		g = parseInt(rgb[2], 10),
		b = parseInt(rgb[3], 10);

	var brightness = (r*299 + g*587 + b*114) / 1000;
	if (brightness > 125) {
		return true;
	}
	else {
		return false;
	}
}
