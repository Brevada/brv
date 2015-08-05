<?php
if($this->getParameter('valid') !== true){ exit('Error.'); }
$_POST = $this->getParameter('POST');

if($_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_COMPANY_STORES)){
	exit('Permission denied.');
}

if(!$_SESSION['Corporate'] && !Permissions::has(Permissions::MODIFY_STORE)){
	exit('Permission denied.');
}
?>
<form id='frmAccount' action='settings?section=tablets' method='post'>
<div class='form-account'>
	<span class="form-subheader">This feature is not available yet.</span><br />
</div>
</form>