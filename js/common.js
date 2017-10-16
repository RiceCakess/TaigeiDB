var assetPath = "assets/KanColleAssets/";

function round(value, precision) {
	var multiplier = Math.pow(10, precision || 0);
	return Math.round(value * multiplier) / multiplier;
}

function pad(num, size) {
	var s = num+"";
	while (s.length < size) s = "0" + s;
	return s;
}