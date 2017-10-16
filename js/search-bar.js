function searchBar(){
	$(".main-search > input").keyup(function(e){
	if(e.which == 40 || e.which == 38 || e.which == 13){
		return;
	}
	var str = $("#livesearch").prev("input").val();
	if(str.length <= 0)
		$("#livesearch").html("");
	else
		$.get("api/search",{query: str}).done(function(res){
			$("#livesearch").html("");
			
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
				$("#livesearch").append(listItem);
			
			});
			$("#livesearch > li").hover(function(){
				var current = $("#livesearch").children(".active");
				$(current).removeClass("active");
				$(this).addClass("active");
			}).click(function(){
				window.location.href = $(this).attr("data-link");
			});
		});
	}).keydown(function (e){
		if (e.which == 13) {
			var current = $("#livesearch").children(".active");
			window.location.href = current.attr("data-link");
		}
		if(e.which == 40){
			var current = $("#livesearch").children(".active");
			if(current.next().length > 0){
				$(current).next().addClass("active");
				$(current).removeClass("active");
			}
			e.preventDefault();
		}
		if(e.which == 38){
			var current = $("#livesearch").children(".active");
			if(current.prev().length > 0){
				$(current).prev().addClass("active");
				$(current).removeClass("active");
			}
			e.preventDefault();
		}
	});
}
			