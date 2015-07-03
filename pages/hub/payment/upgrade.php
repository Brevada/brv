<?php 
$this->addResource('/css/layout.css');
$this->addResource('/css/home_header.css'); 
$this->addResource('/css/home/eco.css'); 
?>					
<div class="home_section" style="background:#fff; margin-top:0px; padding-bottom:100px;">
	<div class="container">
		<div style="width:100%; text-align:center;">
			<img src="/images/brevada.png" style="width:150px; margin:0 auto; margin-top:10px;" />
			<div id="home_text">
				Take your <strong>pick</strong>!
			</div>
            <div id="home_text2" style="float:none; width:500px; margin:0 auto; margin-top:20px; text-align:center;">
				Or give us a call at <span id="emphasis">1 (844) BREVADA</span> and we'll help you figure out exactly what your business needs and get you set up!
			</div>
			<br />
			<div id="pricing_holder">
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
									<a href="process_upgrade.php?l=3" class="btn btn-lg btn-block btn-default">Get Started</a>
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
                                    <a href="process_upgrade.php?l=4" class="btn btn-lg btn-block btn-default">Get Started</a>
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
				<br style="clear:both;" />
			</div>
		</div>
		<br style="clear:both;" />
	</div>
</div>
<?php $this->add(new View('../template/long_footer.php')); ?>