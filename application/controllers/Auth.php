<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'services/ApiService.php');

class Auth extends CI_Controller
{
	public $apiService;

	public function __construct()
	{
		parent::__construct();

		$this->apiService = new ApiService();

		// if($this->session->userdata('user_id') !== TRUE){
		//     redirect('login');
		// }
		// }
		// NOTE: 1 - Player
		//       2 - Csr (Only for Pagcor)
		//       5 - Declarator
		//       4 - Market Manager
		//       ? - Moderator???
		//       ? - Admin
		//  Di pako nakakapag update ng DB for other user type, yaan mo muna mga yan haha


	}

	// **READ ME: We might change the structure a little if the data we need must be real time (will use sse)
	private function call_external_api($data)
	{
		// RE-WRITE $DATA FOR TESTING PURPOSES
		$response = $this->apiService->call_external_api($data);
		return $response;
	}



	public function login()
	{
		$data =  json_decode(file_get_contents('php://input'), true);
		$this->output->set_content_type('application/json');
		// $data = [
		// 	'endpoint' => 'login',
		// 	'data' => [
		// 		'mobile_number' => '09123456789',
		// 		'password' => '123456',
		// 	]
		// ];
		$response = $this->call_external_api($data);

		$jdata = json_decode($response, true);

		if ($jdata['status_code'] == 200) {
			$sesdata = array(

				'user_id' => $jdata['data']['set_session']['user_id'],
				'user_type_id' =>  $jdata['data']['set_session']['user_type_id'],
				'user_name' =>  $jdata['data']['set_session']['user_name'],
				'Active' => TRUE

			);

			$this->session->set_userdata($sesdata);

			// status=200
			// data =

			// $repData['redirect_url']=base_url( $this->redirect($sesdata['user_type_id']));
			// $repData['response']=	$jdata; 

			if (isset($jdata['data']['user_details'])) {
				unset($jdata['data']['user_details']);
			}

			$jdata['redirect_url'] = base_url($this->redirect($sesdata['user_type_id']));
			echo json_encode($jdata);

			//  redirect(base_url( $this->redirect($sesdata['user_type_id'])));
		} else {
			echo $response;
		}
	}

	function redirect($type)
	{
		$caffeine = '';
		$map = [
			'4' => 'marketmanager',
			'5' => 'declarator',

		];
		// $caffeine = $map[$type] ?? 'Not found';
		$caffeine = $map[$type];
		return $caffeine;
	}

	function logout()
	{
		$this->session->sess_destroy();

		redirect(base_url());
	}
}


/* End of file Player.php */
/* Location: ./application/controllers/Player.php */
