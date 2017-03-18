<?php
$this->addResource('/css/layout.css');
$this->addResource('/css/scores.css');

$id = Brevada::validate($_POST['id']);
$url_name = Brevada::validate($_GET['name']);

//GET COUNTRY
$geo = Geography::GetGeo();
$ip = $geo['ip'];
$country = $geo['country'];


//Test for mobile
if(Brevada::IsMobile()){
	Brevada::Redirect("/mobile/profile.php?name={$url_name}");
}

//Check browser type
$ie=Brevada::IsInternetExplorer();

$query = Database::query("SELECT * FROM users WHERE url_name = '{$url_name}' LIMIT 1");

if($query->num_rows==0){
	Brevada::Redirect('/profile/not_found.php');
}

$id = ''; $user_id = ''; $name = ''; $type = ''; $user_extension = ''; $corporate = ''; $public = '';
while($rows=$query->fetch_assoc()){
   $id=$rows['id'];
   $user_id=$id;
   $name = $rows['name'];
   $type = $rows['type'];
   $user_extension = $rows['extension'];
   $corporate=$rows['corporate'];
   $public=$rows['public'];
}

if($corporate='1'){
	Brevada::Redirect("/corporate/profile/corporate_profile.php?name={$url_name}");	
}

$session_id = $_SESSION['user_id'];
  
if($public!=1&&$session_id!=$id){
	Brevada::Redirect("/home");
} else {

$this->setTitle("{$name} Feedback on Brevada");
$this->addResource("<meta property='og:title' content='{$name} Score on Brevada'/>", true, true);
$this->addResource("<meta property='og:type' content='website'/>", true, true);
$this->addResource("<meta property='og:url' content='http://brevada.com/{$url_name}'/>", true, true);
$this->addResource("<meta property='og:image' content='http://brevada.com/images/square_logo.png'/>", true, true);
$this->addResource("<meta property='og:site_name' content='Brevada'/>", true, true);
$this->addResource("<meta property='og:description' content='{$name} Score on Brevada'/>", true, true);
?>

<!-- NEW BANNER -->

<div id="banner_main">
	<div id="banner_main_content">
		<div id="banner_main_logo" style="outline:none;">
		<a href="/index.php" style="outline:none;"><img src="/images/brevada.png" style="height:30px; margin-top:4px;" /></a>
		</div>
		<br style="clear:both;" />
	</div>
</div>

<script type='text/javascript'>
$(document).ready(function(){
    setTimeout(function(){ $(".dis").fadeOut(); }, 3000);
    
    $("#needhelp").click(function() { 
    $("#helper").fadeIn("slow");
  });
});

function message_show() {
	$("#message_box").show();
}
function help_show() {
	$("#help_box").show();
}
</script>

 <div  style="width:1030px; margin: 0 auto; margin-top:0px;  padding-top:0px;">
	
	 		
 		
 		<!-- NEW STUFF -->
 
 		
 		<!-- -->

 <div id="sized_containerHub">	
 
 	<!-- LEFT -->
 	<script>
 	$(document).ready(function() {  
	 var offset=$('#far_left').offset();  
	
	 $(window).scroll(function () {  
	   var scrollTop=$(window).scrollTop(); 
	
	   if (offset.top<scrollTop) $('#far_left').addClass('fixedL');  
	   else $('#far_left').removeClass('fixedL');  
	  });  
	});
 	</script>
 	
 	<div  style="float:left; width:250px; margin-top:10px;">
		
	<div id="far_left">	
			<?php include '../includes/profile_info_pod.php'; ?>
            
            <br style="clear:both;" />
            <a href="/approved"><img src="/images/approved_1.png" width="220px" /></a>
            
            <br style="clear:both;" />
            <img src="/images/approved_2.png" width="220px" style="margin-top:10px;" />
            
 	</div>
 	</div>
 	
 	
 	
 	
 	<!-- RIGHT -->	
 	<div style="float:left; width:520px; margin-top:5px; overflow:hidden;">
 	
    <style>

 		</style>
        
     <?php $userdata=userdata($user_id); ?>
    <div id="score_title" style="margin-top:0px;">
            OFFICIAL BREVADA SCORESHEET
           </div> 
    <div class="rating_holder" style="">
         <span itemscope itemtype="http://data-vocabulary.org/Review-aggregate">
         <span itemprop="itemreviewed"><?php echo $name; ?></span> has an average of <span style="color:<?php echo $userdata['color']; ?>; font-weight:bold;"> <span itemprop="rating" itemscope itemtype="http://data-vocabulary.org/Rating"> <span itemprop="average"><?php echo $userdata['average']; ?></span> out of <span itemprop="best">100</span></span></span> in <strong><span itemprop="count"><?php echo $userdata['count']; ?></span></strong> ratings.    
    	 </span>
    </div>
 	<!-- BAR CHART --> 	
 	<div style="width:100%; height:10px; margin-top:0px;">
 			
			<a class="tooltip" return false>
			<span class="green">
				<p class="resp_big"><?php echo round($userdata['good'],2); ?>%</p>
				<br />
				<p class="resp_small">of all responses are greater than 80</p>
			</span>
			<div class="bar_color" style="float:left; height:15px; width:<?php echo $userdata['good']; ?>%; background:#4EAF0E;">
			
			</div>
			</a>
			
			<a  class="tooltip" return false>
			<span class="orange" >
				<p class="resp_big"><?php echo round($userdata['middle'],2); ?>%</p>
				<br />
				<p class="resp_small">of all responses are between 50 and 80</p>
			</span>
			
			<div class="bar_color" style="float:left; height:15px; width:<?php echo $userdata['middle']; ?>%; background:#EDC812;">
			
			</div>
			</a>
			
			<a class="tooltip" return false>
			<span class="red">
				<p class="resp_big"><?php echo round($userdata['bad'],2); ?>% </p>
				<br />
				<p class="resp_small">of all responses are less than 50</p>
			</span>
			
			<div  class="bar_color" style="float:left; height:15px; width:<?php echo $userdata['bad']; ?>%; background:#E22A12;">
			
			</div>
			</a>
			
			
            <br style="clear:both;" />
 			
 		
 		</div>
 
    
    <div id="score_title">
    ASPECT DATA
    </div> 
     
    <!-- ASPECTS -->
    <?php
    $query=Database::query("SELECT * FROM posts WHERE user_id='{$user_id}' AND active='yes' ORDER BY id DESC");

	while($rows=$query->fetch_assoc()){
		$post_id=$rows['id'];
		$active = $rows['active'];
		$post_name=$rows['name'];
		$post_extension=$rows['extension'];
		$post_description=$rows['description'];
		$postdata=postdata("$post_id");
 	?>
    	  
        <div class="post_holder">
             <strong><?php echo $post_name; ?></strong> has an average of <span style="color:<?php echo $postdata['color']; ?>; font-weight:bold;"><?php echo $postdata['average']; ?></span> in <strong><?php echo $postdata['count']; ?></strong> ratings.    
        </div>
        <!-- POST BAR CHART --> 	
        <div class="post_bar" style="width:100%; height:7px; margin-top:0px;">
                
                <a class="tooltip" return false>
                <span class="green">
                    <p class="resp_big"><?php echo round($postdata['good'],2); ?>%</p>
                    <br />
                    <p class="resp_small">of all responses are greater than 80</p>
                </span>
                <div class="bar_color" style="float:left; height:7px; width:<?php echo $postdata['good']; ?>%; background:#4EAF0E;">
                
                </div>
                </a>
                
                <a  class="tooltip" return false>
                <span class="orange" >
                    <p class="resp_big"><?php echo round($postdata['middle'],2); ?>%</p>
                    <br />
                    <p class="resp_small">of all responses are between 50 and 80</p>
                </span>
                
                <div class="bar_color" style="float:left; height:7px; width:<?php echo $postdata['middle']; ?>%; background:#EDC812;">
                
                </div>
                </a>
                
                <a class="tooltip" return false>
                <span class="red">
                    <p class="resp_big"><?php echo round($postdata['bad'],2); ?>%</p>
                    <br />
                    <p class="resp_small">of all responses are less than 50</p>
                </span>
                
                <div  class="bar_color" style="float:left; height:7px; width:<?php echo $postdata['bad']; ?>%; background:#E22A12;">
                
                </div>
                </a>
                
                
                <br style="clear:both;" />
            
            </div>
    <?php } ?>
    
    <!-- COMMENTS -->
    <div id="score_title">
    COMMENTS
    </div> 
    <?php
    $query=Database::query("SELECT * FROM comments WHERE user_id='{$user_id}' ORDER BY id DESC");
	
	if($query->num_rows==0){
		?>
		<span class="text_clean">No Comments</span>
		<?php
	
	}
	
	while($rows=$query->fetch_assoc()){
		$comment_id=$rows['id'];
		$comment = $rows['comment'];
		$post_id=$rows['post_id'];
		
		$query2=Database::query("SELECT * FROM posts WHERE id='{$post_id}' LIMIT 1");

		$post_name = '';
		while($rows2=$query2->fetch_assoc()){
		$post_name=$rows2['name'];
		}
 	?>
    
        <div class="comment_holder">
             <span style="font-size:11px;">RE <?php echo $post_name; ?>:</span> "<?php echo $comment; ?>"   
        </div>
  
    <?php } ?>
    
    
    <br />
 	
 	</div>
 
 
 
 </div>
</div>

<!-- CLOSE OVERALL IF STATEMENT -->
<?php } ?>
<?php $this->add(new View('../template/footer.php')); ?>