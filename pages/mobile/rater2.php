<?php
$this->addResource('/css/mobile/rater2.css');
$post_id = $this->getParameter('post_id');
?>
<div onclick="disappear_<?php echo $post_id; ?>()"  style="width:250px;">
<div style="float:left; width:40px;">
<!-- Display -->
<div align="center" style="width:40px; height:36px; border:0px solid #dcdcdc; font-size:20px;  float:left;
overflow:hidden; font-family:tahoma;
color:#fefefe; 
background: rgb(255,79,63); /* Old browsers */
background: -moz-linear-gradient(top,  rgba(255,79,63,1) 0%, rgba(206,0,0,1) 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,79,63,1)), color-stop(100%,rgba(206,0,0,1))); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  rgba(255,79,63,1) 0%,rgba(206,0,0,1) 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  rgba(255,79,63,1) 0%,rgba(206,0,0,1) 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  rgba(255,79,63,1) 0%,rgba(206,0,0,1) 100%); /* IE10+ */
background: linear-gradient(to bottom,  rgba(255,79,63,1) 0%,rgba(206,0,0,1) 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ff4f3f', endColorstr='#ce0000',GradientType=0 ); /* IE6-9 */
">
<?php
for ($j=1; $j <= 100; $j++) {
?>
<style>
#result<?php echo $j; ?>_<?php echo $post_id; ?> { display:none;}
</style>
<script>
$(document).ready(function () {
    $("#hov<?php echo $j; ?>_<?php echo $post_id; ?>").hover(function () {
        $("#result<?php echo $j; ?>_<?php echo $post_id; ?>").css("display", "block");
    });
    $("#hov<?php echo $j; ?>_<?php echo $post_id; ?>").mouseleave(function () {
        $("#result<?php echo $j; ?>_<?php echo $post_id; ?>").css("display", "none");
    });
});
</script>
<div id="result<?php echo $j; ?>_<?php echo $post_id; ?>" style="margin-top:1px;">
<?php echo $j; ?> <br><br>
</div>
<?php
}
?>
<div style="margin-top:2px; font-size:12px;">
<!-- Rate: -->
</div>
</div>
</div>
<script>
    function submitOnClick(formName){
        document.forms[formName].submit();
    }
</script>
<div style="float:left; width:210px;">

<!-- Bar -->
<div style="margin-left:7px; float:left;">
<?php
$ipaddress=$_SERVER["REMOTE_ADDR"];
$z=0;
for ($i=1; $i <= 100; $i++) {
$z+=$i;
?>
<a href="#" onclick="doSomething('<?php echo $i; ?>', '<?php echo $post_id; ?>', '<?php echo $ipaddress; ?>', '<?php echo $country; ?>');">
<div  class="circle" id="hov<?php echo $i; ?>_<?php echo $post_id; ?>" style="opacity:<?php echo (0.8-($i/100)); ?>;height:36px; width:8px;  z-index:<?php echo $i;?>; margin:0px; margin-left:-6px; ">
</div>
</a>
<?php
}
?>
</div>
<br style="clear:both;" />
<div style="float:left; font-family:tahoma; height:36px; width:160px; font-size:10px; margin-left:45px; margin-top:-36px;
background: rgb(180,227,145); /* Old browsers */">
</div>
<div style="display:none;float:left; color:#999; font-family:tahoma; font-size:10px; margin-left:5px;">
Select a grade from 1-100 using the bar.
</div>
</div>
</div>
<script type="text/javascript">
function doSomething(val,id,ip,country) {
    $.get("insert_rating.php?valu= + val + "&posti= + id + "&ipaddres= + ip + "&countr= + country);
    return false;
}
function disappear_<?php echo $post_id; ?>() {
	document.getElementById("full_bar_<?php echo $post_id; ?>").style.display='none';
	document.getElementById("full_bar2_<?php echo $post_id; ?>").style.display='none';
	document.getElementById("appear_bar_<?php echo $post_id; ?>").style.display='block';

}
</script>