<?php
$q = Brevada::validate($_GET['q'], VALIDATE_DATABASE);

# Collect the results
$arr = array();

$query = Database::query("SELECT DISTINCT `name` FROM `posts` WHERE `name` IN (SELECT `name` FROM `posts` WHERE `name` LIKE '{$q}%' GROUP BY `name` HAVING COUNT(*) > 1) ORDER BY `name` ASC LIMIT 10");

$names = array();
$names[] = $q;

$arr[] = array('id' => $q, 'name' => $q);

while($row = $query->fetch_assoc()){
	if(!in_array($row['name'], $names)){
		$names[] = $row['name'];
		$arr[] = array('id' => $row['name'], 'name' => $row['name']);
	}
}

# JSON-encode the response
$json_response = json_encode($arr);

# Optionally: Wrap the response in a callback function for JSONP cross-domain support
if($_GET["callback"]) {
    $json_response = $_GET["callback"] . "(" . $json_response . ")";
}

# Return the response
echo $json_response;
exit;
?>