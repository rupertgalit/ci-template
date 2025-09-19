<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ApiService
{
  protected $CI;
  protected $secret_key;
  protected $endpoint_base_url;

  public function __construct()
  {
    $this->CI = &get_instance();
    $this->CI->load->database();

    // Key for encryption (must be the same on both ends)
    $this->secret_key = $_ENV['SECRET_KEY'];
    $this->endpoint_base_url = $_ENV['ENDPOINT_BASE_URL'];
  }

  // Generation of Token were developed here as well to lessen the steps
  // This will ommit the step of getting token from the External API
  private function generate_token()
  {

  }

  public function call_external_api($data)
  {

  }

}
