<?php
$this->IsScript = true;
//if(!Brevada::IsLocal()){ exit('Invalid request.'); }
$then = microtime(true);
BrevadaData::execute_analysis();
echo "It took " . (microtime(true) - $then) . " seconds to analyze the data.";
?>