<?php
class CommentStandard{
	/*
	 * Write something about this variable. The reason why We created that (purpose) <= Định nghĩa mục đích của biến này
	 *
	 * @param String <= Kiểu của
 	 */
	protected $userName;

	/*
	 * Write something about this variable. The reason why We created that (purpose)
	 *
	 * @var Array
	 */
	protected $aUsername;

	/**
	 * Create a new contextual binding builder <= Định nghĩa mục đích của hàm này
	 *
	 * @param  Libs\Container  $container <= Chỉ rõ Container từ đâu
	 * @param  string|array  $concrete <= Chỉ rõ kiểu dữ liệu của parameter này
 	 * @return void <= Phương thức này trả về gì Nếu không trả về bất kì giá trị gì thì là void. Trả về số là number
	 * Trả về true|false là bool
	 */
	public function __construct(Container $container, $concrete)
	{
		$this->concrete = $concrete;
		$this->container = $container;
	}

	/**
	 * Define the abstract target that depends on the context.
	 *
	 * @param  string  $abstract
	 * @return $this <= Phương thức này trả về gì
	 */
	public function needs($abstract)
	{
		$this->needs = $abstract;

		return $this;
	}

	/**
	 * Define the abstract target that depends on the context.
	 *
	 * @param  string  $abstract
	 * @return string <= Phương thức này trả về gì
	 */
	public function returnNumber($number)
	{
		return $number;
	}

	/**
	 * Define the abstract target that depends on the context.
	 *
	 * @param  string  $abstract
	 * @return bool <= Phương thức này trả về gì
	 */
	public function returnBool($number)
	{
		return $number;
	}

}