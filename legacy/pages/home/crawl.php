<?php
$this->IsScript = true;

$websiteURL = trim(Brevada::FromPOSTGET('website'));

$data = array();

if(!empty($websiteURL)){
	if(stripos($websiteURL, 'http://') !== 0){
		$websiteURL = 'http://'.$websiteURL;
	}
}

if(isset($_SESSION['LastCrawlTime']) || empty($websiteURL) || !filter_var($websiteURL, FILTER_VALIDATE_URL)){
	$data['error'] = 1;
} else {	
	$ch = @curl_init($websiteURL);

	if($ch !== false){
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, "spider");

		$raw = curl_exec($ch);
		curl_close($ch);

		if($raw === false){
			$data['error'] = 2;
			unset($_SESSION['LastCrawlTime']);
		} else {
			//$_SESSION['LastCrawlTime'] = time();
			
			$dom = new DOMDocument();
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			libxml_use_internal_errors(true);
			$dom->loadHTML(strtolower($raw));

			$xpath = new DOMXPath($dom);
			
			/* Retrieve website's meta data to scan for keywords. */
			
			$metaData = '';
			
			$meta = $xpath->query('//meta[contains(@name, "description")]');
			if($meta->length > 0){
				$metaData .= $meta->item(0)->getAttribute('content');
			}
			
			$meta = $xpath->query('//meta[contains(@name, "keywords")]');
			if($meta->length > 0){
				$metaData .= ' ' . $meta->item(0)->getAttribute('content');
			}
			
			$meta = $xpath->query('//meta[contains(@name, "keywords")]');
			if($meta->length > 0){
				$metaData .= ' ' . $meta->item(0)->getAttribute('content');
			}
			
			$categoryID = 1;
			
			$categoryMatches = array();
			if(($query = Database::query("SELECT `id`, `Title` FROM `company_categories`")) !== false){
				while($row = $query->fetch_assoc()){
					$id = $row['id'];
					$title = strtolower($row['Title']);
					
					if(preg_match("/\b".preg_quote($title)."\b/i", $metaData)){
						if(isset($categoryMatches[$title])){
							$categoryMatches[$title]['count']++;
						} else {
							$categoryMatches[$title] = array('id' => $id, 'count' => 1);
						}
					}
				}
			}
			
			$categoryName = 'Restaurant';
			
			if(!empty($categoryMatches)){
				$counts = array();
				foreach($categoryMatches as $key => $row){
					$counts[$key] = $row['count'];
				}
				
				array_multisort($counts, SORT_DESC, $categoryMatches);
				
				$keys = array_keys($categoryMatches);
				$categoryID = @intval($categoryMatches[$keys[0]]);
				$categoryName = ucwords($keys[0]);
			}
			
			$keywordIDMatches = array();
			if(($query = Database::query("SELECT `id`, `Title`, `Aliases` FROM `company_keywords` WHERE `CategoryID` = {$categoryID}")) !== false){
				while($row = $query->fetch_assoc()){
					$id = $row['id'];
					$title = $row['Title'];
					$aliases = empty($row['Aliases']) ? array() : explode(',', strtolower($row['Aliases']));
					
					$aliases[] = strtolower($title);
					
					foreach($aliases as $alias){
						$alias = trim($alias);
						if(preg_match("/\b".preg_quote($alias)."\b/i", $metaData)){
							$keywordIDMatches[] = $id;
						}
					}
				}
			}
			
			$keywordIDMatches = array_unique($keywordIDMatches, SORT_NUMERIC);
			
			/* Retrieve additional website data for later use (in the future). */
			
			$ogImage = '';
			
			$meta = $xpath->query('//meta[contains(@property, "og:image")]');
			if($meta->length > 0){
				$ogImage = $meta->item(0)->getAttribute('content');
			}
			
			if(empty($ogImage)){
				$meta = $xpath->query('//meta[contains(@property, "twitter:image")]');
				if($meta->length > 0){
					$ogImage = $meta->item(0)->getAttribute('content');
				}
			}
			
			$data['category'] = array('id' => $categoryID, 'title' => __($categoryName));
			$data['keywords'] = $keywordIDMatches;
			$data['image'] = $ogImage;
		
		}
	} else { $data['error'] = 3; }
}

header('Content-Type: application/json');
echo json_encode($data);
?>