function Utility() { }
Utility.Form = function () { }
Utility.Rating = function () { }
Utility.Navigation = function() { }
Utility.Navigation.methods = Array();
Utility.Navigation.setPath = function ( path ) {
	location.hash = path;
	document.body.scrollTop = 0;
	var x = location.hash;
	document.title = "WorkFar.com: " + x;
}
Utility.Navigation.loadPath = function( path,method ) {
	Utility.Navigation.methods[path] = method;
}
/* Use: Utility.Form.post("/upload_image","#image_form",function(response) {}); */
Utility.Form.post = function ( sUrl, sForm, rootCallback ) {
	if(sForm == "no_form") {
		$.post(sUrl, rootCallback);
		return;
	}
	var failedRegex = false;
	var formObject = this;

	$(sForm + " :input").each(function() {
	 	var input = new RegExp($(this).attr("regex")).test($(this).val()); 
	 	if (input == false) {
	 		$(this).css("border-color","#FFA500");
 			failedRegex = true;
 			return;
	 	}
	});
	
	if (failedRegex == false) {
		Utility.Form.get("/token",function(jToken) {
			$("#loading").show();
			var oToken = $.parseJSON(jToken);
			$(sForm).prepend("<input type='hidden' name='token' id='token' value='" + oToken[0] + "' />");
			
			$.post(sUrl, $(sForm).serialize(), function(jPostFormResults) {
				$("#token").remove();
				$("#loading").hide();
				rootCallback(jPostFormResults);
			});
		});
	} else { return; }
}
Utility.Form.get = function ( sUrl,  fCallback ) {
	$("#loading").show();
	$.get(sUrl, function(json) {
		fCallback(json);
	$("#loading").hide();
	});
	
}
Utility.Form.errorMessage = function ( sForm, sTitle, sMessage ) {
	$('#error').attr('title', sTitle);
	$('#error').html(sMessage);
	$('#error').dialog({ height: 140, modal: true });
	
}

Utility.Rating.getStars = function (amount) {
	var stars = "";
	for ( var i = 0; i < amount; i++ ) {
		stars = stars + "<span class='fui-star-2'></span>";
	}
	return stars;
}
Utility.Rating.getMiniStar = function (amount) {
	var stars = "<span class='star relative'><span class='fui-star-2'>" + amount + "</span></span> ";
	
	return stars;
}
