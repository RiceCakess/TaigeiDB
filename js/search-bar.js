function searchBar(){
	$(".search-bar > input").keyup(function(e){
	if(e.which == 40 || e.which == 38 || e.which == 13){
		return;
	}
	var str = $(this).val();
	var livesearch = $(this).next("#livesearch");
	if(str.length <= 0)
		livesearch.html("");
	else
		$.get("api/search",{query: str, baseform: true}).done(function(res){
			livesearch.html("");

			res.data.forEach(function(obj,i){
				var img = "";
				switch(obj.category){
					case "ship":
						img = assetPath + "ships/" + obj.asset + "/21.png";
						break;
					case "equip":
						img = assetPath +"slotitem/item_on/" + pad(obj.id,3) + ".png";
						break;
					case "category":
						if(obj.subtype == "equip") 
							img = assetPath +"icons/plain/" + obj.icon + ".png";
						break;
				}
				var listItem = $("<li/>")
								.text(obj.name)
								.addClass("list-group-item")
								.addClass(i==0 ? "active" :"")
								.addClass(obj.category)
								.css("background-image", "url(" + img + ")")
								.attr("data-link",obj.category + ".php?id=" + obj["id"]);
				livesearch.append(listItem);
				//console.log(listItem);
			
			});
			$("#livesearch > li").hover(function(){
				var current = livesearch.children(".active");
				$(current).removeClass("active");
				$(this).addClass("active");
			}).click(function(){
				window.location.href = $(this).attr("data-link");
			});
		});
	}).keydown(function (e){
		var current = $(livesearch).children(".active");
		if (e.which == 13 && current.attr("data-link")) {
			window.location.href = current.attr("data-link");
			return;
		}
		if(e.which == 40 && current.next().length > 0){
			$(current).next().addClass("active");
			$(current).removeClass("active");
			e.preventDefault();
		}
		if(e.which == 38 && current.prev().length > 0){
			$(current).prev().addClass("active");
			$(current).removeClass("active");
			e.preventDefault();
		}
	});
}
			