<?php
class Slack
{
	const WEBHOOK_URL = 'https://hooks.slack.com/services/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';
	
	public static function send($json)
	{
		$ch = curl_init(self::WEBHOOK_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('payload' => json_encode($json)));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		if($result === false){
			Logger::info("Slack failed: " . curl_error($ch));
		} else {
			Logger::info("Slack: Sent data: ".json_encode($json).". Received: " . (strlen($result) > 300 ? substr($result, 0, 300).'...' : $result));
		}
		curl_close($ch);
		
		return $result;
	}
}
?>