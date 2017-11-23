var assetPath = "assets/KanColleAssets/";
$(document).ready(function(){
	addCollapse();
	addSort();
});
function addCollapse(callback){
	$(".data-card").each((index, value)=>{
		var header = $(value).children(".card-header");
		var block = $(value).children(".card-block");
		if(header.attr("data-toggle") === "collapse") //if already has collapse
			return;
		header.append('<span class="collapse-btn"><i class="fa" aria-hidden="true"></i></span>');
		header.attr("data-toggle","collapse");
		header.attr("data-target","#" + "card-" + index);
		block.attr("id","card-" + index);
		block.addClass("show");
		if(index == $(".data-card").length -1 && callback)
			callback();
	});
}
function UrlExists(url)
{
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status!=404;
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
	.append('<img src="assets/KCAssets/ships/' + asset + '/1.jpg">')
	.append('<span class="ship-banner-name">' + name + "</span>");
}
function createEquipBanner(id, name){
	return $("<div/>")
	.addClass("equip-banner")
	.append('<img src="assets/KanColleAssets/slotitem/card/' + pad(id,3) + '.png">')
	.append('<span class="equip-name">' + name + "</span>");
}
var season = ["Spring","Summer", "Fall", "Winter"];
function locNumber(world, map){
	if(world > 6){
		var year = 2015 + Math.floor((world - 30)/4);
		var seaso = season[((world - 30) % 4)];
		return seaso + " " + year + " Event" + (map > 0 ? " E-" + map : "");
	}
	else
		return world +"-" + map;
}

jQuery.fn.sortElements = (function(){
    var sort = [].sort;
    return function(comparator, getSortable) {
        
        getSortable = getSortable || function(){return this;};
        var placements = this.map(function(){
            var sortElement = getSortable.call(this),
                parentNode = sortElement.parentNode,
                nextSibling = parentNode.insertBefore(
                    document.createTextNode(''),
                    sortElement.nextSibling
                );
            return function() {
                if (parentNode === this) {
                    throw new Error(
                        "You can't sort elements if any one is a descendant of another."
                    );
                }
                parentNode.insertBefore(this, nextSibling);
                parentNode.removeChild(nextSibling);  
            };
        });
        return sort.call(this, comparator).each(function(i){
            placements[i].call(getSortable.call(this));
        });
    };
})();

function addSort(){
	$(".table").each(function(){
		var table = $(this);
		table.children("thead").find("th").each(function(){
			var th = $(this);
			 var thIndex = th.index();
			 var inverse = false;
			 th.append('<i class="fa"></i>');
			 th.click(function(){
				table.find("i").each(function(){ $(this).removeClass("sort_desc"); $(this).removeClass("sort_asc");  });
				th.children("i").addClass(inverse ? "sort_asc" : "sort_desc");
				th.children("i").removeClass(inverse ? "sort_desc" : "sort_asc");
				table.find('td').filter(function(){
					return $(this).index() === thIndex;
				}).sortElements(function(a, b){
					var atext = $(a).text();
					var btext = $(b).text();
					if(/^\d+$/.test(atext) && /^\d+$/.test(btext)){
						atext = parseInt(atext);
						btext = parseInt(btext);
					}
					else if(atext.includes("%")){
						atext.replace("%","");
						btext.replace("%","");
						atext = parseFloat(atext);
						btext = parseFloat(btext);
					}
					return atext > btext ?
						inverse ? -1 : 1
						: inverse ? 1 : -1;
				}, function(){
					return this.parentNode; 
				});
				inverse = !inverse;
			});
		});
	});
}