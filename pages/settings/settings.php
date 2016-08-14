<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/signup.css');
$this->addResource('/css/settings.css');
$this->addResource('/js/dashboard-slide.js');
$this->addResource('/js/settings.js');
$this->addResource('/css/brevada.tooltip.css');
$this->addResource('/js/brevada.tooltip.js');

if(!Brevada::IsLoggedIn()){
	Brevada::Redirect('/logout');
}

$name = Database::query("SELECT `Name` FROM `companies` WHERE `companies`.`id` = {$_SESSION['CompanyID']} LIMIT 1")->fetch_assoc()['Name'];

$section = Brevada::FromGET('section');
$acceptableSections = ['account', 'feedback', 'billing', 'tablets', 'logins', 'stores', 'emails', 'personalize'];
if(!in_array($section, $acceptableSections)){ $section = 'account'; }
?>
<div class='top-fixed'>
	<div class='top-banner row'>
		<div class='col-lg-12'>
			<img class='logo-quote link pull-left' src='/images/brevada.png' data-link='' />
			<div class='dropdown pull-right'>
				<div class='three-lines btn btn-default dropdown-toggle'  data-toggle='dropdown'>
					<i class='fa fa-ellipsis-h'></i>
				</div>
				<ul class='dropdown-menu'>
					<li class='link' data-link='dashboard'><?php _e('Dashboard'); ?></li>
					<li class='link' data-link='logout' style="border-bottom: none;"><?php _e('Logout'); ?></li>
				</ul>
			</div>
			<div class='name pull-right'>
				<?php _e('Current User'); ?>: <span class="variable"><?php echo $name; ?></span>
			</div>
		</div>
	</div>
</div>

<div class='spacer'></div>

<div class='container-fluid'>
	<div class='row'>
		<div class='col-md-2'></div>
		<div class='col-sm-2 col-md-2 sidebar'>
			<ul class='nav nav-sidebar'>
				<li data-page='account'><a href='?section=account'><?php _e("Account"); ?></a></li>
				<li data-page='feedback'><a href='?section=feedback'><?php _e("Feedback"); ?></a></li>
				<li data-page='emails'><a href='?section=emails'><?php _e("Customer Emails"); ?></a></li>
				<li data-page='personalize'><a href='?section=personalize'><?php _e("Personalize"); ?></a></li>
				
				<?php if(($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) || (!$_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_STORE))){ ?>
				<li data-page='billing'><a href='?section=billing'><?php _e("Billing"); ?></a></li>
				<li data-page='logins'><a href='?section=logins'><?php _e("Manage Logins"); ?></a></li>
				<?php } ?>
				<?php if($_SESSION['Corporate']){ ?>
				<li data-page='stores'><a href='?section=stores'><?php _e("Manage Stores"); ?></a></li>
				<?php } ?>
				<?php if(($_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_COMPANY_STORES)) || (!$_SESSION['Corporate'] && Permissions::has(Permissions::MODIFY_STORE))){ ?>
				<li data-page='tablets'><a href='?section=tablets'><?php _e("Tablets"); ?></a></li>
				<?php } ?>
			</ul>
		</div>
		
		<script type='text/javascript'>
		$('ul.nav-sidebar > li').removeClass('active');
		$('ul.nav-sidebar > li[data-page="<?php echo $section;?>"]').addClass('active');
		</script>
		
		<div class='col-sm-10 col-md-6 panel panel-default'>
			<div class='panel-heading'><?php echo ucfirst($section); ?></div>
			<div class='panel-body'>
			<?php	
			$this->add(new View("../pages/settings/section_{$section}.php", array('valid' => true, 'POST' => $_POST, 'GET' => $_GET)));
			?>
			</div>
		</div>
		<div class='col-md-2'></div>
	</div>
</div>

<script type='text/javascript'>
$('.help[data-tooltip]').css('cursor', 'help').brevadaTooltip({
	fadeInDuration: 300,
	keepalive: true,
	offset: 15
});
</script>