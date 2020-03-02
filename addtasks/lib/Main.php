<?php
namespace AddTasks;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\HttpClient;
class Main {

	public static function GetTasks(){
		
		$module_id = pathinfo(dirname(__DIR__))['basename'];
		
		$options = array(
			'url'	=>	Option::get($module_id, 'url','http://dev-zone-srv.ru/'),
			'login'	=>	Option::get($module_id, 'login','admin'),
			'password'	=>	Option::get($module_id, 'password','admin')
		);
		
		$httpClient = new HttpClient();
		$httpClient->setHeader('Content-Type', 'application/json', true);
		$response = $httpClient->post(
			$options['url'],
			json_encode(
				array(
					'login' =>  $options['login'],
					'password' =>  $options['password']
					)
			)
		);
		
		$response = json_decode($response);
		
		if(is_array($response)):
			foreach($response as $k => $task):
				self::addTask($task);
			endforeach;
		endif;
	
		return "\addtasks\Main::GetTasks();";
	}
	
	public static function addTask($task=''){

		if (\CModule::IncludeModule("tasks")&&is_object($task))
		{
			$arFields = Array(
				"TITLE" => $task->TITLE,
				"DESCRIPTION" => $task->DESCRIPTION,
				"RESPONSIBLE_ID" => $task->RESPONSIBLE_ID,
				"PRIORITY" => $task->PRIORITY
			);

			$obTask = new \CTasks;
			$ID = $obTask->Add($arFields);
		}

	}

}