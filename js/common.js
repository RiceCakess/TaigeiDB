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

function createAlert(type,message){
	var alert = $("<div/>")
		.addClass("alert alert-" + type)
		.attr("role","alert")
		.text(message);
	return alert;
}
function addLoadingFairy(elm){
	var url = assetPath + "slotitem/item_character/" + pad(Math.floor(Math.random()*255),3) + ".png";
	var fairy = $("<div/>")
				.addClass("loadingFairy")
				.append('<img src="' + url + '"/>')
				.append("<div>Loading...</div>");
	$(elm).append(fairy);
	return fairy;
}
function createShipBanner(asset, name){
	return $("<div/>")
	.addClass("ship-banner")
	.append('<img src="https://rawgit.com/WolfgangKurz/KanColleAssets/master/ships/' + asset + '/1.png">')
	.append('<span class="ship-banner-name">' + name + "</span>");
}
function createEquipBanner(id, name){
	return $("<div/>")
	.addClass("equip-banner")
	.append('<img src="' + assetPath + 'slotitem/card/' + pad(id,3) + '.png">')
	.append('<span class="equip-name">' + name + "</span>");
}