<?php
$this->addResource('/css/post_box.css');
$this->addResource('/js/post_box.js');

$r = $this->getParameter('row');

$post_id = $r['ID'];
$post_name = $r['Title'];
$post_description = $r['Description'];

$isMobile = $this->getParameter('mobile');
$isMobile = empty($isMobile) ? '' : '_mobile';

$ipAddress = $this->getParameter('ip');

$userAgent = $_SERVER['HTTP_USER_AGENT'];

/* Authorized tablet user agent. */
$authUserAgent = TABLET_USERAGENT;

/*
	Taken directly from /pages/overall/insert/insert_rating.php
	(NOW() - INTERVAL 1 HOUR) determines how long a user must wait before rating again.
*/
$alreadyRated = true;
if(($check = Database::prepare("SELECT `feedback`.id FROM `feedback` LEFT JOIN user_agents ON user_agents.ID = feedback.UserAgentID WHERE `feedback`.AspectID = ? AND `feedback`.IPAddress = ? AND (`feedback`.`Date` > NOW() - INTERVAL 1 HOUR) AND `user_agents`.UserAgent = ? AND `user_agents`.UserAgent <> ? LIMIT 1")) !== false){
	$check->bind_param('isss', $post_id, $ipAddress, $userAgent, $authUserAgent);
	if($check->execute()){
		$check->store_result();
		if($check->num_rows == 0){
			$alreadyRated = false;
		}
		$check->close();
	}
}
?>

<div id="aspect_<?php echo $post_id; ?>" class="aspect<?php echo $alreadyRated ? ' rated' : ''; ?>">
		<div class="aspect-header">
			<div class="title"><?php echo $post_name; ?></div>
			<!-- <div class="subtitle"><?php echo substr($post_description,0,85); if(strlen($post_description)>85){echo '...';} ?></div> -->
		</div>

		<div class="rating-box">
			<?php				
			if(!$alreadyRated){
			?>
			<div><?php $this->add(new View('../widgets/profile/star_rating_bar.php', array('row' => $r, 'id' => $this->getParameter('id')))); ?></div>
			<br style="clear: both;" />
			<div class="rate-description">
				<div class="pull-left">Worst</div>
				<div class="pull-right">Best</div>
				<br class="clear" />
			</div>
			<?php } else { ?>
			<div class="appear" id="appear_bar_<?php echo $post_id; ?>"  align="center" style="width:100%;">Already rated.</div>	
			<?php } ?>
		</div>

	</div>