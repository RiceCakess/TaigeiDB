var assetPath = "assets/KanColleAssets/";
$(document).ready(function(){
	addCollapse();
});
function addCollapse(callback){
	$(".data-card").each((index, value)=>{
		var header = $(value).children(".card-header");
		var block = $(value).children(".card-block");
		if(header.attr("data-toggle") === "collapse") //if already has collapse
			return;
		//console.log($(this));
		header.append('<span class="collapse-btn"><i class="fa" aria-hidden="true"></i></span>');
		header.attr("data-toggle","collapse");
		header.attr("data-target","#" + "card-" + index);
		block.attr("id","card-" + index);
		block.addClass("show");
		/*block.collapse("show");
		header.click(function (){
			block.collapse("toggle");
		});*/
		if(index == $(".data-card").length -1 && callback)
			callback();
		
	});
}
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
	//.append('<img src="https://rawgit.com/WolfgangKurz/KanColleAssets/master/ships/' + asset + '/1.png">')
	.append('<img src="assets/KCAssets/ships/' + asset + '/1.jpg">')
	.append('<span class="ship-banner-name">' + name + "</span>");
}
function createEquipBanner(id, name){
	return $("<div/>")
	.addClass("equip-banner")
	.append('<img src="assets/KanColleAssets/slotitem/card/' + pad(id,3) + '.png">')
	.append('<span class="equip-name">' + name + "</span>");
}