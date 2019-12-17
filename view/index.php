<html>
	<head>
		<?php
		if (isset($_GET['request'])) {
				$aData = explode("/", $_GET['request']);
				$page = $aData[0];
			} else $page = "";
			$main = new Controller();
			switch($page) {
				case 'member':
					echo "<title>" . $aData[2] . "</title>" .
					'<meta name="description" content="' . $aData[2]. ' ' . $aData[3] . '" />';
					break;
				case 'members':
					echo "<title>WorkFar: Remote Workers</title>" .
					'<meta name="description" content="WorkFar list of Workers for hire" />';
					break;
				case 'privacy_policy':
					echo "<title>WorkFar: Privacy Policy</title>" .
					'<meta name="description" content="WorkFar Legal Privacy policy" />';
					break;
				case 'work':
					echo "<title>WorkFar: Remote Jobs</title>" .
					'<meta name="description" content="WorkFar List of Remote Jobs Today" />';
					break;
				default:
					?><title>WorkFar: Remote Work, Remote Jobs, Telecommuting Jobs</title>
					<meta name="description" content="WorkFar has remote jobs for you, telecommuting with no fees you will get paid more for your remote work." /><?php
					break;
			}
			?>
		<meta name="msvalidate.01" content="244E9CE85D4638311ACC835F7F651A5F" />
	    <link href="/view/ux/dist/css/flat-ui.css" rel="stylesheet">
	    <link href="/view/ux/docs/assets/css/demo.css" rel="stylesheet">
	    <link href="/view/home/index.css" rel="stylesheet" type="text/css"  />
	    
		<style>
			#header { background-color:#FFFFFF; width: 100%; height: 300px; }
			.profile_image { box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    text-align: center;}
			.et_pb_contact_submit { padding: 6px 20px !important; }
			#salesvideo { margin: 0 auto; margin-top: 150px !important; }
			#content {width 100%;}
			canvas {
				cursor: crosshair;
				display: block;
				background-color:#000000;
				width:30%;
				float:left;
			}
			#footer { margin-left:250px; }
			#register_form { margin-top:180px; }
			.worker { width: 250px !important; background-color:#4596D7 !important; cursor: context-menu !important; }
			td { margin:20px; }
			#login_button { border: 0; height: 45px;background-color:#2EA3F2;color:#fff !important;margin-top: 0px; }
			#header { z-index: 9999; top: 0px; width: 100%; background-color: #FFFFFF; position: fixed; height: 150px; }
			#logo { margin-left: 50px; }
			#email { height: 45px;width:270px !important; }
			#password { height: 45px;width:270px !important; }
			.widetextbox {margin: 5px;}
			
			@media only screen and (max-width: 600px) {
				#header {
					height:300px;
				}
				#login_form {float: left; margin-left:60px;}
				#register_form { margin-top:310px;}
			}
		</style>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-61004855-1"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'UA-61004855-1');
		</script>
		<script>
		!function() {var t;if (t = window.driftt = window.drift = window.driftt || [], !t.init) return t.invoked ? void (window.console && console.error && console.error("Drift snippet included twice.")) : (t.invoked = !0, t.methods = [ "identify", "config", "track", "reset", "debug", "show", "ping", "page", "hide", "off", "on" ], t.factory = function(e) {return function() {var n;return n = Array.prototype.slice.call(arguments), n.unshift(e), t.push(n), t;};}, t.methods.forEach(function(e) {t[e] = t.factory(e);}), t.load = function(t) {var e, n, o, i;e = 3e5, i = Math.ceil(new Date() / e) * e, o = document.createElement("script"), o.type = "text/javascript", o.async = !0, o.crossorigin = "anonymous", o.src = "https://js.driftt.com/include/" + i + "/" + t + ".js", n = document.getElementsByTagName("script")[0], n.parentNode.insertBefore(o, n);});}();drift.SNIPPET_VERSION = '0.3.1';drift.load('kca8uhwdfeub');
		</script>
</script>
	</head>
	<body>
		<div id='header' class='relative'>
			<a href='https://workfar.com'><img id='logo' src='/view/index/remote jobs telecommuting jobs remote work.gif' alt='Remote Work, Remote Jobs, Telecommunicating Jobs' style='' class='left'/></a>
			
			

			<form id="login_form" class="right">
				<div id="message"> </div>
				<input type="text" name="email" id="email" class="widetextbox left" placeholder="Email"><br>
				<input type="password" name="password" style='margin-left: -3px;'id="password" class="widetextbox left" placeholder="Password"><br>
				<a href="#forgot_password" class='left' id="forgot_password_btn">Change password</a> 
				<input type="submit" class="et_pb_contact_submit right" id="login_button" value="Login">
			</form>
		</div>
			

		<form id="register_form">
			<input type='hidden' name='work_applicant_id' value='' />
			<center>
				<h1> Work, Hire, and Pay on remote jobs with same email for <b><i></i></b>.</h1>
				<div id="error_message_join" style="display: none;"></div>
				<input type='hidden' value="" name='worker_id' id='worker_id' />
				<table>
					<tr><td><input class="widetextbox left" style="height: 45px; width: 350px !important;" name="email" type="text" placeholder="New Email"></td></tr>
					<tr><td><input  class="widetextbox left" style="height: 45px; width: 350px !important;" name="password" type="password" placeholder="New Password"></td></tr>
					<tr><td><input id="register_button" class="et_pb_contact_submit" style="width:350px !important; border: 0; height: 45px;background-color: #2ea3f2; color: #fff !important; margin-left: 5px;" type="submit" value="Create Account"></td></tr>
				</table>
			
		</form>
		<h3>Find work fast and gain experience and feedback. <br />By doing telecommuting jobs you do remote work
			from your home office anywhere in the world</h3>
		<div id='content' style='margin: 0 auto;'>
		<?php
			if (isset($_GET['request'])) {
				$aData = explode("/", $_GET['request']);
				$page = $aData[0];
			} else $page = "";
			$main = new Controller();
			switch($page) {
				case 'work':
					include_once "controller/work.php";
					$work = new Work();
					$work->list_work();
					break;
				case 'member':
					include_once "controller/users.php";
					$user = new User();
					$user->list_member($aData[1]);
					break;
				case 'members':
					include_once "controller/users.php";
					$user = new User();
					$user->list_members();
					break;
				case 'list_posts':
					include_once "controller/message_board.php";
					$message_board = new Message_Board();
					$message_board->list_posts();
					break;
				case 'privacy_policy':
?>
<iframe src='https://app.termly.io/document/privacy-policy/2b9a36f9-cbce-43ee-a084-38adda38aff4' width='100%' height='100%'></iframe>

<?php
					break;
			}
?>
<br><br>WorkFar.com is a marketplace that connects workers with employers. WorkFar.com makes hiring remote workers at an hourly rate easy, and soon will provide access to in-office work, and fixed-priced services. WorkFar.com is a one-stop shop for any work related needs, we charge only 3% transaction fees. This is cheaper than all of our competitors and we are doing it better with more features for the users, workers, and employers.
		<br><br><h2>Remote Jobs</h2>
		The site is easy to use, one of our beta testers is a 9 year old girl and she was able to create a job and hire a person. It’s so easy elementary school students can use it, not that we recommend child labor, of course not, but multilingual who may not know english very well can easily browse our site with elementary level words. We provide video invoices for employers; they are able to watch while the worker is working via video screen share, and every 10 minutes the credit card is billed for 1/6th of the worker’s hourly pay. Video invoicing is the future. <br><br>
		Employers can make sure the workers are not doing other tasks while they are billing time to the employer. Upwork tried to solve this issue by taking a picture every 10 minutes, a sad attempt to give clarity to the employers. We give you video, if you see the worker is not working properly then you can dispute the charge and get your money back. You can easily skim through video invoices, it’s not required that you watch all the videos as an employer. If your worker is giving you results, by all means give them autonomy and check less frequently. Workfar.com is a fair and balanced system that allows workers to be guaranteed payment by continuous billing every 10 minutes, and guaranteed work completion for the employer. Also our main competitor Upwork’s tool for taking simple pictures every ten minutes is downloadable. At WorkFar.com everything works in the browser and it requires no downloads.
We have them beat in every feature, and every price sector. WorkFar.com makes it easy to video conference in the website from across the globe, we even took it a step further and added a universal translator to the video conferencing. A Worker in India can hear his or her employer in English, and Hindi at the same time. This is not a feature that ensures understandability between cultures, but it will help the workers get a clearer picture of what the employers want if they see a particular word in their language in addition to the employers instructions in english.
<br><br>WorkFar.com’s payment system allows ACH direct deposits which take 3-5 business days. We pay faster than any marketplace on the market right now. Upwork takes 2 weeks to pay its workers, Freelancer takes a similar amount of time. As long as there are no disputes the worker can be sure to get their money faster than any other marketplace service. In their particular country the workers will be contractors and are responsible for paying their own taxes. 

<h2>Remote Work</h2>
WorkFar.com does not handle any of the employer's money, we simply direct deposit it through our Stripe payment gateway into the workers bank. You can request your money as long as your balance is over $0.29. WorkFar.com saw what the competition was doing 5 years ago, and decided to make something so outstanding that the world of work and how we view it and communicate with it will change. 
<br><br><h2>Telecommuting Jobs</h2>By having less transaction fees than upwork and freelancer.com, we are tapping into economic principles of supply and demand. By charging less we can handle more volume. WorkFar.com has 1 employee, Upwork has over 300 and a giant office. Upwork has to pay salaries for 300 people and a giant office bill, that’s why they charge 20%. They cannot afford to charge any less. WorkFar.com can. We can charge next to nothing because all of the work was completed by a genius programmer -- Teddy Murray -- over the course of 7 years. <div id='seo_pages' style='margin-top: 10px;'>
	     	<a href='/work' type="submit" class="et_pb_contact_submit" style="border: 0; height: 45px;background-color:#2EA3F2;color:#fff !important;margin-top: 0px;">List Work</a>
	     	<a href="/members" type="submit" class="et_pb_contact_submit" style="border: 0; height: 45px;background-color:#2EA3F2;color:#fff !important;margin-top: 0px;">View Members</a> 
	     	<!-- <a href='/list_posts' type="submit" class="et_pb_contact_submit" style="border: 0; height: 45px;background-color:#2EA3F2;color:#fff !important;margin-top: 0px;">View Message Board</a> -->
	    </div>
	    
	    <br><br><a href='/privacy_policy'>Privacy Policy</a><br>In God We Trust</center>
    	<link href="/view/ux/dist/css/vendor/bootstrap.min.css" rel="stylesheet">
		<script src="/view/jquery/jquery-1.10.2.js"></script>
		<script src="/view/jquery/jquery-ui-1.10.3.custom.js"></script>
		<link href="/view/jquery/css/redmond/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css" />
		<script src="/view/utility.js"></script>
		<script type='text/javascript'>
			$(document).ready(function() {
				if($("#interview_text").val() != undefined) {
					$("#register_button").val($("#interview_text").val());
					$("#worker_id").val($("#worker_id_value").val());
				}
				window.loginMode = true;
				$("#login_form").submit(function() {
					if (window.loginMode) {
						Utility.Form.post("/login", "#login_form", function(response) {
							var success = $.parseJSON(response);
							if (success[0] == true) window.location = "/dashboard";
							else { $("#message").html("Email or password incorrect");}
						});
					} else {
						Utility.Form.post("/forgot_password", "#login_form", function(response) {
							$("#message").html("See your email"); 
							$("#login_button").val("Login");
							$("#forgot_password_btn").text("Change Password");
							$("#password").show('fade');
						});
					}
					return false;
				});
				$("#register_form").submit(function() {
					$("#employer_or_worker").val("true");
					Utility.Form.post("/create_account", "#register_form", function(response) {
						var success = $.parseJSON(response);
						if (success[0] == true) window.location = "/dashboard";
						else $("#error_message_join").show().html("You already joined! Please login above or change your password.");
					});
					return false;
				});
				$("#salesvideo").click(function() {
					var vid = document.getElementById("salesvideo"); 
					if ($("salesvideo").attr("playing") == "true" ) {
						vid.pause(); 
						$("salesvideo").attr("playing","false");
					}
					else {
						vid.play(); 
						$("salesvideo").attr("playing","true");
					}
				});
				$("#forgot_password_btn").click(function() {
					if (window.loginMode) {
						$("#login_button").val("Change Password");
						$("#forgot_password_btn").text("Login");
						$("#password").hide('fade');
					} else {
						$("#login_button").val("Login");
						$("#forgot_password_btn").text("Change Password");
						$("#password").show('fade');
					}
					window.loginMode = !window.loginMode;
				});
			});
		</script>
	</body>
</html>