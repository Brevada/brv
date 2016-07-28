<?php
$store_id = $this->getParameter('store_id');
$store_id = @intval($store_id);

$tablet = $this->getParameter('tablet') === true;

if (($stmt = Database::prepare("
	SELECT `CollectionTemplate`, `CollectionLocation`
	FROM store_features
	JOIN stores ON stores.FeaturesID = store_features.id
	WHERE stores.id = ?
")) !== false){
	$stmt->bind_param('i', $store_id);
	if ($stmt->execute()){
		$stmt->store_result();
		if ($stmt->num_rows > 0){
			$stmt->bind_result($col_template, $col_location);
			$stmt->fetch();
			// Render data form.
?>
<div id="data-collect-overlay"></div>
<div id="data-collect" style='display:none;' <?= $tablet ? "class='tablet'" : ""; ?> data-location='<?= $col_location; ?>'>
	<div class='content'>
		<?php
			$dataT = DataTemplate::fromJSON($col_template);
			if($dataT !== false){
				echo $dataT;
			}
		?>
	</div>
</div>
<?php
		}
	}
	$stmt->close();
}
?>