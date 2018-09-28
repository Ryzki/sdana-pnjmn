<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pendana extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('User_model');
		$this->load->model('Member_model');
		$this->load->model('Grade_model');
		$this->load->model('Wallet_model');
		
		// error_reporting(E_ALL);
	}

	function index()
	{
		$this->User_model->has_login();

		$output['PAGE_TITLE'] = 'Member';

		$mainData['top_css']   = '';
		$mainData['top_js']    = '';
		$mainData['bottom_js'] = '';
		$mainData['bottom_js'] .= add_js('js/data/pendana.js');
		$mainData['bottom_js'] .= add_js('js/global.js');

		$mainData['mainContent']  = $this->load->view('member/vpendana', $output, true);

		$this->load->view('vbase',$mainData);
	}

	function json()
	{			
		$data = $this->Member_model->get_member(2);
		print_r($data);
	}

	function detail()
	{
		$id = $this->input->post('id');

		$output['data'] = $this->Member_model->get_usermember_by($id);
		$this->load->view('member/vdetail', $output);
	}

	function activate()
	{
		$this->User_model->has_login();

		$id = antiInjection($this->uri->segment(3));

		if ($id)
		{
			$affected = $this->Member_model->set_member_status($id, 1);

			$this->session->set_userdata('message','Pendana telah Aktif.');
			$this->session->set_userdata('message_type','success');
		}else{
			$this->session->set_userdata('message','No Data selected.');
			$this->session->set_userdata('message_type','warning');
		}

		redirect('pendana');
	}

	function deactivate()
	{
		$this->User_model->has_login();

		$id = antiInjection($this->uri->segment(3));

		if ($id)
		{
			$affected = $this->Member_model->set_member_status($id, 0);

			$this->session->set_userdata('message','Pendana telah Aktif.');
			$this->session->set_userdata('message_type','success');
		}else{
			$this->session->set_userdata('message','No Data selected.');
			$this->session->set_userdata('message_type','warning');
		}

		redirect('pendana');
	}

	function delete()
	{
		$this->User_model->has_login();

		$id = antiInjection($this->uri->segment(3));

		$getdata = $this->Member_model->get_user_ojk_bymember($id);

		$del = $this->Member_model->delete_member($id);
		if($id && $del){

			$this->Member_model->delete_user_ojk($id);
			$this->Member_model->delete_user_ojk_detail($getdata['Id_pengguna']);
			$this->Member_model->delete_profil_geografi($getdata['Id_pengguna']);
			$this->Wallet_model->delete_master_wallet($getdata['Id_pengguna']);
			$this->Wallet_model->delete_detail_wallet($getdata['Id_pengguna']);

			$this->session->set_userdata('message','Data has been deleted.');
			$this->session->set_userdata('message_type','success');
		}else{
			$this->session->set_userdata('message','No Data was deleted.');
			$this->session->set_userdata('message_type','warning');
		}

		redirect('pendana');
	}

	public function edit()
	{
		$this->User_model->has_login();

		$id             = antiInjection($this->uri->segment(3));
		$output['mode'] = 2; // sbg tanda edit
		$output['EDIT'] = $this->Member_model->get_usermember_by($id);

		$this->validation();
		if ($this->form_validation->run() == FALSE)
		{
			$output['top_css']   ="";
			$output['top_js']    ="";
			$output['bottom_js'] ="";

			$output['top_css']   .= add_css("plugins/fileinput/fileinput.min.css");
			$output['top_css']   .= add_css("plugins/jquery-tags-input/dist/bootstrap-tagsinput.css");
			$output['top_css']   .= add_css("plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css");
			$output['top_css']   .= add_css("plugins/bootstrap-timepicker/bootstrap-timepicker.css");
			$output['top_css']   .= add_css("plugins/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css");
			
			$output['top_js']    .= add_js("plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js");
			$output['top_js']    .= add_js("plugins/bootstrap-timepicker/bootstrap-timepicker.js");
			$output['top_js']    .= add_js('plugins/ckeditor/ckeditor.js');
			$output['top_js']    .= add_js("plugins/autonumeric/autoNumeric.js");
			$output['top_js']    .= add_js("plugins/fileinput/fileinput.min.js");
			$output['top_js']    .= add_js('plugins/jquery-tags-input/dist/bootstrap-tagsinput.js');
			$output['top_js']    .= add_js("plugins/friendurl/jquery.friendurl.min.js");
			$output['top_js']    .= add_js("plugins/bootstrap-switch/js/bootstrap-switch.min.js");
			$output['top_js']    .= add_js("plugins/numeric/jquery.numeric.min.js");
			
			$output['bottom_js'] .= add_js('js/select2-data.js');
			$output['bottom_js'] .= add_js('js/global.js');

			$output['grade'] = $this->Grade_model->get_active_grade();
			
			$mainData['mainContent'] = $this->load->view('member/vpendana_form', $output, TRUE);

			$this->load->view('vbase', $mainData);
		}else{
			$post = $this->input->post(NULL, TRUE);

			if (trim($post['grade']) != '' && !empty($id) )
			{
				$updata['peringkat_pengguna'] = antiInjection(trim($post['grade']));
				$affected = $this->Member_model->update_user_ojk($updata, $id);
				if($affected){

					$this->session->set_userdata('message','Data has been updated.');
					$this->session->set_userdata('message_type','success');
				}else{
					$this->session->set_userdata('message','No Update.');
					$this->session->set_userdata('message_type','warning');
				}
			}
			
			redirect('pendana');
		}
	}

	function validation()
	{
		$this->form_validation->set_rules('grade', 'Grade', 'trim|required');

		$this->form_validation->set_message('required', '%s harus diisi.');
	}

}