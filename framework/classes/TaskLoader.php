<?php
class TaskLoader
{
	public static function load($task)
	{
		$tasks = explode('/', $task);
		$task = ucwords($tasks[0]);
		$taskClassPath = realpath(__DIR__ . "/APITasks/Task.{$task}.class.php");
		if(strpos($taskClassPath, realpath(__DIR__ . "/APITasks/")) !== 0 || !file_exists($taskClassPath)){
			throw new Exception('Invalid API task.');
		} else {
			require_once $taskClassPath;
			$taskClass = "Task{$task}";
			return new $taskClass;
		}
	}
	
	public static function requiresData($needles, $hay)
	{
		return count(array_diff_key($needles, $hay)) == 0;
	}
}

abstract class AbstractTask
{
	abstract protected function execute($method, $tasks, &$data);
}
?>