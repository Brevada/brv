<?php
$this->addResource('/css/search_result.css');

$user_id = $this->getParameter('user_id');
$user = $this->getParameter('user');

$userdata = userdata($user_id);
?>

<div id="result">
    
    <div id="result_title">
    	<strong><?php echo $user['name']; ?></strong> Feedback on Brevada  
    </div>
   
    <div id="result_green">
    	www.brevada.com/profile/scores/scores.php?name=<?php echo $user['url_name']; ?>
    </div>
     <div id="result_rating">
     <?php 
	 	$av=round($userdata['average']/20)*10;
	 	//$a=5 * round($av / 5)); 
	 	//echo $av; 
			
			?>
    	<div style="float:left;"><span class="rating-static rating-<?php echo $av; ?>"></span></div>
        <div style="float:left;">Rating: <?php echo $userdata['average']; ?>% - <?php echo $userdata['count']; ?> Reviews</div>
        <br style="clear:both;" />
    </div>
    <div id="result_main">
    	Grapeway Winery has an average of 66.63 out of 100 in 282 ratings. 58.16% of all responses are greater than 80. 16.67% of all responses are between 50 and ...
    </div>
    <div id="result_sub">
    
    </div>
</div>