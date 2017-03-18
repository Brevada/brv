<?php
$this->IsScript = true;

$category = @intval(Brevada::FromPOST('category'));

if($category <= 0){ exit("<p>Sorry, we've encountered an error.</p>"); }

if(($query = Database::query("SELECT company_keywords.Title, company_keywords.id as CompanyKeywordID FROM company_keywords WHERE company_keywords.CategoryID = {$category} ORDER BY company_keywords.Title ASC")) !== false){
	while($row = $query->fetch_assoc()){
		echo "<div class='token noselect' data-tokenid='{$row['CompanyKeywordID']}'><span>".__($row['Title'])."</span></div>";
	}
}
?>
<input type='hidden' name='tokensKeywords' class='token-input' />