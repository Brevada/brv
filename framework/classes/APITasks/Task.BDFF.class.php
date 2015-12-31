<?php
class TaskBDFF extends AbstractTask
{
	public function execute($method, $tasks, &$data)
	{
		if($method == 'get'){
			if(TaskLoader::requiresData(['localtime'], $_GET)){
				
			} else {
				throw new Exception("Incomplete request.");
			}
		}
	}
}
?>