<?php
$this->addResource('/pages/overall/packages/dygraph-combined.js');

$post_id = $this->getParameter('post_id');

  $query9=Database::query("SELECT * FROM feedback WHERE post_id='{$post_id}'");
	
	$num9 = $query9->num_rows;
	
	if($num9<2){
	?>
<span style="font-family: helvetica; color:#444444; font-size:12px;"><br />
This has not received enough ratings to generate a graph.</span>
<?php
	} else {
?>
<div id="graphdiv<?php echo $post_id; ?>" style="width:180px; font-family: helvetica; color:#444444; font-size:12px;"></div>
<?php } ?>
<script type="text/javascript">
  g<?php echo $post_id; ?>=new Dygraph(

    // containing div
    document.getElementById("graphdiv<?php echo $post_id; ?>"),

    // CSV or path to a CSV file.
    "Date,Rating\n" 
  <?php
  $query9=Database::query("SELECT AVG(value) as value, date FROM feedback WHERE post_id='{$post_id}' GROUP BY date");

while($row9=$query9->fetch_assoc()){
  	 $value=$row9['value'];
     $date=$row9['date'];
     ?>
   + "<?php echo $date; ?>,<?php echo $value; ?>\n"
    <?php } ?>
    ,
    
   {
     width: 180,
     height: 160,
     drawYGrid:false,
     drawXGrid:false,
     drawYAxis:false,
     drawXAxis:true,
     axisLineColor:'#AAAAAA',
     axisLabelColor:'#666666',
     drawPoints: true,
     pointSize:3,
     axisLabelFontSize: 10,
	 colors: ['#dc0101', '#EE1111', '#8AE234'],
     visibility: [true, true, true]
    }

  );
</script> 
