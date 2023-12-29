<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'services/SanitationService.php');

class Api extends CI_Controller
{
	private $response;
	public $sanitationService;

	public function __construct()
	{
		parent::__construct();

		date_default_timezone_set('Asia/Manila');

		header("Content-Type: application/json");

		if (isset($_SESSION['user_id'])) {
			redirect('nav');
		}

		$this->sanitationService = new SanitationService();
	}

	private function check_parameters($params)
	{
		$optionals = array('facebook_link');

		foreach ($params as $key => $value) {
			if (!in_array($key, $optionals)) {
				if ($value == '') {
					return false;
				}
			}
		}

		return true;
	}

	private function upload($files)
	{
		$file = $files['file'];

		// print_r($file);
		// exit();

		// Check for errors
		// if ($file['error'] == true) {
			// Retrieve the temporary file location
			$tmpFilePath = $file['tmp_name'];

			// Choose a destination directory
			$uploadDir = $files['file_path'];

			// Generate a unique filename
			$fileName = $file['name'];
			$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
			$fileName = uniqid() . '_' . uniqid() . '_' .uniqid() . '.' . $fileExtension;

			// Get the file extension
			// $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
			

			// Define the allowed image file extensions
			$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];

			// Define the maximum allowed file size in bytes (e.g., 2MB = 2 * 1024 * 1024 bytes)
			$max_mb = 15;
			$maxFileSize = $max_mb * 1024 * 1024; // 2MB

			// Check if the file extension is valid
			// COMMENTED DUE TO NOT ACCEPTING
			if (in_array($fileExtension, $allowedExtensions)) {
				// Check if the file size is within the allowed limit
				if ($file['size'] <= $maxFileSize) {

					// Construct the full path to the destination file
					// $destination = $_SERVER['DOCUMENT_ROOT'] . '/' . $uploadDir . $fileName;

					/* 100% working upload */
					$allowed_types = 'jpg|jpeg|gif|png|bmp|tif|tiff|JPG|JPEG|GIF|PNG|BMP|TIF|TIFF';

					$config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '/' . $uploadDir;

					// $message =  $config['upload_path'];
					// return array(
					// 	'result' => false,
					// 	'response_code' => 500,
					// 	'message' => $message
					// );
					// exit();

					$config['allowed_types'] = $allowed_types;
					// IN KILOBYTE
					$config['max_size']  = $maxFileSize; // 25mb
					$config['file_name'] = $fileName;
					$this->load->library('upload',$config,'ValidID');
					$this->ValidID->initialize($config);

					if(!$this->ValidID->do_upload('valid_id'))
					{
						// $error = array('error' => $this->upload->display_errors());
						// print_r($error['error']);
						// var_dump($_FILES['Add_Image']);

						return array(
							'result' => false,
							'response_code' => 500,
							'message' => 'Internal Server Error. Error uploading file. '.$this->upload->display_errors()
						);
					}

					else
					{
						return array(
							'result' => true,
							'message' => '',
							'data' => array(
								'file_name' => $fileName,
								'file_extension' => $fileExtension
							)
						);
					}
					 /* end of 100% guaranteed working upload*/

				} else {
					// File size exceeds the allowed limit
					// echo 'File size exceeds the allowed limit. Maximum file size allowed: ' . $max_mb . 'MB';
					return array('result' => false, 'message' => 'File size exceeds the allowed limit. Maximum file size allowed: ' . $max_mb . 'MB');
				}
			} else {
				// Invalid file type
				// echo 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.';
				return array('result' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed. FILE TYPE: ');
			}
	// 	} else {
	// 		// Error occurred during file upload
	// 		$uploadErrors = array(
	// 				UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
	// 				UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
	// 				UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
	// 				UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
	// 				UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder. Introduced in PHP 5.0.3.',
	// 				UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk. Introduced in PHP 5.1.0.',
	// 				UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
	// 		);
	
	// 		$errorMessage = isset($uploadErrors[$file['error']]) ? $uploadErrors[$file['error']] : 'Unknown error occurred during file upload.';
	
	// 		return array('result' => false, 'message' => $errorMessage);
	// }
	}

	public function register()
	{
		try {
			// CHECK REQUEST METHOD
			if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
				header("HTTP/1.1 405 Method Not Allowed");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '405';
				$this->response['message'] = 'Method Not Allowed';
				$this->response['allow'] = array('POST');

				echo json_encode($this->response);
				exit();
			}

			// CHECK PARAMETERS
			try {
				$params = array(
					'first_name' => $_POST['first_name'],
					'middle_name' => $_POST['middle_name'],
					'last_name' => $_POST['last_name'],
					'mobile_number' => $_POST['mobile_number'],
					'permanent_address' => $_POST['permanent_address'],
					'birth_date' => $_POST['birth_date'],
					'place_of_birth' => $_POST['place_of_birth'],
					'nationality' => $_POST['nationality'],
					'nature_of_work' => $_POST['nature_of_work'],
					'source_of_income' => $_POST['source_of_income'],
					'facebook_link' => $_POST['facebook_link'],
					'username' => $_POST['username'],
					'password' => $_POST['password'],
					'email_address' => $_POST['email_address'],
					'type_of_payment' => $_POST['type_of_payment'],
					'account_number' => $_POST['account_number'],
					'account_name' => $_POST['account_name'],
					'secret_question' => $_POST['secret_question'],
					'secret_answer' => $_POST['secret_answer'],
					'files' => array(
						'id_code' => $_POST['id_code'],
						'file_name' => $_POST['valid_id_name'],
						'file' => $_FILES['valid_id'],
						'file_path' => 'assets/uploads/user_valid_ids/'
					)
				);
			} catch (Exception $e) {
				header("HTTP/1.1 400 Bad Request");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '201';
				$this->response['message'] = 'Incomplete Parameter.';

				echo json_encode($this->response);
				exit();
			}

			$check_parameters = $this->check_parameters($params);

			if (!$check_parameters) {
				header("HTTP/1.1 400 Bad Request");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '201';
				$this->response['message'] = 'Incomplete Parameter.';

				echo json_encode($this->response);
				exit();
			}

			$params = $this->sanitationService->sanitize($params);

			// CHECK MOBILE NUMBER
			if (strlen($params['mobile_number']) != 11) {
				header("HTTP/1.1 400 Bad Request");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '400';
				$this->response['message'] = 'Mobile Number must me 10 digits.';

				echo json_encode($this->response);
				exit();
			}

			// CHECK DUPLICATE MOBILE NUMBER
			$check_duplicate_mobile = $this->db->get_where('users', ['mobile_number' => $params['mobile_number']]);

			if ($check_duplicate_mobile->num_rows() != null) {
				header("HTTP/1.1 400 Bad Request");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '400';
				$this->response['message'] = 'Mobile Number already exist.';

				echo json_encode($this->response);
				exit();
			}

			// CHECK PASSWORD
			if (strlen($params['password']) < 6) {
				header("HTTP/1.1 400 Bad Request");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '400';
				$this->response['message'] = 'Minimum password characters is 6.';

				echo json_encode($this->response);
				exit();
			}

			// CHECK DATE OF BIRTH
			$dateTime = new DateTime($params['birth_date']);

			$twentyOneYearsAgo = new DateTime('-21 years');

			if (!($dateTime <= $twentyOneYearsAgo)) {
				header("HTTP/1.1 400 Bad Request");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '400';
				$this->response['message'] = 'Age must be 21 or above.';

				echo json_encode($this->response);
				exit();
			}

			// CHECK VALID ID DROP DOWN
			$check_valid_ids = $this->db->get_where('valid_ids', ['id_code' => $params['files']['id_code']]);

			if ($check_valid_ids->num_rows() <= 0) {
				header("HTTP/1.1 400 Bad Request");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '400';
				$this->response['message'] = 'Valid ID is not Allowed.';

				echo json_encode($this->response);
				exit();
			}

			// UPLOAD VALID ID
			$valid_id_upload = $this->upload($params['files']);

			if ($valid_id_upload['result'] == false) 
			{
				if(isset($valid_id_upload['response_code'])) 
				{
					header("HTTP/1.1 500 Internal Server Error");
					$response_code = $valid_id_upload['response_code'];
				} 

				else 
				{
					header("HTTP/1.1 400 Bad Request");
					$response_code = '400';
				}

				$this->response['status'] = 'failed';
				$this->response['response_code'] = $response_code;
				$this->response['message'] = $valid_id_upload['message'];

				echo json_encode($this->response);
				exit();
			}

			// ALL CLEAR
			$this->load->model('Api_Model');
			$register_user = $this->Api_Model->insert_user_registration($params);

			if (!$register_user['result']) {
				header("HTTP/1.1 500 Internal Server Error");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '204';
				$this->response['message'] = 'Unexpected Error';

				echo json_encode($this->response);
				exit();
			}

			// RECORD FILE DATA
			$params['files']['user_id'] = $register_user['data']['user_id'];
			$params['files']['remarks'] = 'User Valid ID Upload';
			$params['files']['file_path'] =  base_url().'assets/uploads/user_valid_ids/'.$valid_id_upload['data']['file_name'];
			$params['files']['file_extension'] =  $valid_id_upload['data']['file_extension'];

			$save_uploads = $this->Api_Model->insert_user_upload($params);

			if (!$save_uploads['result']) {
				header("HTTP/1.1 500 Internal Server Error");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '204';
				$this->response['message'] = 'Unexpected Error. '.$save_uploads['message'];
				

				echo json_encode($this->response);
				exit();
			}

			// SAVE USER SECRET
			$user_secret = $this->Api_Model->insert_user_secret($params);

			if (!$user_secret['result']) {
				header("HTTP/1.1 500 Internal Server Error");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '204';
				$this->response['message'] = 'Unexpected Error. '.$user_secret['message'];
				

				echo json_encode($this->response);
				exit();
			}

			// SAVE USER TYPE OF PAYMENT
			$user_secret = $this->Api_Model->insert_user_type_of_payment($params);

			if (!$user_secret['result']) {
				header("HTTP/1.1 500 Internal Server Error");

				$this->response['status'] = 'failed';
				$this->response['response_code'] = '204';
				$this->response['message'] = 'Unexpected Error. '.$user_secret['message'];
				

				echo json_encode($this->response);
				exit();
			}

			// SET SESSION
			$user_data = array(
				'user_id' => $register_user['data']['user_id'],
				'user_type_id' => $register_user['data']['user_type_id']
			);

			$this->session->set_userdata($user_data);

			header("HTTP/1.1 201 Created");

			$this->response['status'] = 'success';
			$this->response['response_code'] = '201';
			$this->response['message'] = 'Account Registered! Page will redirect into the chat room, please wait.';

			echo json_encode($this->response);
		} catch (Exception $e) {
			header("HTTP/1.1 500 Internal Server Error");

			$this->response['status'] = 'failed';
			$this->response['response_code'] = '204';
			$this->response['message'] = 'Unexpected Error';

			echo json_encode($this->response);
			exit();

			// MUST LOG THIS ERROR
			// $e->getMessage();
		}
	}

	public function get_valid_ids()
	{
		$data = [];
		$query = $this->db->get("valid_ids");
		foreach ($query->result() as $value) {
			$data[] = [
				'id' => $value->id,
				'id_code' => $value->id_code,
				'id_name' => $value->id_name,
			];
		}

		header("HTTP/1.1 201 Created");

		$this->response['status'] = 'success';
		$this->response['response_code'] = '201';
		$this->response['message'] = 'Data Received!';
		$this->response['data'] = $data;

		echo json_encode($this->response);
		exit();
	}

	public function validate_mobile_number () {
		$mobileNumber = $this->input->post('mobile_number');

		$query = $this->db->query("SELECT * FROM users WHERE mobile_number = '$mobileNumber'");
		if ($query->result() != null) {
			$this->response['mobile_number_available'] = false;
		} else {
			$this->response['mobile_number_available'] = true;
		}

		header("HTTP/1.1 201 Created");
		$this->response['status'] = 'success';
		$this->response['response_code'] = '201';
		$this->response['message'] = 'Data Received!';

		echo json_encode($this->response);
		exit();
	}

	public function validate_username () {
		$username = $this->input->post('username');

		$query = $this->db->query("SELECT * FROM users WHERE username = '$username'");
		if ($query->result() != null) {
			$this->response['username_available'] = false;
		} else {
			$this->response['username_available'] = true;
		}

		header("HTTP/1.1 201 Created");
		$this->response['status'] = 'success';
		$this->response['response_code'] = '201';
		$this->response['message'] = 'Data Received!';

		echo json_encode($this->response);
		exit();
	}

}
