<!doctype html>
<html>
<head>
	<title>Datavis.</title>
	<link rel="stylesheet" href="assets/css/app.min.css">
	<script src="assets/js/app-head.min.js"></script>
</head>
<body>
	<div class="contain-to-grid">
		<nav class="top-bar" data-topbar>
			<ul class="title-area">
				<li class="name">
					<h1><a href="#">Advanced Topics in Web Development</a></h1>
				</li>
			</ul>
			<section class=	"top-bar-section">
				<ul class="right">
				</ul>
			</section>
		</nav>
	</div>
	<div class="row">
		<div class="columns">
			<h1>Data Visualisation</h1>
			<form action="">
				<label for="region">Select region</label>
				<select name="region" id="region">
					<option value=""></option>
				</select>
			</form>

			<canvas id="bar" width="600" height="400"></canvas>
			<canvas id="pie" width="600" height="400"></canvas>
		</div>
	</div>
	<script src="assets/js/app.js"></script>
</body>
</html>
