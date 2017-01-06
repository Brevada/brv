<?php
$store_id = $this->getParameter('store_id');
$store_id = @intval($store_id);

$tablet = $this->getParameter('tablet') === true;

$dataT = DataTemplate::fromStore($store_id);

if ($dataT !== false && $dataT['tpl'] !== false){
?>
<div id="data-collect-overlay" class="pp-overlay"></div>
<div id="data-collect" style='display:none;' class='pp <?= $tablet ? " tablet" : ""; ?>' data-location='<?= $dataT['loc']; ?>'>
	<div class='content'><?= $dataT['tpl']; ?></div>
</div>
<?php
}
?>