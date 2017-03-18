<?php
class TaskSupport extends AbstractTask
{
	private $data;
	
	public function execute($method, $tasks, &$data)
	{
		if($method == 'get'){
			if(!TaskLoader::requiresData(['localtime'], $_GET)){
				throw new Exception("Incomplete request.");
			}
		}
		if(!Brevada::IsLoggedIn()){
			throw new Exception("Authentication required.");
		}
		$this->data = &$data;
	}
	
	public function taskOpen(){
		$accountID = $_SESSION['AccountID'];
		$company = $_SESSION['CompanyID'];
		$message = Brevada::FromPOST('message');
		
		if(empty($message)){
			throw new Exception('Support message cannot be empty.');
		}
		
		if(strlen($message) > 1000){
			throw new Exception('Support message cannot be greater than 1000 characters.');
		}
		
		$insert_id = -1;
		if(($stmt = Database::prepare("INSERT INTO `support` (`AccountID`, `Date`, `Message`) VALUES (?, NOW(), ?)")) !== false){
			$stmt->bind_param('is', $accountID, $message);
			if(!$stmt->execute()){
				$stmt->close();
				throw new Exception("Unknown error.");
			} else {
				$insert_id = $stmt->insert_id;
				$stmt->close();
			}
		}
		
		$company_name = ''; $company_phone = '';
		$first_name = ''; $email = '';
		
		if(($stmt = Database::prepare("
			SELECT companies.`Name`, companies.`PhoneNumber`, `accounts`.`FirstName`, `accounts`.`EmailAddress` FROM `companies` JOIN `accounts` ON `accounts`.`CompanyID` = `companies`.`id` WHERE companies.`id` = ? AND `accounts`.`id` = ? LIMIT 1")) !== false){
			$stmt->bind_param('ii', $company, $accountID);
			if($stmt->execute()){
				$stmt->bind_result($company_name, $company_phone, $first_name, $email);
				$stmt->fetch();
			}
			$stmt->close();
		}
		
		$encoded = htmlentities($message);
		
		$fields = [];
		
		$fields[] = [ 'title' => 'Company', 'value' => $company_name, 'short' => false ];
		if(!empty($first_name)){
			$fields[] = [ 'title' => 'Name', 'value' => $first_name, 'short' => true ];
		}
		if(!empty($company_phone)){
			$fields[] = [ 'title' => 'Phone #', 'value' => $company_phone, 'short' => true ];
		}
		if(!empty($email)){
			$fields[] = [ 'title' => 'Email', 'value' => $email, 'short' => true ];
		}
		
		$fields[] = [ 'title' => 'Message', 'value' => $encoded, 'short' => false ];
		
		Slack::send([
			'username' => 'BrevadaSupport',
			'channel' => '#support',
			'attachments' => [
				[
					'fallback' => "New support ticket: <".URL."admin?show=support&id={$insert_id}|View in Browser>",
					'pretext' => "New support ticket: <".URL."admin?show=support&id={$insert_id}|View in Browser>",
					'color' => '#FF2B2B',
					'fields' => $fields
				]
			]
		]);
	}
	
	public function taskReply(){
		$accountID = $_SESSION['AccountID'];
		
		$message = Brevada::FromPOST('message');
		$supportID = Brevada::FromPOST('sid');
		
		if(!Permissions::has(Permissions::VIEW_ADMIN)){
			throw new Exception('Invalid authentication.');
		}
		
		if(empty($message)){
			throw new Exception('Support message cannot be empty.');
		}
		
		if(strlen($message) > 1000){
			throw new Exception('Support message cannot be greater than 1000 characters.');
		}
		
		if(strtolower($message) == '/closed'){
			if(($stmt = Database::prepare("UPDATE `support` SET `support`.`Resolved` = 1 WHERE `support`.`id` = ?")) !== false){
				$stmt->bind_param('i', $supportID);
				if(!$stmt->execute()){
					$stmt->close();
					throw new Exception("Unknown error.");
				}
				$stmt->close();
			}
			if(($stmt = Database::prepare("INSERT INTO `support_responses` (`SupportID`, `AccountID`, `Date`, `Message`) VALUES (?, ?, NOW(), ?)")) !== false){
				$message = 'The support ticket has been closed.';
				$stmt->bind_param('iis', $supportID, $accountID, $message);
				if(!$stmt->execute()){
					$stmt->close();
					throw new Exception("Unknown error.");
				}
				$stmt->close();
			}
		} else {
			if(($stmt = Database::prepare("INSERT INTO `support_responses` (`SupportID`, `AccountID`, `Date`, `Message`) VALUES (?, ?, NOW(), ?)")) !== false){
				$stmt->bind_param('iis', $supportID, $accountID, $message);
				if(!$stmt->execute()){
					$stmt->close();
					throw new Exception("Unknown error.");
				}
				$stmt->close();
			}
		}
	}
}
?>