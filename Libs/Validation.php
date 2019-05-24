<?php
class Validate{
	// Error array
	private $errors	= array();
	// Source array
	private $source	= array();
	// Rules array
	private $rules	= array();
	// Result array
	private $result	= array();
	// Contrucst
	public function __construct($source){
		//$source=array(username=>Phuc)
		$this->source = $source;
	}
	// Add rules
	public function addRules($rules){
		$this->rules = array_merge($rules, $this->rules );
	}
	// Get Err
	public function getError(){
		return $this->errors;
	}
	// Set Err
	public function setError($element, $message){
		if(array_key_exists($element, $this->errors)){
			$this->errors[$element] .= ' - ' . $message;
		}else{
			$this->errors[$element] = '<b>' . ucfirst($element) . ':</b> ' . $message;
		}
	}
	// Get result
	public function getResult(){
		return $this->result;
	}
	// Add rule
	/*
	 $element=username
	 $type=existRecord
	$option={
	Database=
	}
	 */
	public function addRule($element, $type, $options = null, $required = true){
		$this->rules[$element] = array('type' => $type, 'options' => $options, 'required' => $required);
		/*Trả về mảng:
		{
            errors
            source :done
            rules:done
            result
		}
		*/
		return $this;
	}
	// Run
	public function run(){
		/*Duyệt qua các phần tử mảng:
		$this->rules:tên mảng
		$element:Chỉ số của mảng
		$value:Giá trị
		*/
		foreach($this->rules as $element => $value){
			//$element =username
			//$value=arr();
			//Kiểm tra $value['required'] == true;trim($this->source[$element])='Phuc'
			if($value['required'] == true && trim($this->source[$element])==null){
				$this->setError($element, 'is not empty');
			}else{
				// Nen cho add multiple-types
				$aParseType = explode('|', $value['type']);
				foreach ($aParseType  as $type){
					switch ($type) {//existRecord
						case 'int':
							$this->validateInt($element, $value['options']['min'], $value['options']['max']);
							break;
						case 'string':
							$this->validateString($element, $value['options']['min'], $value['options']['max']);
							break;
						case 'url':
							$this->validateUrl($element);
							break;
						case 'email':
							$this->validateEmail($element);
							break;
						case 'status':
							$this->validateStatus($element);
							break;
						case 'group':
							$this->validateGroupID($element);
							break;
						case 'password':
							$this->validatePassword($element, $value['options']);
							break;
						case 'date':
							$this->validateDate($element, $value['options']['start'], $value['options']['end']);
							break;
						case 'existRecord':
							//Nhảy đến phương thức validateExistRecord
							//$element=username, $value['options']=arr[2]
							$this->validateExistRecord($element, $value['options']);
							break;
						case 'file':
							$this->validateFile($element, $value['options']);
							break;
					}
				}
			}
			//$element=username, this->errors=null
			//Kiểm tra có key $element trong arr $this->errors
			if(!array_key_exists($element, $this->errors)) {//==1
				//result=arr(username=>Phuc){1}
				$this->result[$element] = $this->source[$element];
			}
		}
		//Lấy các phần tử không giống nhau của mảng source và mảng errors
		//eleNotValidate=arr(username=>Phuc){1}
		$eleNotValidate = array_diff_key($this->source, $this->errors);
		//Nối mảng
		//$this->result=arr(username=>Phuc){1}
		$this->result	= array_merge($this->result, $eleNotValidate);

		// Nen them return $this o day
		return $this;
	}
	// Validate Integer
	private function validateInt($element, $min = 0, $max = 0){
		if(!filter_var($this->source[$element], FILTER_VALIDATE_INT, array("options"=>array("min_range"=>$min,"max_range"=>$max)))){
			$this->setError($element, 'is an invalid number');
		}
	}
	// Validate String
	private function validateString($element, $min = 0, $max = 0){
		$length = strlen($this->source[$element]);
		if($length < $min) {
			$this->setError($element, 'is too short');
		}elseif($length > $max){
			$this->setError($element, 'is too long');
		}elseif(!is_string($this->source[$element])){
			$this->setError($element, 'is an invalid string');
		}
	}
	// Validate URL
	private function validateURL($element){
		if(!filter_var($this->source[$element], FILTER_VALIDATE_URL)){
			$this->setError($element, 'is an invalid url');
		}
	}
	// Validate Email
	private function validateEmail($element){
		if(!filter_var($this->source[$element], FILTER_VALIDATE_EMAIL)){
			$this->setError($element, 'is an invalid email');
		}
	}
	public function showErrors(){
		$xhtml = '';
		if(!empty($this->errors)){
			$xhtml .= '<ul class="Err">';
			foreach($this->errors as $key => $value){
				$xhtml .= '<li>'.$value.' </li>';
			}
			$xhtml .=  '</ul>';
		}
		return $xhtml;
	}
	public function isValid(){
		if(count($this->errors)>0) return false;
		return true;
	}
	// Validate Status
	private function validateStatus($element){
		if($this->source[$element] < 0 || $this->source[$element] > 1){
			$this->setError($element, 'Select status');
		}
	}
	// Validate GroupID
	private function validateGroupID($element){
		if($this->source[$element] == 0){
			$this->setError($element, 'Select group');
		}
	}
	// Validate Password
	private function validatePassword($element, $options){
		if($options['action'] == 'add' || ($options['action'] == 'edit' && $this->source[$element] )){
			$pattern = '#^(?=.*\d)(?=.*[A-Z])(?=.*\W).{8,8}$#';	// Php4567!
			if(!preg_match($pattern, $this->source[$element])){
				$this->setError($element, 'is an invalid password');
			};
		}

	}
	// Validate Date
	private function validateDate($element, $start, $end){
		// Start
		$arrDateStart 	= date_parse_from_format('d/m/Y', $start) ;
		$tsStart		= mktime(0, 0, 0, $arrDateStart['month'], $arrDateStart['day'], $arrDateStart['year']);
		// End
		$arrDateEnd 	= date_parse_from_format('d/m/Y', $end) ;
		$tsEnd			= mktime(0, 0, 0, $arrDateEnd['month'], $arrDateEnd['day'], $arrDateEnd['year']);
		// Current
		$arrDateCurrent	= date_parse_from_format('d/m/Y', $this->source[$element]) ;
		$tsCurrent		= mktime(0, 0, 0, $arrDateCurrent['month'], $arrDateCurrent['day'], $arrDateCurrent['year']);

		if($tsCurrent < $tsStart || $tsCurrent > $tsEnd){
			$this->setError($element, 'is an invalid date');
		}
	}
	// Validate Exist record
	private function validateExistRecord($element, $options){
		/*
		 $Database={User_Model}[4]
		*/
		$database = $options['database'];//$Database có kiểu Object
		//$query=SELECT id FROM user WHERE username = 'Phuc' AND password= 'dcf3be759d4ba999ca26e9583308a969'
		$query	  = $options['query'];
		if($database->isExist($query)==false){//==0
			$this->setError($element, 'record is not exist');
		}
	}
	// Validate File
	private function validateFile($element, $options){
		if(!filter_var($this->source[$element]['size'], FILTER_VALIDATE_INT, array("options"=>array("min_range"=>$options['min'],"max_range"=>$options['max'])))){
			$this->setError($element, 'kích thước không phù hợp');
		}
		$ext = pathinfo($this->source[$element]['name'], PATHINFO_EXTENSION);
		if(in_array($ext, $options['entension']) == false){
			$this->setError($element, 'phần mở rộng không phù hợp');
		}
	}
}