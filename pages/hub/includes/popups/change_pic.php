<?php
$this->addResource('/css/layout.css'); 
?>
<div id="modal_title" class="text_clean">Change Picture</div>        
  <div  style="margin-top:30px; padding:4px; width:500px;">
  <br style="clear:both;" />
  <br style="clear:both;" />
    <form action="/hub/update/picture_change.php" method="post" enctype="multipart/form-data">
      <input type="file" name="file" style="float:left; width:150px;" />
      <input class="button2" type="submit" name="submit" value="Change" />
      <br style="clear:both;" />
    </form>
    <br style="clear:both;" />
    <br style="clear:both;" />
  </div>