<?php
$this->add(new View('../template/main_header.php'));
$this->addResource('/css/layout.css');
$this->addResource('/css/404.css');
?>

<div class='container'>

	<div class='section1'>
		<p>Sorry,<br/><br />The page you are looking for doesn't exist.</p>
		<p><br />- Brevada</p>
	</div>

</div>

<?php $this->add(new View('../template/footer.php')); ?>