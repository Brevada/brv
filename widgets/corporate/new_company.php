<?php
	$this->addResource('/css/new_company.css');
	
	$user_id = $this->getParameter('user_id');
	
	$queryCredits = Database::query("SELECT * FROM corporate_credits WHERE user_id='{$user_id}'");
	
	$credits = $queryCredits->num_rows;
?>

<div style="width:100%; color:#777; font-size:12px; padding-left:15px;">

<span id="red" style=" font-size:15px;">Add new companies</span>

<br />

<form action="<?php echo p('HTML','path_corporatehub','create_companies.php'); ?>" method="POST">

<div id="new_comp_text">
How many <strong>identical</strong> companies do you want to create? <span id="red" style="font-size:11px;"><?php echo $credits ?> credits available</span>
</div>



<div class="styled" style="float:left;">
   <select name="num">
   		<?php for($i=1; $i<=$credits; $i++){ ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php } ?>
    </select>
</div>

<br style="clear:both;" />


<div id="new_comp_text">
How many aspects would you like to get feedback on for each business? <span id="red" style="font-size:11px;">(eg. pricing, wait time, location)</span>
</div>



<div class="styled" style="float:left;">
   <select name="aspects">
   		<?php for($i=1; $i<=10; $i++){ ?>
        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php } ?>
    </select>
</div>

<br style="clear:both;" />


<input class="button4" type="submit" style="width:300px; text-align:center;" value="Create Companies" />

</form>

</div>


<br style="clear:both;" />