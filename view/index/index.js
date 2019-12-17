var loginFormOpen = false;
$(document).ready(function() {
  	$("#logo_container").hover(function() {
		$("#logo").hide();
		$("#animated_logo").show();

	},function() {
		$("#logo").show();
		$("#animated_logo").hide();

	});
	$(window).bind('hashchange', function() {
        var hash = window.location.hash.replace(/^#/,'');
    });
	Utility.Form.get("isLoggedIn", function(jLoggedInResult) {
		var oLoginResult = $.parseJSON(jLoggedInResult);
		if ( oLoginResult[0] == true ) {
			Utility.Form.get("view/members/worker/worker.html", function(data) {
				$('#content').append(data);
			});
		} else { window.location = "https://workfar.com" }
	});
	$(".hover_logo").hover(function() {
		$(this).css("color","#2ECC71");
	},function () {
		$(this).css("color","#000000");
	});
});