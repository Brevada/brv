<?php
$tablet = $this->getParameter('tablet') === true;
$message = $this->getParameter('message');
?>

<div class='comments-btn<?= $tablet ? ' tablet' : ''; ?>' id='btn-submit-comment'>
	<i class='fa fa-quote-left'></i>
</div>

<div id="comment-form-overlay" class='pp-overlay'></div>
<div id="comment-form" style='display:none;' class='pp <?= $tablet ? " tablet" : ""; ?>'>
	<div class='content'>
		<p class='pp-message pp-centered'><?= empty($message) ? "Send us a message." : $message; ?></p>
		
		<div class='pp-textarea-box'>
			<textarea class='auto-focus' placeholder='Start typing...' data-pp-key='comment' data-pp-label='Comment'></textarea>
			<div class='pp-btn-done' data-type='submit'>Done</div>
			<div class='pp-btn-cancel' data-type='dismiss'>Cancel</div>
		</div>
	</div>
</div>