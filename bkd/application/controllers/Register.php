<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {

	public function __construct()
	{
		parent::  __construct();

		$this->load->model('Member_model');
		$this->load->library('encryption');
		//error_reporting(E_ALL);
		error_reporting(0);
	}

	public function index()
	{
		// clear browser cachesubn
		header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		$logintype    = isset($_SESSION['_bkdtype_'])? htmlentities($_SESSION['_bkdtype_']) : 0; // 1.peminjam, 2.pendana

		if (isset($_SESSION['_bkdlog_']) && isset($_SESSION['_bkduser_'])) {
			// Jika sudah login maka redirect ke form only
			if ($logintype=='1') {
				redirect('formulir-pinjaman-kilat');
				exit();
			}else{
				redirect('message/restrict_pendana');
				exit();
			}
		}

		$data['top_css']   = '';
		$data['top_js']    = '';
		$data['bottom_js'] = '';

		$data['top_css'] .= add_css('js/validationengine/validationEngine.jquery.css');
		$data['top_css'] .= add_css('js/bootstrap-datepicker/css/bootstrap-datepicker.css');
		$data['top_css'] .= add_css('js/alertify/css/alertify.min.css');
		$data['top_css'] .= add_css('js/alertify/css/themes/default.min.css');
		$data['top_css'] .= add_css("js/fileinput/fileinput.min.css");


		$data['bottom_js'] .= add_js('js/validationengine/languages/jquery.validationEngine-en.js');
		$data['bottom_js'] .= add_js('js/validationengine/jquery.validationEngine.js');
		$data['bottom_js'] .= add_js('js/bootstrap-datepicker/js/bootstrap-datepicker.min.js');
		$data['bottom_js'] .= add_js('js/jqueryvalidation/dist/jquery.validate.min.js');
		$data['bottom_js'] .= add_js('js/autoNumeric/autoNumeric.min.js');
		$data['bottom_js'] .= add_js('js/alertify/alertify.min.js');
		$data['bottom_js'] .= add_js("js/fileinput/fileinput.min.js");
		$data['bottom_js'] .= add_js('js/validation-init.js');
		$data['bottom_js'] .= add_js('js/autoNumeric-init.js');
		$data['bottom_js'] .= add_js('js/date-init.js');
		$data['bottom_js'] .= add_js('js/fileinput-init.js');
		$data['bottom_js'] .= add_js('js/dsn.js');
		$data['bottom_js'] .= add_js('js/form-wizard.js');
		$data['bottom_js'] .= add_js('js/register.js');

		$data['title'] = $this->M_settings->title;
		$data['meta_tag'] = $this->M_settings->meta_tag_noindex('daftar, pinjaman kilat', 'daftar pinjaman kilat');

		$data['harga'] = $this->Content_model->get_harga_pinjaman_kilat();

		//_d($data['harga']);

		$data['products'] = $this->Content_model->get_pinjaman(1); // type_off_business_id

		$data['pages']    = 'v_register';
		$this->load->view('template', $data);
	}

	function submit_()
	{
		if($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$post = $this->input->post(NULL, TRUE);

			$nowdate     = date('Y-m-d');
			$nowdatetime = date('Y-m-d H:i:s');
			$fullname    = trim($post['fullname']);
			$notelp      = trim($post['telp']);
			$email       = trim($post['email']);
			$password    = trim($post['password']);
			$repassword  = trim($post['confirm_password']);
			$tipe_member = trim($post['tipe_member']);

			$check = $this->Content_model->check_existing_member($email, $notelp, '');
			$count_member = count($check);

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$ret = array('error'=> '1', 'message'=>'Invalid Email format!');

			}else if ( $count_member > 1){
				$ret = array('error'=> '1', 'message'=>'Email/No.Telp Anda sudah terdaftar!');

			}else if ( $password == '' OR strlen($password) < 6 ) {
				$ret = array('error'=> '1', 'message'=>'Password minimal 6 karakter.');

			}else if(preg_match("/^.*(?=.{6,})(?=.*[0-9])(?=.*[a-zA-Z]).*$/", $password) === 0) {
				// min 6 karakter, terdiri dari minimum 1 huruf, minimum 1 angka
				$ret = array('error'=> '1', 'message'=>'Password harus terdiri dari huruf dan angka');
			
			}else if ( $password != $repassword ) {
				$ret = array('error'=> '1', 'message'=>'Password dan Konfirmasi Password tidak sama!');
				
			}else if ($fullname != '' 
				&& $notelp != '' 
				&& $password == $repassword && strlen($password) >=6 
				) {

				$stored_p = password_hash(base64_encode(hash('sha256', (trim($post['password'])), true)), PASSWORD_DEFAULT);

				$mem_data['mum_fullname']      = trim($post['fullname']);
				$mem_data['mum_email']         = trim($post['email']);
				$mem_data['mum_telp']          = trim($post['telp']);
				$mem_data['mum_password']      = $stored_p;
				$mem_data['mum_status']        = 0;
				$mem_data['mum_create_date']   = $nowdatetime;
				$mem_data['mum_type']          = $tipe_member; // 1.peminjam, 2.pendana
				$mem_data['mum_type_peminjam'] = '0'; // 1.Kilat, 2.mikro

				$uid = $this->Content_model->insert_mod_usermember($mem_data);

				if ($uid) {

					$prefixID    = 'PK-';
					$orderID     = $prefixID.strtoupper(substr(uniqid(sha1(time().$uid)),0,12));
			        $exist_order = $this->Content_model->check_ordercode_pinjaman($orderID);	// Cek if order ID exist on Database
					
					// jika order ID sudah ada di Database, generate lagi tambahkan datetime
					if (is_array($exist_order) && count($exist_order) > 0 )
					{
						$orderID = $prefixID.$uid.strtoupper(substr(uniqid(sha1(time().$uid)),0,3)).date('YmdHis');
					}

					// user
					$user['Tgl_record']         = $nowdate;
					$user['Nama_pengguna']      = $fullname;
					$user['Jenis_pengguna']     = 1; // 1.orang, 2.badan hukum
					$user['id_mod_user_member'] = $uid;

					$userID = $this->Content_model->insert_user($user);

					// user_detail
					$u_detail['Id_pengguna']       = $userID;
					$u_detail['user_type']         = 'peminjam';
					$u_detail['Mobileno']          = $notelp;

					$this->Content_model->insert_userdetail($u_detail);

					// profile_geografi
					$u_geo['Agama']       = NULL;
					$u_geo['Alamat']      = NULL;
					$u_geo['Kodepos']     = NULL;
					$u_geo['Kota']        = NULL;
					$u_geo['Provinsi']    = NULL;
					$u_geo['User_id']     = $userID;

					$this->Content_model->insert_profil_geografi($u_geo);	

					//$set_otp = $this->Member_model->set_cookies_otp($email); // set cookies for OTP login controller login/login_otp

					// ranking
					$get_ranking = set_ranking_pengguna($userID, 1, 1); // (Id_pengguna, peminjam/pendana, kilat/mikro)

					$update_pengguna['peringkat_pengguna']            = $get_ranking['grade'];
					$update_pengguna['peringkat_pengguna_persentase'] = $get_ranking['ranking'];
					$this->Content_model->update_user_byid($userID, $update_pengguna);
					// End ranking

					$this->send_email($email);

					$ret = array('error'=> '0', 'message'=>'Sukses daftar pinjaman kilat.');
					$this->session->set_userdata('message','Sukses daftar pinjaman kilat');
					$this->session->set_userdata('message_type','success');
				}
			}else{
				$ret = array('error'=> '1', 'message'=>'Isilah semua kolom!');
				//$this->session->set_userdata('message','Isilah semua kolom!');
				//$this->session->set_userdata('message_type','error');
			}

			echo json_encode($ret);
		}
	}
}