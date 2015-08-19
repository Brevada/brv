<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }

$storeID = empty($_GET['storeID']) ? false : @intval($_GET['storeID']);
?>
<h1 class="page-header">Data Analysis</h1>

<form method='get' action='/admin'>
	<input type='hidden' name='show' value='data' />
	<div class='col-lg-6 col-md-6 input-group'>
		<input type='text' class='form-control' name='storeID' placeholder='Enter the store ID...' /> 
		<span class='input-group-btn'><button type='submit' class='btn btn-default'>Analyze</button></span>
	</div>
</form>

<h2 class="sub-header">Financial Data<?php echo $storeID ? " for Store #{$storeID}" : ''; ?></h2>

<div class="table-responsive">
<?php if(!$storeID){ ?>
	<p>No store is selected.</p>
<?php } else { ?>
<div class='well'><?php BrevadaData::execute_analysis($storeID); ?></div>
<?php } ?>
</div>