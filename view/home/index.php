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
					'<meta name="description" content="WorkFar Legal Privacy Policy" />';
					break;
				case 'work':
					echo "<title>WorkFar: Remote Jobs</title>" .
					'<meta name="description" content="WorkFar List of Remote Jobs Today" />';
					break;
				default:
					?>
    <title>WorkFar: Remote Work, Remote Jobs, Telecommuting Jobs</title>
    <meta name="viewport" content="width=1200">

    <meta name="description"
        content="WorkFar has remote jobs for you, telecommuting with no fees you will get paid more for your remote work." />
    <?php
					break;
			}
			?>
    <meta name="msvalidate.01" content="244E9CE85D4638311ACC835F7F651A5F" />
    <script src="https://workfar.com/view/jquery/jquery-1.10.2.js"></script>
<script src="https://workfar.com/view/utility.js" type="text/javascript"></script>
    <link href="/view/home/bootstrap.css" rel="stylesheet" id="bootstrap-css">
    <script src="/view/home/bootstrap.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.4.1/js/mdb.min.js">
    </script>

    <style>
    @import url('https://fonts.googleapis.com/css?family=Montserrat:400,700');
    @import url('https://fonts.googleapis.com/css?family=Lato:300,400,700');
    @import url("//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css");
    /* SERVICE SECTION-------------------------------------------------- */
    body {   
        max-width: 1200px;
        min-width: 1200px;
        margin: 0 auto;
            background-color: #ffffff;
    font-family: 'Lato', sans-serif;

    }
    
    #login_div { border: 1px solid #ffffff;}
    .service-sec .heading {
        float: left;
        width: 100%;
        margin-bottom: 70px;
        text-align: center;
    }

    .service-sec h2,
    h5 {
        display: block;
        text-transform: capitalize;
        font-weight: 600;
        color: #4595CD;
        font-size: 24px;
    }

    .service-sec h2,
    h5 small {
        color: #222;
        display: block;
        font-size: 22px;
        margin-bottom: 18px;
    }

    .service-sec i {
        border: 1px solid #0297FF;
        border-radius: 2px;
        font-size: 25px;
        padding: 12px 0;
        width: 52px;
        color: #0297FF;
        margin-bottom: 20px
    }

    .service-sec h3 {
        font-size: 23px;
        font-weight: 600;
    }

    .service-sec p {
        line-height: 22px;
        margin-top: 13px;
        padding: 0 21px;
    }

    .service-sec .service-block {
        margin-top: 30px;
    }


    /* ABOUT SECTION-------------------------------------------------- */
    .about-sec {
        background: url('') no-repeat center center;
        background-size: cover;
        color: #fff;
        position: relative;
        height:1px;
    }

    .about-sec:before {
        content: ' ';
        position: absolute;
        width: 100%;
        height: 400px;
        background: rgba(22, 122, 192, 0.8);
        top: 0;
        left: 0;
    }

    .about-sec h2 {
        font-size: 55px;
        font-weight: 800;
        margin-top: 25%;
    }

    .about-sec h2 small {
        display: block;
        font-size: 24px;
        margin-bottom: 15px;
        padding-left: 10px;
    }

    .about-sec p {
        font-size: 16px;
    }

    /* -----------------  Card  --------------------- */
    .card-inverse .card-img-overlay {
        background-color: rgba(#333, .85);
        border-color: rgba(#333, .85);
    }


    /*-------------------footer ----------------------*/


    section {
        padding: 60px 0;
            min-height: 66%;
    }

    a,
    a:hover,
    a:focus,
    a:active {
        text-decoration: none;
        outline: none;
    }

    ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .main-footer {
        position: relative;
        background: #1e2129;
        margin-top:-250px;
    }

    .footer-content {
        position: relative;
        padding: 85px 0px 80px 0px;
    }

    .footer-content:before {
        position: absolute;
        content: '';
        background: url();
        width: 744px;
        height: 365px;
        top: 50px;
        right: 0px;
        background-size: cover;
        background-repeat: no-repeat;
        animation-name: float-bob;
        animation-duration: 30s;
        animation-iteration-count: infinite;
        animation-timing-function: linear;
        -webkit-animation-name: float-bob;
        -webkit-animation-duration: 30s;
        -webkit-animation-iteration-count: infinite;
        -webkit-animation-timing-function: linear;
        -moz-animation-name: float-bob;
        -moz-animation-duration: 30s;
        -moz-animation-iteration-count: infinite;
        -moz-animation-timing-function: linear;
        -ms-animation-name: float-bob;
        -ms-animation-duration: 30s;
        -ms-animation-iteration-count: infinite;
        -ms-animation-timing-function: linear;
        -o-animation-name: float-bob;
        -o-animation-duration: 30s;
        -o-animation-iteration-count: infinite;
        -o-animation-timing-function: linear;
    }

    .footer-content .logo-widget {
        position: relative;
        margin-top: -5px;
    }

    .footer-content .logo-widget .footer-social li {
        position: relative;
        display: inline-block;
        margin-right: 9px;
    }

    .footer-content .logo-widget .footer-social li:last-child {
        margin-right: 0px;
    }

    .footer-content .logo-widget .footer-social li a {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 42px;
        line-height: 42px;
        background: #2e3138;
        color: #9ea0a9;
        text-align: center;
        border-radius: 50%;
    }

    .footer-content .logo-widget .footer-social li a:hover {
        color: #ffffff;
        background: #ff5e14;
    }

    .footer-content .logo-widget .logo-box {
        margin-bottom: 25px;
    }

    .footer-content .logo-widget .text p {
        color: #9ea0a9;
        margin-bottom: 32px;
    }

    .footer-content .footer-title {
        position: relative;
        font-size: 24px;
        line-height: 35px;
        font-family: 'Playfair Display', serif;
        color: #ffffff;
        font-weight: 700;
        margin-bottom: 27px;
    }

    .footer-content .service-widget .list li {
        display: block;
        margin-bottom: 12px;
    }

    .footer-content .service-widget .list li a {
        position: relative;
        display: inline-block;
        color: #9ea0a9;
    }

    .footer-content .service-widget .list li a:hover {
        color: #ff5e14;
    }

    .footer-content .contact-widget p {
        color: #9ea0a9;
        margin-bottom: 15px;
    }

    .footer-content .contact-widget {
        margin-left: 90px;
    }

    .footer-content .contact-widget .footer-title {
        margin-bottom: 29px;
    }

    /** footer-bottom **/

    .footer-bottom {
        position: relative;
        background: #13151a;
        padding: 25px 0px 22px 0px;
    }

    .footer-bottom .copyright,
    .footer-bottom .copyright a,
    .footer-bottom .footer-nav li a {
        position: relative;
        color: #9ea0a9;
    }

    .footer-bottom .copyright a:hover,
    .footer-bottom .footer-nav li a:hover {
        color: #ff5e14;
    }

    .footer-bottom .footer-nav {
        position: relative;
        text-align: right;
    }

    .footer-bottom .footer-nav li {
        position: relative;
        display: inline-block;
        margin-left: 29px;
    }

    .footer-bottom .footer-nav li:first-child {
        margin-left: 0px;
    }

    .footer-bottom .footer-nav li:before {
        position: absolute;
        content: '';
        background: #9ea0a9;
        width: 1px;
        height: 14px;
        top: 7px;
        left: -18px;
    }

    .footer-bottom .footer-nav li:first-child:before {
        display: none;
    }

    .logo-box img {
        max-width: 220px;
    }



    /* ------------------  Carousel ----------------------------- */

    #myCarousel .carousel-item .mask {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background-attachment: fixed;
    }

    #myCarousel h4 {
        font-size: 50px;
        margin-bottom: 15px;
        color: #FFF;
        line-height: 100%;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    #myCarousel p {
        font-size: 18px;
        margin-bottom: 15px;
        color: #d5d5d5;
    }

    #myCarousel .carousel-item a {
        background: #F47735;
        font-size: 14px;
        color: #FFF;
        padding: 13px 32px;
        display: inline-block;
    }

    #myCarousel .carousel-item a:hover {
        background: #394fa2;
        text-decoration: none;
    }

    #myCarousel .carousel-item h4 {
        -webkit-animation-name: fadeInLeft;
        animation-name: fadeInLeft;
    }

    #myCarousel .carousel-item p {
        -webkit-animation-name: slideInRight;
        animation-name: slideInRight;
    }

    #myCarousel .carousel-item a {
        -webkit-animation-name: fadeInUp;
        animation-name: fadeInUp;
    }

    #myCarousel .carousel-item .mask img {
        -webkit-animation-name: slideInRight;
        animation-name: slideInRight;
        display: block;
        height: auto;
        max-width: 100%;
    }
h4 { color:#000000 !important;}
    #myCarousel h4,
    #myCarousel p,
    #myCarousel a,
    #myCarousel .carousel-item .mask img {
        -webkit-animation-duration: 1s;
        animation-duration: 1.2s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
    }

    #myCarousel .container {
        max-width: 1430px;
    }

    #myCarousel .carousel-item {
        min-height: 550px;
    }

    #myCarousel {
        position: relative;
        z-index: 1;
        background: url() center center no-repeat;
        background-size: cover;
    }

    @-webkit-keyframes fadeInLeft {
        from {
            opacity: 0;
            -webkit-transform: translate3d(-100%, 0, 0);
            transform: translate3d(-100%, 0, 0);
        }

        to {
            opacity: 1;
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }
    }



    @-webkit-keyframes fadeInUp {
        from {
            opacity: 0;
            -webkit-transform: translate3d(0, 100%, 0);
            transform: translate3d(0, 100%, 0);
        }

        to {
            opacity: 1;
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }
    }



    @-webkit-keyframes slideInRight {
        from {
            -webkit-transform: translate3d(100%, 0, 0);
            transform: translate3d(100%, 0, 0);
            visibility: visible;
        }

        to {
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }
    }

    /*------------form--------------*/
    @import url(https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css);
    @import url(https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.4.3/css/mdb.min.css);

    .form-white .md-form label {
        color: #fff;
    }

    .form-white input[type=text]:focus:not([readonly])+label {
        color: #fff;
    }

    .form-white input[type=password]:focus:not([readonly]) {
        border-bottom: 1px solid #fff;
        -webkit-box-shadow: 0 1px 0 0 #fff;
        box-shadow: 0 1px 0 0 #fff;
    }

    .form-white input[type=password]:focus:not([readonly])+label {
        color: #fff;
    }

    .form-white input[type=password],
    .form-white input[type=text] {
        border-bottom: 1px solid #fff;
    }


    /*--------------coffee section------------------*/
    .counter {
        background-color: #f5f5f5;
        padding: 20px 0;
        border-radius: 5px;
    }

    .count-title {
        font-size: 40px;
        font-weight: normal;
        margin-top: 10px;
        margin-bottom: 0;
        text-align: center;
    }

    .count-text {
        font-size: 13px;
        font-weight: normal;
        margin-top: 10px;
        margin-bottom: 0;
        text-align: center;
    }

    .fa-2x {
        margin: 0 auto;
        float: none;
        display: table;
        color: #4ad1e5;
    }
    .left { float: left; }
    .right { float: right; }
    #forgot_password { background-color: #4ad1e5 !important; }
    .none { border: none !important; background:none !important;}
    </style>

    <!------ Include the above in your HEAD tag ---------->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-61004855-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-61004855-1');
    </script></head>

<body>

    <!-- Carousel -->
    <div id="myCarousel" style="background-color: #FFFFFF;" class="carousel slide carousel-fade" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="mask flex-center">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-7 col-12 order-md-1 order-2">
                                <br><br><br><br>
                                <a class='none' href='https://workfar.com'><img src='https://workfar.com/view/index/logo.gif' /></a><br><h4>
                                   <i>Work, Hire, and Pay</i> on remote jobs with same email.</h4>
                            </div>
                            <div class="col-md-5 col-12 order-md-2 order-1">
                                <br><br><br><br><br><br>
                                <form id='login_form'>
                                    <div id='login_div' style="background-color: #0967a5;" class="card indigo form-white">
                                        <div class="card-body">
                                            <h3 class="text-center white-text py-3" style="color: #FFFFFF;"><i class="fa fa-user"></i>&nbsp;Login</h3>
                                            <!--Body-->
                                            <div class="md-form">
                                                <label for="defaultForm-email1"><i class="fa fa-envelope prefix white-text"></i>&nbsp;Your email</label>
                                                <input type="text" name='email' id="email" class="form-control">
                                            </div>
                                            <div id='password_div' class="md-form">
                                                <label for="password_label"><i class="fa fa-lock prefix white-text"></i>&nbsp;Your password</label>
                                                <input type="password" name='password' id="password" class="form-control">
                                            </div>
                                            <br />
                                            <div>
                                                <b id='forgot_password_success' style='display:none;color:#ffee11'>Check your email to reset your password!</b>
                                                <h3 id='error' style='display:none;color:#ffee11'>Email or Password not found.</h3>
                                                <b id='error_signup' style='display:none;color:#ffee11'>You already joined! Please login or change your password.</b>
                                                <br />
                                                <button style="background-color: #4595CD;border-color:#FFFFFF;" class="btn btn-info" id='login_btn'>Login</button>
                                                <button style="background-color: #ED4337; border-color:#FFFFFF;" class="btn btn-info" id='sign_up_btn'>Join</button>
                                            </div>
                                            <div>
                                                <a style='background-color: #4595CD !important; border-color:#FFFFFF;display:none;' class='left btn btn-info' id='back_btn'>Back</a>
                                                <a style='background-color: #4595CD !important; border-color:#FFFFFF;' class='right btn btn-info' id='forgot_password'>Forgot Password?</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefits================================================== -->
    <section class="service-sec" id="benefits">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading">
                        <br /><br />
                        
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
                case 'privacy_policy':
?>
        <iframe src='https://app.termly.io/document/privacy-policy/2b9a36f9-cbce-43ee-a084-38adda38aff4' width='100%'
            height='100%'></iframe>

        <?php
                    break;
            }
?>
<h5><small>Find work fast and gain experience and feedback</small>By doing telecommuting jobs
                            you do remote work
                            from your home office anywhere in the world</h5>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 text-sm-center service-block"> <i class="fa fa-plus"
                                aria-hidden="true"></i>
                            <h3>Remote Jobs</h3>
                            <p>WorkFar.com is a marketplace that connects workers with employers. WorkFar.com makes
                                hiring remote workers at an hourly rate easy, and soon will provide access to in-office
                                work, and
                                fixed-priced services. WorkFar.com is a one-stop shop for any work related needs, we
                                charge only 10% transaction
                                fees.</p>
                        </div>
                        <div class="col-md-6 text-sm-center service-block"> <i class="fa fa-leaf"
                                aria-hidden="true"></i>
                            <h3>Remote Work</h3>
                            <p>WorkFar.com does not handle any of the employer's money, we simply direct deposit it
                                through our Stripe payment gateway into the workers bank. You can request your money as
                                long as your balance is
                                over $0.29. </p>
                        </div>
                        <div class="col-md-6 text-sm-center service-block"> <i class="fa fa-leaf"
                                aria-hidden="true"></i>
                            <h3>Telecommuting Jobs</h3>
                            <p>By having less transaction fees than upwork and freelancer.com, we are tapping into
                                economic principles of supply and demand. By charging less we can handle more volume.
                                WorkFar.com has 1 employee, Upwork has over 300 and a giant office. Upwork has to pay
                                salaries for 300
                                people and a giant office bill, that’s why they charge 20%. They cannot afford to charge
                                any less.</p>
                        </div>
                        <div class="col-md-6 text-sm-center service-block"> <i class="fa fa-bell"
                                aria-hidden="true"></i>
                            <h3>Career Gudiance</h3>
                            <p>The site is easy to use, one of our beta testers is a 9 year old girl and she was able to
                                create a job and hire a person. It’s so easy elementary school students can use it, not
                                that we recommend
                                child labor, of course not, but multilingual who may not know english very well can
                                easily browse our site with
                                elementary level words.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
    </section>




    <!-- About ================================================== -->
    <section class="about-sec parallax-section" id="about">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h2><small>Who We Are</small></h2>
                </div>
                <div class="col-md-4">
                    <br /><br />
                    <p>WorkFar is a marketplace that connects workers with employers in a way that is fair to both
                        users.</p>
                    <p>It takes less than 10 minutes to master the site and will give you a lifetime of opportunity when
                        your employers see you are a trusted expert. When you add value to your employers life I'm
                        making a system faster or easier to use they will reward you for being the expert in your area.
                        Workfar stands out of the way and let the magic happen when people come together.
                    </p>
                </div>
                <div class="col-md-4">
                    <br /><br />
                    <p>Employers are protected my reviewing the video invoices of the worker and if there is a problem
                        can dispute it. Workfar does not take large fees such as freelancer.com who takes 10% from both
                        users. As of now the transaction fee is 0.01% which does not harm any of our users. We have
                        designed it to be easy to use to non-native speakers and with our video conferencing you can now
                        talk to your Indian Pakistani or thanks again workers in English and they will hear you in their
                        native language.</p>
                    <p><a href="#" class="btn btn-transparent-white btn-capsul">Explore More</a></p>
                </div>
            </div>
        </div>
    </section>


    <!--footer-->

    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12 footer-column">
                        <div class="logo-widget footer-widget">
                            <figure class="logo-box"><a href="#"><img src="" alt=""></a></figure>
                            <div class="text">
                                <p>Employers can make sure the workers are not doing other tasks while they are billing
                                time to the employer.
                                Upwork tried to solve this issue by taking a picture every 10 minutes, a sad attempt to
                                give clarity to the
                                employers. We give you video, if you see the worker is not working properly then you can
                                dispute the charge and
                                get your money back. You can easily skim through video invoices, it’s not required that
                                you watch all the videos
                                as an employer. If your worker is giving you results, by all means give them autonomy
                                and check less frequently.
                                Workfar.com is a fair and balanced system that allows workers to be guaranteed payment
                                by continuous billing
                                every 10 minutes, and guaranteed work completion for the employer. Also our main
                                competitor Upwork’s tool for
                                taking simple pictures every ten minutes is downloadable. At WorkFar.com everything
                                works in the browser and it
                                requires no downloads.</p>
                            </div>
                            <ul class="footer-social">
                                <li><a href="https://facebook.com/WorkFarOfficial" target="_blank"><i class="fab"></i>F</a></li>
                                <li><a href="https://twitter.com/job_teddy" target="_blank"><i class="fab">T</i></a></li>
                                <li><a href="https://instagram.com/tdicusmurray" target="_blank"><i class="fab">I</i></a></li>
                            </ul>
                        </div>
                    </div><div class="col-lg-4 col-md-6 col-sm-12 footer-column">
                        <div class="logo-widget footer-widget">
                            <figure class="logo-box"><a href="#"><img src="" alt=""></a></figure>
                            <div class="text">
                                    <p>We have them beat in every feature, and every price sector. WorkFar.com makes it easy to
                                video conference in the
                                website from across the globe, we even took it a step further and added a universal
                                translator to the video
                                conferencing. A Worker in India can hear his or her employer in English, and Hindi at
                                the same time. This is not
                                a feature that ensures understandability between cultures, but it will help the workers
                                get a clearer picture of
                                what the employers want if they see a particular word in their language in addition to
                                the employers
                                instructions in english.
                                <br><br>WorkFar.com’s payment system allows ACH direct deposits which take 3-5 business
                                days. We pay faster than
                                any marketplace on the market right now. Upwork takes 2 weeks to pay its workers,
                                Freelancer takes a similar
                                amount of time. As long as there are no disputes the worker can be sure to get their
                                money faster than any other
                                marketplace service. In their particular country the workers will be contractors and are
                                responsible for paying their own taxes.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 footer-widget">
                        <div class="contact-widget footer-widget">
                            <div class="footer-title">In God We Trust</div>
                            <div class="text">
                                <p>2000 Paradise Rd</p>
                                <p>(725) 212-6701</p>
                                <p>tdicusmurray@gmail.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- main-footer end -->
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 column">
                    <div class="copyright"><a href="#">WorkFar.com</a> &copy; 2019 All Right Reserved</div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 column">
                    <ul class="footer-nav">
                        <li><a href='https://WorkFar.com/work'>List Work</a>
                        <li><a href='https://WorkFar.com/members'>List Members</a>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="privacy_policy">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="/view/jquery/jquery-1.10.2.js"></script>
    <script src="/view/jquery/jquery-ui-1.10.3.custom.js"></script>
    <script src="/view/utility.js"></script>
    <script type='text/javascript'>
    $(document).ready(function() {
           $('meta[name="viewport"]').prop('content', 'width=1200');

   var remin = /(min-width\s*:\s*768px)|(min-width\s*:\s*980px)/i; 
var remax = /(max-width\s*:\s*979px)|(max-width\s*:\s*767px)|(max-width\s*:\s*480px)|(max-width\s*:\s*1049px)/i;

        if ($("#interview_text").val() != undefined) {
            $("#register_button").val($("#interview_text").val());
            $("#worker_id").val($("#worker_id_value").val());
        }
        $("#login_btn").click(function() {
            Utility.Form.post("/login", "#login_form", function(response) {
                var success = $.parseJSON(response);
                if (success[0] == true){
                    $(this).attr('disabled','true');
                    window.location = "/dashboard";
                }
                else {
                    $(this).attr('disabled','false'); 
                    $("#error").show();
                    $("#error_signup").hide();
                }
            });
            return false;
        });
        $("#sign_up_btn").click(function() {
            $("#employer_or_worker").val("true");
            Utility.Form.post("/create_account", "#login_form", function(response) {
                var success = $.parseJSON(response);
                if (success[0] == true) {
                    window.location = "/dashboard";
                    $(this).attr('disabled','false');
                }
                else {
                    $("#error_signup").show();
                    $("#error").hide();
                    $(this).attr('disabled','false');
                }
            });
            return false;
        });
        $("#salesvideo").click(function() {
            var vid = document.getElementById("salesvideo");
            if ($("salesvideo").attr("playing") == "true") {
                vid.pause();
                $("salesvideo").attr("playing", "false");
            } else {
                vid.play();
                $("salesvideo").attr("playing", "true");
            }
        });
        $("#forgot_password").click(function() {
            $("#forgot_password").html("Change Password");
            
            if(window.loginMode) {
                Utility.Form.post("forgot_password","#login_form",function(success) {
                    $("#forgot_password_success").show();
                    $("#forgot_password").html("Forgot Password?");
                    window.loginMode = !window.loginMode;
                    $("#back_btn").hide('fade');
                    $("#password_div").show('fade');
                    $("#login_btn").show('fade');
                    $("#sign_up_btn").show('fade');
                    return false;
                });
            }
            window.loginMode = !window.loginMode;
            $("#back_btn").show('fade');
            $("#password_div").hide('fade');
            $("#login_btn").hide('fade');
            $("#sign_up_btn").hide('fade');
        });
        $("#back_btn").click(function() {
            $("#forgot_password").html("Forgot Password?");
            window.loginMode = !window.loginMode;
            $("#back_btn").hide('fade');
            $("#password_div").show('fade');
            $("#login_btn").show('fade');
            $("#sign_up_btn").show('fade');
        });

    });

    </script>
</body>

</html>