<?php
if(!Brevada::IsLoggedIn()){ Brevada::Redirect('/home/logout'); }
if(!Permissions::has(Permissions::VIEW_ADMIN)){ Brevada::Redirect('/404'); }
?>
<h1 class="page-header">Messages</h1>

<h2 class="sub-header">Broadcast Message</h2>

<?php if(isset($_GET['sent'])){ ?>
<div class='alert alert-success'>Message sent.</div>
<?php } else if(isset($_GET['failed'])){ ?>
<div class='alert alert-danger'>An error has occured.</div>
<?php } ?>

<form class='form col-md-8 col-xs-12 col-lg-7' action='admin/update/new_message.php' method='post'>
	<div class='form-group'>
		<label>Recipient</label>
		<input class='form-control' placeholder='* = Everyone' name='txtTo' />
		<p class='help-block'>Which account should receive the broadcast?</p>
	</div>
	<div class='form-group'>
		<label>Type</label>
		<select class='form-control' name='ddType'>
			<?php
			if(($query = Database::query("SELECT id, Title FROM notification_type ORDER BY Title")) !== false){
				while($row = $query->fetch_assoc()){
					echo "<option value='{$row['id']}'>{$row['Title']}</option>";
				}
			}
			?>
		</select>
		<p class='help-block'>Type of message.</p>
	</div>
	<div class='form-group'>
		<label>Title</label>
		<input class='form-control' name='txtTitle' />
		<p class='help-block'>e.g. Try out our new beta feature!</p>
	</div>
	<div class='form-group'>
		<label>Description</label>
		<textarea class='form-control' rows='5' name='txtDescription'></textarea>
		<p class='help-block'>Main body of message.</p>
	</div>
	<div class='form-group'>
		<label>Options</label>
		<div class='checkbox'>
			<label>
				<input type='checkbox' name='ddSilent' /> Silent
			</label>
		</div>
	</div>
	<div class='form-group'>
	<br />
		<button type='submit' class='btn btn-default'>Send</button>
	</div>
</form>