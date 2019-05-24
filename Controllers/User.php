<?php
class User extends Controller{
	public function __construct(){
		parent::__construct();
		Session::init();
	}

	public function printLoginScripts(){
	}

	public function login(){
		if(Session::get('loggedIn')==true){
			$this->redirect('group','index');
		}
		if(isset($_POST['submit'])){
			//$source=array{username='Phuc'}
			$source 	= array('username' => $_POST['username']);
			//username=Phuc
			$username 	= $_POST['username'];
			// Mã hóa dữ liệu theo kiểu MD5
			$password 	= md5($_POST['password']);
			$validate = new Validate($source);
			//Nhảy đến phương thức addRule class Models

			$validate->addRule('username', 'existRecord|required');
			$validate->addRule('password', 'required');

			//Phương thức run() class Validate
			$isValid = $validate->run()->isValid();

			if ( !$isValid ){
				$this->view->errors = $validate->showErrors();
			}

			if ( UserModel::checkLogin($username, $password) ){
				$this->view->render('user/login');
			}

			return 'Error';
		}
	}

	public function logout(){
		$this->view->render('user/logout');
		Session::destroy();
	}
}