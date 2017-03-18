<?php
$this->addResource('/css/reviewers_display.css');
$this->addResource('/js/reviewers_display.js');

$user_id = $this->getParameter('user_id');

$query = Database::query("SELECT `id`, `user_id`, `session_id`, `email` FROM reviewers WHERE user_id='{$user_id}' ORDER BY `id` DESC");

if($query->num_rows == 0){
	echo "<div class='text_clean' style='padding:4px;'>No Saved Reviewers</div>";
} else {

	while($row = $query->fetch_assoc()){
		$reviewer_id = $row['id'];
		$reviewer_session_id = $row['session_id'];
		$reviewer_email = $row['email'];
		?>
		<div id='a_reviewer' class='text_clean'>
		<div id='reviewer_left' class='left' style='width:200px;'><strong><?php echo $reviewer_email; ?></strong><br />
		<?php
		$reviewer_cum=0;
		$reviewer_count=0;
		$reviewer_average=0;
		
		$where = empty($reviewer_session_id) ? '' : " OR session_id='{$reviewer_session_id}'";
		
		$queryFeedback=Database::query("SELECT * FROM feedback WHERE (reviewer='{$reviewer_id}'{$where}) AND user_id='{$user_id}'");
		$numComments=Database::query("SELECT `id`, `reviewer`, `session_id`, `user_id` FROM comments WHERE (reviewer='{$reviewer_id}'{$where}) AND user_id='{$user_id}' ORDER BY `id` DESC")->num_rows;
		
			
		while($rowsFeedback = $queryFeedback->fetch_assoc()){
			$reviewer_cum += @intval($rowsFeedback['value']);
		}
		
		if($queryFeedback->num_rows > 0){
			$reviewer_average = round($reviewer_cum/$queryFeedback->num_rows, 2);
		}

		$color = "#EDC812";
		if($reviewer_average>75){
			$color = "#4EAF0E";
		} else if($reviewer_average<50){
			$color = "#E22A12";
		}
?>
	<div class="left" style="width:100px;">
		<span style="color:<?php echo $color; ?>">Average of <strong><?php echo $reviewer_average; ?></strong>.</span>
	</div>
	<div class="left" style="margin-left:8px;">
		<img src="/images/rating.png" style="width:12px; opacity:0.8;"/> <?php echo $queryFeedback->num_rows; ?>
	</div>
	<div class="left" style="margin-left:8px;">
		<img src="/images/comment.png" style="width:10px; opacity:0.8;" /> <?php echo $numComments; ?>
	</div>
	<br style="clear:both;" />
</div>
<div id="reviewer_left" style="width:45px; float:right;">
	<a id="reviewHead<?php echo $reviewer_id; ?>" class='reviewHead' reviewerid="<?php echo $reviewer_id; ?>" data-reveal-id="unlock"><div class="button4" style="font-size:11px; padding:3px;">Details</div></a>
</div>
<br style="clear:both;" />
	<div id="reviewContent<?php echo $reviewer_id; ?>" style="display:none; margin-top:5px;">
			<?php
			//Ratings			
			if(!empty($reviewer_session_id)){
				$queryFeedback = Database::query("SELECT * FROM feedback WHERE (reviewer='{$reviewer_id}' OR session_id='{$reviewer_session_id}') AND user_id='{$user_id}' ORDER BY `id` DESC");
			}
			else{
				$queryFeedback = Database::query("SELECT * FROM feedback WHERE reviewer='{$reviewer_id}' ORDER BY id DESC");
			}
			
			while($rowsFeedback=$queryFeedback->fetch_assoc()){
				$reviewer_values=$rowsFeedback['value'];
				$reviewer_post=$rowsFeedback['post_id'];
				$queryPosts=Database::query("SELECT `id`, `name` FROM posts WHERE id='{$reviewer_post}' LIMIT 1");
				$reviewer_post_name = '';
				while($rowsPosts = $queryPosts->fetch_assoc()){
					$reviewer_post_name = $rowsPosts['name'];
				}
			?>
			<div id="reviewer_review">
				<div id="reviewer_review_left">
					<?php echo $reviewer_post_name; ?>
				</div>
				<div id="reviewer_review_left" style="float:right; text-align:right;">
					<span id="reviewer_color"><?php echo $reviewer_values; ?></span>
				</div>
				<br style="clear:both;" />
			</div>
			<?php
			}
			
			if(!empty($reviewer_session_id)){
				$queryFeedback=Database::query("SELECT * FROM comments WHERE (reviewer='{$reviewer_id}' OR session_id='{$reviewer_session_id}') AND user_id='{$user_id}' ORDER BY id DESC");
			}
			else{
				$queryFeedback=Database::query("SELECT * FROM comments WHERE reviewer='{$reviewer_id}' ORDER BY id DESC");
			}
			while($rowsFeedback = $queryFeedback->fetch_assoc()){
				$reviewer_comment=$rowsFeedback['comment'];
				$reviewer_post=$rowsFeedback['post_id'];
				$queryPosts=Database::query("SELECT `id`, `name` FROM posts WHERE id='{$reviewer_post}' LIMIT 1");
				$reviewer_post_name = '';
				while($rowsPosts=$queryPosts->fetch_assoc()){
					$reviewer_post_name=$rowsPosts['name'];
				}
			?>
			<div id="reviewer_review">
				<div id="reviewer_review_left">
					<?php echo $reviewer_post_name; ?>
				</div>
				<div id="reviewer_review_left" style="float:right;">
					<span id="reviewer_color">"<?php echo $reviewer_comment; ?>"</span>
				</div>
				<br style="clear:both;" />
			</div>
			<?php
			}
			?>
	</div>
</div>
<?php
	}
}
?>