<?php
class TaskResources extends AbstractTask
{
	public function execute($method, $tasks, &$data)
	{
		if($method == 'get'){
			if(TaskLoader::requiresData(['serial'], $_GET)){
				$serial = $_GET['serial'];
				
				$url = URL;
				
				$data['download'] = array();
				$data['download'][] = array('name' => 'layout.css', 'url' => 'css/layout.css', 'role' => 'styles');
				$data['download'][] = array('name' => 'profile_header.css', 'url' => 'css/profile_header.css', 'role' => 'styles');
				$data['download'][] = array('name' => 'post_box.css', 'url' => 'css/post_box.css', 'role' => 'styles');
				$data['download'][] = array('name' => 'star_rating_bar.css', 'url' => 'css/star_rating_bar.css', 'role' => 'styles');
				$data['download'][] = array('name' => 'tablet.js', 'url' => 'js/tablet.js', 'role' => 'script');
				$data['download'][] = array('name' => 'profile.css', 'url' => 'css/profile.css', 'role' => 'styles');
				$data['download'][] = array('name' => 'profile.html', 'url' => 'api/profile?m=' . $serial, 'role' => 'markup');
				
				foreach($data['download'] as &$dl){
					$dl['url'] = $url . $dl['url'];
					$dl['sha1'] = strtoupper(sha1_file($dl['url']));
				}
				unset($dl);
			} else {
				throw new Exception("Incomplete request.");
			}
		}
	}
}
?>