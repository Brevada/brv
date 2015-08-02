<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Overview</h1>

<p>Welcome to the Brevada Admin Panel!</p>
<p>Note that all administrative actions are logged for security purposes.</p>