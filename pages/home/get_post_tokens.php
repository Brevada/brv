<?php
$q = Brevada::validate($_GET['q'], VALIDATE_DATABASE);

# Collect the results
$arr = array();

$query = Database::query("SELECT DISTINCT `name` FROM `posts` WHERE `name` LIKE '{$q}%' AND `name` IN (SELECT `name` FROM `posts` WHERE `name` LIKE '{$q}%' GROUP BY `name` HAVING COUNT(*) > 1) ORDER BY `name` ASC LIMIT 10");

//If dedicated table (of table name 'post_tokens' and table column 'name') exists:
// $query = Database::query("SELECT `name` FROM `post_tokens` WHERE `name` LIKE '{q}%' ORDER BY `name` ASC LIMIT 10");

$names = array();
$names[] = $q;

$arr[] = array('id' => $q, 'name' => ucwords($q));

while($row = $query->fetch_assoc()){
	$nm = ucwords($row['name']);
	if(!in_array($nm, $names)){
		$names[] = $nm;
		$arr[] = array('id' => $nm, 'name' => $nm);
	}
}

# JSON-encode the response
$json_response = json_encode($arr);

# Optionally: Wrap the response in a callback function for JSONP cross-domain support
if(!empty($_GET["callback"])) {
    $json_response = $_GET["callback"] . "(" . $json_response . ")";
}

# Return the response
echo $json_response;
exit;
?>