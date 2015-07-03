<?php
$this->addResource('/css/layout.css'); 
$this->addResource('/css/payment.css');

$user_id = Brevada::validate($_SESSION['user_id']);

$query = Database::query("SELECT `name`, `id`, `email`, `level`, `url_name`, `active` FROM `users` WHERE `id`='{$user_id}' LIMIT 1");

if($query === false || $query->num_rows == 0){
	Brevada::Redirect('/home/logout.php');
}

$name = ''; $email = ''; $level = ''; $url_name = ''; $active = '';

while($rows = $query->fetch_assoc()){
	$name = $rows['name'];
	$email = $rows['email'];
	$level = $rows['level'];
	$url_name = $rows['url_name'];
	$active = $rows['active'];
}
if($active=='yes'){
	Brevada::Redirect('/dashboard');
}
?>

<div class="home_section" style="background:#fff; margin-top:0px; padding-bottom:100px;">
	<div class="container">
		<div style="width:100%; text-align:center;">
			<img src="/images/brevada.png" style="width:150px; margin:0 auto; margin-top:10px;" />
			<div id="home_text">
				You're only <strong>one step</strong> away <?php echo $name; ?>!
			</div>
			<div id="home_text2" style="float:none; width:500px; margin:0 auto; margin-top:20px; text-align:center;">
				Gve us a call at <span id="emphasis">1 (844) BREVADA</span> and we'll help you figure out exactly what your business needs and get you set up!
			</div>
			<br />
			<div id="pricing_holder">
				<?php if($level==4){ ?>
				<div id="pricing_box">
					<div id="pricing_top" style="background:#FF2B2B;">
						<div class="pricing_title">
							<div  id="home_text" style="color:#fff;">
							PREMIUM
							</div>
							<div  id="pricing_price">
							<span id="price">$90</span>/month
							</div>
						</div>
						<div id="pricing_under">
							<i>Billed at $1080 for one year</i>
							<br />
							<strong>The premium package.</strong>
						</div>
					</div>
					<div id="pricing_bottom">
						<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
								 <input type="hidden" name="cmd" value="_xclick">
								 <input type="hidden" name="business" value="payments@brevada.com">
								 <input type="hidden" name="currency_code" value="CAD">
								 <input type="hidden" name="item_name" value="Brevada Premium Package (1 Year)">
								 <input type="hidden" name="amount" value="1080.00">
								 <input type="hidden" name="return" value="http://www.brevada.com/thanks">
								 <input type="hidden" name="notify_url" value="http://www.brevada.com/ipn.php?id=<?php echo $user_id; ?>">
								 <input type="submit" id="pricing_button"  name="submit" value="Pay Now" style="border:none; color:#f9f9f9; padding:5px; width:200px;">
					   </form>		
					</div>
				</div>
				<?php } else if($level==2) { ?>
				<div id="pricing_box">
					<div id="pricing_top" style="background:#FF2B2B;">
						<div class="pricing_title">
							<div  id="home_text" style="color:#fff;">
							PERSONAL
							</div>
							<div  id="pricing_price">
							<span id="price">$14</span>/month
							</div>
						</div>
						<div id="pricing_under">
							<i>Billed at $168 for one year</i>
							<br />
							<strong>The personal package.</strong>
						</div>
					</div>
					<div id="pricing_bottom">
						<form name="_xclick"  action="https://www.paypal.com/cgi-bin/webscr" method="post">
								 <input type="hidden" name="cmd" value="_xclick">
								 <input type="hidden" name="business" value="payments@brevada.com">
								 <input type="hidden" name="currency_code" value="CAD">
								 <input type="hidden" name="item_name" value="Brevada Personal Package (1 Year)">
								 <input type="hidden" name="amount" value="168.00">
								 <input type="hidden" name="return" value="http://www.brevada.com/thanks">
								 <input type="hidden" name="notify_url" value="http://www.brevada.com/ipn.php?id=<?php  echo $user_id; ?>">
								 <input type="submit" id="pricing_button"  name="submit" value="Pay Now" style="border:none; color:#f9f9f9; padding:5px; width:200px;">
					   </form>
					</div>
				</div>		
				<?php } else { ?>
				<div id="pricing_box">
					<div id="pricing_top" style="background:#FF2B2B;">
						<div class="pricing_title">
							<div  id="home_text" style="color:#fff;">
							BASIC
							</div>
							<div  id="pricing_price">
							<span id="price">$50</span>/month
							</div>
						</div>
						<div id="pricing_under">
							<i>Billed at $600 for one year</i>
							<br />
							<strong>The basic package.</strong>
						</div>
					</div>
					<div id="pricing_bottom">
						<form name="_xclick"  action="https://www.paypal.com/cgi-bin/webscr" method="post">
							 <input type="hidden" name="cmd" value="_xclick">
							 <input type="hidden" name="business" value="payments@brevada.com">
							 <input type="hidden" name="currency_code" value="CAD">
							 <input type="hidden" name="item_name" value="Brevada Basic Package (1 Year)">
							 <input type="hidden" name="amount" value="600.00">
							 <input type="hidden" name="return" value="http://www.brevada.com/thanks">
							 <input type="hidden" name="notify_url" value="http://www.brevada.com/ipn.php?id=<?php echo $user_id; ?>">
							 <input type="submit" id="pricing_button"  name="submit" value="Pay Now" style="border:none; color:#f9f9f9; padding:5px; width:200px;">
						</form>
					</div>
				</div>
				<?php } ?>
				<br />
				<br />
				<a href="upgrade.php">Switch Package</a>
			</div>
		</div>
		<br style="clear:both;" />
	</div>
</div>
<?php $this->add(new View('../template/long_footer.php')); ?>