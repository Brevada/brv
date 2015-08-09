<?php
$this->IsScript = true;
$then = microtime(true);
/* Execute twice just in case there's not enough data for comparisons (bug). */
BrevadaData::execute_analysis();
BrevadaData::execute_analysis();
echo "It took " . (microtime(true) - $then) . " seconds to analyze the data.";
?>