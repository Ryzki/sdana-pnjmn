<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wallet extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('User_model');
		$this->load->model('Member_model');
		$this->load->model('Grade_model');
		$this->load->model('Wallet_model');
		
		 //error_reporting(E_ALL);
		 //ini_set('display_errors', '1');
	}

	function index()
	{
		$this->User_model->has_login();

		$output['PAGE_TITLE'] = 'Wallet';

		$mainData['top_css']   = '';
		$mainData['top_js']    = '';
		$mainData['bottom_js'] = '';
		$mainData['bottom_js'] .= add_js('js/data/wallet.js');
		$mainData['bottom_js'] .= add_js('js/global.js');

		$mainData['mainContent']  = $this->load->view('wallet/vlist', $output, true);

		$this->load->view('vbase',$mainData);
	}

	function json()
	{			
		$data = $this->Wallet_model->get_all_dt();
		print_r($data);
	}

	function delete()
	{
		$this->User_model->has_login();

		$id = antiInjection($this->uri->segment(3));

		$del = $this->Wallet_model->delete_master($id);
		if($id && $del){

			$this->session->set_userdata('message','Data has been deleted.');
			$this->session->set_userdata('message_type','success');
		}else{
			$this->session->set_userdata('message','No Data was deleted.');
			$this->session->set_userdata('message_type','warning');
		}

		redirect('peminjam');
	}
}