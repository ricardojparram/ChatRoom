<?php 
	
	namespace controller;

	class frontController{

		private $url;

		public function __construct($request){

			if(isset($request['module'])){
				$this->url = $request['module'];
				$this->reDirection();
			}else{
				header('Location: ?module=login');
			}
		}

		private function reDirection(){
			$file = "src/controller/{$this->url}Controller.php";
			if(file_exists($file)){
				require_once($file);
			}else{
				die('Controller doesnt exists.');
			}
		} 

	}