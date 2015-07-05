<?php
if(Brevada::IsLoggedIn()){ Brevada::Redirect('/dashboard'); }

$this->addResource('/css/home/animate.css');
$this->addResource('/css/home/bootstrap.css');
$this->addResource('/css/home/eco.css');

$this->addResource('/css/home/slick.css');

$this->addResource('/js/home/jquery-1.11.1.min.js');
$this->addResource('/js/home/bootstrap.min.js');
$this->addResource('/js/home/slick.min.js');
$this->addResource('/js/home/placeholdem.min.js');
$this->addResource('/js/home/rs-plugin/js/jquery.themepunch.plugins.min.js');
$this->addResource('/js/home/rs-plugin/js/jquery.themepunch.revolution.min.js');
$this->addResource('/js/home/waypoints.min.js');
$this->addResource('/js/home/scripts.js');


$this->add(new View('../template/home_header.php'));
?>




    <div class="wrapper">

        
		<!-- How it works -->
        <section id="about">
            <div class="container">
                
                <div class="section-heading scrollpoint sp-effect3">
                    <h1>How It Works</h1>
                    <div class="divider"></div>
                    <p>4 simple steps to bring your business to the next level.</p>
                </div>

                <div class="row">
                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <div class="about-item scrollpoint sp-effect2">
                            <!-- <i class="fa fa-hand-o-up fa-2x"></i> -->
                            <div class="icon-holder">1</div>
                            <h3>Pick Your Plan</h3>
                            <p>Choose the plan that's right for your business.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-6" >
                        <div class="about-item scrollpoint sp-effect5">
                            <!-- <i class="fa fa-list fa-2x"></i> -->
                            <div class="icon-holder">2</div>
                            <h3>Customize</h3>
                            <p>Specify the <i>products, services, or other aspects</i> of your business that you want to get feedback on.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-6" >
                        <div class="about-item scrollpoint sp-effect5">
                            <!-- <i class="fa fa-users fa-2x"></i> -->
                            <div class="icon-holder">3</div>
                            <h3>Gather Results</h3>
                            <p>Gather feedback on those aspects from your customers by sharing your <b>Brevada Page</b> URL and with <b>tablets</b> (included in every Brevada subscription!)</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-6" >
                        <div class="about-item scrollpoint sp-effect1">
                            <!-- <i class="fa fa-external-link-square fa-2x"></i> -->
                            <div class="icon-holder">4</div>
                            <h3>Analyze and Compare</h3>
                            <p>Our revolutionary data reporting dashboard tells you what you need to know to take your business to the next level.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
		
		<!-- Benefits -->
        <section id="features">
            <div class="container">
                <div class="section-heading scrollpoint sp-effect3">
                    <h1>Receive Feedback</h1>
                    <div class="divider"></div>
                    <p>We believe in <b>one-click feedback</b>.</p>
                </div>
                <div class="row">
                    <div class="side col-md-4 col-sm-4 scrollpoint sp-effect1">
                        
                        <div class="media feature">
                            <a class="pull-right" href="#">
                                <i class="fa fa-envelope fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">Brevada Page</h3>
                                Forget surveys! We make giving feedback quick and easy.
                            </div>
                        </div>
                        
                        <div class="media feature ">
                            <a class="pull-right" href="#">
                                <i class="fa fa-cogs fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">Tablets</h3>
                                Brevada feedback tablets are included with every plan.
                            </div>
                        </div>
                        <div class="media eature">
                            <a class="pull-right" href="#">
                                <i class="fa fa-users fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">Custom URL</h3>
                                Recieve feedback through brevada.com/<i>yourcompany</i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4" >
                        <img src="/images/tablet.png" class="img-responsive scrollpoint sp-effect5" alt="">
                    </div>


                    <div class="side col-md-4 col-sm-4 scrollpoint sp-effect2">
                        
                        <div class="media feature">
                            <a class="pull-left" href="#">
                                <i class="fa fa-comments fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">Custom QR Code</h3>
                                Making it extremely easy to share your URL.
                            </div>
                        </div>
                        <div class="media feature">
                            <a class="pull-left" href="#">
                                <i class="fa fa-calendar fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">Marketing Material</h3>
                                We auto create custom printables for you to share.
                            </div>
                        </div>
                        <div class="media active feature">
                            <a class="pull-left" href="#">
                                <i class="fa fa-plus fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">And much more!</h3>
                                New features released monthly.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="reviews">
            <div class="container">
                <div class="section-heading inverse scrollpoint sp-effect3">
                    <h1>Get Results</h1>
                    <div class="divider"></div>
                    <p>We believe in <b>actionable data</b>.</p>
                    <img class="focus-image" src="/images/macbook.png" alt="">
                </div>
                <div class="row">
     <!--                <div class="col-md-10 col-md-push-1 scrollpoint sp-effect3">
                        <div class="review-filtering">
                            <div class="review">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="review-person">
                                            <img src="http://api.randomuser.me/portraits/women/94.jpg" alt="" class="img-responsive">
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="review-comment">
                                            <h3>“Brevada offers incredible insight into my business and allows me to gather feedback in an attractive and classy fashion.”</h3>
                                            <p>
                                                - Krin Fox
                                                <span>
                                                    <i class="fa fa-star fa-lg"></i>
                                                    <i class="fa fa-star fa-lg"></i>
                                                    <i class="fa fa-star fa-lg"></i>
                                                    <i class="fa fa-star fa-lg"></i>
                                                    <i class="fa fa-star-half-o  fa-lg"></i>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="review rollitin">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="review-person">
                                            <img src="http://api.randomuser.me/portraits/men/70.jpg" alt="" class="img-responsive">
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="review-comment">
                                            <h3>“Oleaose Is The Best Stable, Fast App I Have Ever Experienced”</h3>
                                            <p>
                                                - Theodore Willis
                                                <span>
                                                    <i class="fa fa-star fa-lg"></i>
                                                    <i class="fa fa-star fa-lg"></i>
                                                    <i class="fa fa-star fa-lg"></i>
                                                    <i class="fa fa-star-half-o fa-lg"></i>
                                                    <i class="fa fa-star-o fa-lg"></i>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="review rollitin">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="review-person">
                                            <img src="http://api.randomuser.me/portraits/men/93.jpg" alt="" class="img-responsive">
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="review-comment">
                                            <h3>“Keep It Up Guys Your Work Rules, Cheers :)”</h3>
                                            <p>
                                                - Ricky Grant
                                                <span>
                                                    <i class="fa fa-star fa-lg"></i>
                                                    <i class="fa fa-star fa-lg"></i>
                                                    <i class="fa fa-star fa-lg"></i>
                                                    <i class="fa fa-star-half-o fa-lg"></i>
                                                    <i class="fa fa-star-o fa-lg"></i>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                     <div class="feature col-md-4">
                            <a class="icon" href="#">
                                <i class="fa fa-map-marker fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">Industry Comparisons</h3>
                                View comparisons to industry averages.
                            </div>
                        </div>
                        <div class="feature col-md-4">
                            <a class="icon" href="#">
                                <i class="fa fa-compress fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">Intra-Company Analysis</h3>
                                View comparisons between the different aspects internally.
                            </div>
                        </div>
                        <div class="feature col-md-4">
                            <a class="icon" href="#">
                                <i class="fa fa-compass fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">Track Progress</h3>
                                Monitor improvement over time.
                            </div>
                        </div>
                </div>
            </div>
        </section>
		
		<!-- PRICING -->
        <section id="screens">
                <div class="container scrollpoint sp-effect3">
					<div class="row">
						<!-- Pricing Module 1 -->
                        <div class="col-md-4">
							<div class="panel panel-success panel-pricing">
								<div class="panel-heading">
									<h4 class="text-center title">Basic</h4>
                                    <p class="lead text-center">
                                        <span class="price"><span class="currency">$</span>50<span class="time">/month</span></span>
                                        <br />
                                        <span class="sub-price">Charged annualy at $600</span>
                                    </p>
								</div>
								<ul class="list-group list-group-flush text-center">
                                    <li class="list-group-item main-focus">
                                        Custom Feedback Page, QR Code, and Marketing Materials
                                    </li>
									<li class="list-group-item">
										2 <emp>Tablets</emp>
									</li>
									<li class="list-group-item">
										1 Login
									</li>
									<li class="list-group-item">
										Advanced <emp>Data Reporting</emp> and <emp>Competition Comparison</emp> Reports 
									</li>
									<li class="list-group-item">
										24/7 Technical Support
									</li>
                                    <li class="list-group-item">
                                        Weekly <emp>Email Reports</emp>
                                    </li>
								</ul>
								<div class="panel-footer">
									<a href="/signup?l=professional" class="btn btn-lg btn-block btn-default">Get Started</a>
								</div>
							</div>
						</div>

                        <!-- Pricing Module 2 -->
                        <div class="col-md-4">
                            <div class="panel panel-success panel-pricing">
                                <div class="panel-heading">
                                    <h4 class="text-center title">Premium</h4>
                                    <p class="lead text-center">
                                        <span class="price"><span class="currency">$</span>90<span class="time">/month</span></span>
                                        <br />
                                        <span class="sub-price">Charged annualy at $1080</span>
                                    </p>
                                </div>
                                <ul class="list-group list-group-flush text-center">
                                    <li class="list-group-item main-focus">
                                        Custom Feedback Page, QR Code, and Marketing Materials
                                    </li>
                                    <li class="list-group-item">
                                        5 <emp>Tablets</emp>
                                    </li>
                                    <li class="list-group-item">
                                        3 Logins
                                    </li>
                                    <li class="list-group-item">
                                        Advanced <emp>Data Reporting</emp> and <emp>Competition Comparison</emp> Reports 
                                    </li>
                                    <li class="list-group-item">
                                        24/7 Technical Support
                                    </li>
                                    <li class="list-group-item">
                                        Weekly <emp>Email Reports</emp>
                                    </li>
                                </ul>
                                <div class="panel-footer">
                                    <a href="/signup?l=premium" class="btn btn-lg btn-block btn-default">Get Started</a>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Module 3 -->
                        <div class="col-md-4">
                            <div class="panel panel-success panel-pricing">
                                <div class="panel-heading">
                                    <h4 class="text-center title">Custom</h4>
                                    <p class="lead text-center">
                                        <span class="price" style="line-height: 10px;"><span class="time">Contact Us</span></span>
                                        <br />
                                        <span class="sub-price"></span>
                                    </p>
                                </div>
                                <ul class="list-group list-group-flush text-center">
                                    <li class="list-group-item">
                                        Get a custom number of tablets, logins, and marketing materials. For one to many locations.
                                    </li>
                                </ul>
                                <div class="panel-footer">
                                    <a href="/signup" class="btn btn-lg btn-block btn-default">Contact Us</a>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
        </section>

    </div>
    <script>
        $(document).ready(function() {
            appMaster.preLoader();
        });
    </script>

<?php $this->add(new View('../template/long_footer.php')); ?>