<?php
$this->IsScript = true;
$then = microtime(true);
BrevadaData::execute_analysis();
echo "It took " . (microtime(true) - $then) . " seconds to analyze the data.";
?>