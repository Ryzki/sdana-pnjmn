<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transaksi extends CI_Controller {

	/* --- Transaksi Peminjam ---  */

	public function __construct()
	{
		parent::  __construct();

		$this->load->model('Member_model');
		$this->load->model('Wallet_model');

		$this->load->library('pagination');

		//error_reporting(E_ALL);

		$this->Content_model->has_login();
	}
	
	public function index()
	{
		$data['top_css']   = '';
		$data['top_js']    = '';
		$data['bottom_js'] = '';

		$data['top_css'] .= add_css('js/validationengine/validationEngine.jquery.css');
		$data['bottom_js'] .= add_js('js/jquery-loading-overlay/dist/loadingoverlay.min.js');
		$data['bottom_js'] .= add_js('js/validationengine/languages/jquery.validationEngine-en.js');
		$data['bottom_js'] .= add_js('js/validationengine/jquery.validationEngine.js');
		$data['bottom_js'] .= add_js('js/dsn.js');

		$data['title'] = $this->M_settings->title;
		$data['meta_tag'] = $this->M_settings->meta_tag_noindex('bkdana.com', 'website bkdana.com');

		$uid       = htmlentities($_SESSION['_bkduser_']);
		$logintype = htmlentities($_SESSION['_bkdtype_']); // 1.peminjam, 2.pendana

		$limit_per_page = 10;
        $page           = (int)antiInjection($this->uri->segment(3));

        if (empty($page)) {
	        $start_index    = 0;;
	    }else{
	        $start_index    = ($page*$limit_per_page)-$limit_per_page;
	    }

		$data['logintype'] = $logintype;
		$data['memberid']  = $uid;
		$data['memberdata']     = $this->Member_model->get_member_byid($uid);
		$data['total_pinjaman'] = $this->Content_model->get_jml_pinjam($uid);
		$data['total_invest']   = $this->Content_model->get_jml_invest($uid);
		$data['total_saldo']    = $this->Content_model->get_total_saldo($uid);

		if ($logintype == '1') {
			// Peminjam
			$data['list_transaksi'] = $this->Content_model->get_my_transactions_pinjam($uid, $limit_per_page, $start_index);
			$total_records          = $this->Content_model->get_total_pinjam($uid);
			$data['pages']          = 'v_transaksi';
		}else{
			// Pendana
			$data['list_transaksi'] = $this->Content_model->get_my_transactions_pendana($uid, $limit_per_page, $start_index);
			$total_records          = $this->Content_model->get_total_pendana($uid);
			$data['pages']          = 'v_transaksi_pendana';
		}  
 
        if (is_array($total_records) && $total_records['itotal'] > 0) 
        {             
            $config['base_url']    = base_url() . 'transaksi/page';
            $config['total_rows']  = $total_records['itotal'];
            $config['per_page']    = $limit_per_page;
            $config["uri_segment"] = 3;
            // custom paging configuration
            $config['num_links']   = 2;
            $config['use_page_numbers']   = TRUE;
            $config['reuse_query_string'] = TRUE;
             
            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';
             
            $config['first_link'] = 'First';
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
             
            $config['last_link'] = 'Last';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
             
            $config['next_link'] = '&raquo;';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
 
            $config['prev_link'] = '&laquo;';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
 
            $config['cur_tag_open'] = '<li><a style="background-color:#f0f8ff;">';
            $config['cur_tag_close'] = '</a></li>';
 
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
             
            $this->pagination->initialize($config);
             
            // build paging links
            $data["pagination"] = $this->pagination->create_links();
        }else{
        	$data["pagination"] = '';
        }

		
		$this->load->view('template', $data);
	}

	function detail()
	{
		// ====== Detail Pinjaman ========

		$data['top_css']   = '';
		$data['top_js']    = '';
		$data['bottom_js'] = '';

		$data['top_css'] .= add_css('js/validationengine/validationEngine.jquery.css');
		$data['top_css'] .= add_css('js/alertify/css/alertify.min.css');
		$data['top_css'] .= add_css('js/alertify/css/themes/default.min.css');

		$data['bottom_js'] .= add_js('js/jquery-loading-overlay/dist/loadingoverlay.min.js');
		$data['bottom_js'] .= add_js('js/validationengine/languages/jquery.validationEngine-en.js');
		$data['bottom_js'] .= add_js('js/validationengine/jquery.validationEngine.js');
		$data['bottom_js'] .= add_js('js/alertify/alertify.min.js');
		$data['bottom_js'] .= add_js('js/autoNumeric/autoNumeric.min.js');
		$data['bottom_js'] .= add_js('js/autoNumeric-init.js');
		$data['bottom_js'] .= add_js('js/dsn.js');
		$data['bottom_js'] .= add_js('js/transaction.js');

		$data['title'] = $this->M_settings->title;
		$data['meta_tag'] = $this->M_settings->meta_tag_noindex('bkdana.com', 'website bkdana.com');

		$uid = htmlentities($_SESSION['_bkduser_']);
		$logintype = htmlentities($_SESSION['_bkdtype_']); // 1.peminjam, 2.pendana
		$data['logintype'] = $logintype;
		$data['memberid']  = $uid;
		$data['memberdata']     = $this->Member_model->get_member_byid($uid);
		$data['total_saldo']    = $this->Content_model->get_total_saldo($uid);

		$ID = antiInjection($this->input->get('tid', TRUE)); // transaksi id

		$log_transaksi_pinjam     = $this->Content_model->get_log_transaksi_pinjam($ID);
		$transaksi                = $this->Content_model->get_transaksi_pinjam_byid($ID); // pinjaman
		$data['detail_transaksi'] = $this->Content_model->get_detail_pinjam_byid($ID); // cicilan
		$data['transaksi']        = $transaksi;
		$data['log_pinjaman']     = $log_transaksi_pinjam;

		$total_bayar = $transaksi['Jml_permohonan_pinjaman_disetujui'];
		$data['total_bayar'] = $total_bayar;
		$data['jatuh_tempo'] = '-';
		
		if ($transaksi['type_of_business_id'] == '1')
		{
			//echo ' Pinjaman Kilat';
			
			$data['jml_cicilan'] = $log_transaksi_pinjam['ltp_jml_angsuran'];			
			$data['pages']    = 'v_transaksi_detail_kilat';

			if ( $transaksi['Master_loan_status'] == 'complete' || $transaksi['Master_loan_status'] == 'lunas') {
				$data['jatuh_tempo'] = date('d/m/Y', strtotime($log_transaksi_pinjam['ltp_tgl_jatuh_tempo']));
			}
		}else{
			//echo 'Pinjaman Mikro';
			
			$data['jml_cicilan']   = $log_transaksi_pinjam['ltp_jml_angsuran'];
			$data['lama_angsuran'] = $log_transaksi_pinjam['ltp_lama_angsuran']; // berapa minggu

			$data['pages']         = 'v_transaksi_detail_mikro';

			if ($transaksi['Master_loan_status'] == 'complete' || $transaksi['Master_loan_status'] == 'lunas') {
				$data['jatuh_tempo'] = date('d/m/Y', strtotime("+3 months", strtotime($transaksi['tgl_pinjaman_disetujui'])));
			}
		}

		$this->load->view('template', $data);
	}

	function submit_cicilan_kilat()
	{
		// ========= Bayar cicilan Kilat pakai Saldo ============= //

		if($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$post = $this->input->post(NULL, TRUE);

			$uid = htmlentities($_SESSION['_bkduser_']);

			$transaksi_id       = trim($post['transaksi_id']);
			$jml_cicilan_hidden = trim($post['jml_cicilan']);	// jml cicilan asli
			$jml_cicilan        = trim($post['jml_bayar']);		// jml dari input user

			$filter = explode('.', $jml_cicilan);
			$jml_bayar = str_replace(',', '', $filter[0]);

			if (!empty($uid) && $transaksi_id != '' AND $jml_cicilan != '' AND strlen($jml_cicilan) > 4 && $jml_cicilan_hidden == $jml_bayar)
			{
				$nowdate     = date('Y-m-d');
				$nowdatetime = date('Y-m-d H:i:s');
				$get_master_wallet = $this->Wallet_model->get_wallet_bymember($uid);

				if (count($get_master_wallet)>0 && isset($get_master_wallet['Id']) && $get_master_wallet['Amount'] >= $jml_bayar)
				{

					$indetail['Master_loan_id']   = antiInjection($transaksi_id);
					$indetail['Date_repaid']      = $nowdate;
					$indetail['Amount_repayment'] = antiInjection($jml_bayar);
					$indetail['Nomor_angsuran']   = 1;

					$detail_id = $this->Content_model->insert_cicilan($indetail);

					if ($detail_id)
					{
						// update profil pinjaman tambah total_loan_repayment, kurangi total_loan_outstanding
						$this->Content_model->update_total_loan_repayment($indetail['Master_loan_id'], $uid, $indetail['Amount_repayment']);

						// master wallet -> kurangi saldo peminjam
						$this->Wallet_model->kurangi_saldo_wallet($uid, $jml_bayar);

						// detail transaksi wallet 
						$detail_w['Id']               = $get_master_wallet['Id'];
						$detail_w['Date_transaction'] = $nowdate;
						$detail_w['Amount']           = $jml_bayar;
						$detail_w['Notes']            = 'Pembayaran pinjaman No.'. $indetail['Master_loan_id'];
						$detail_w['tipe_dana']        = 2;
						$detail_w['User_id']          = $get_master_wallet['User_id'];
						$detail_w['kode_transaksi']   = $indetail['Master_loan_id'];
						$detail_w['balance']          =  $get_master_wallet['Amount'] - $detail_w['Amount'];
						$this->Wallet_model->insert_detail_wallet($detail_w);

						$get_data_pinjam = $this->Content_model->get_transaksi_pinjam_byid($transaksi_id); // get total yg sdh diangsur

						if ($get_data_pinjam['Total_loan_repayment'] >= $get_data_pinjam['Jml_permohonan_pinjaman_disetujui'])
						{
							// ------ status lunas, date close -------
							$this->Content_model->close_pinjaman($indetail['Master_loan_id']);
							$this->Content_model->update_status_pendana($indetail['Master_loan_id'], 'received');
						}

						// ----- pengembalian Saldo ke Pendana ------
						$list_pendana = $this->Content_model->get_pendanaan_byloan($transaksi_id);						

						foreach ($list_pendana as $dp) {
							$get_wallet_pendana = $this->Wallet_model->get_wallet_byuser($dp['User_id']);

							$tambah_saldo = $dp['jml_angsuran_ke_pendana'];

							// tambah saldo pendana
							$this->Wallet_model->update_master_wallet_saldo($dp['User_id'], $tambah_saldo);

							// wallet detail
							$upw_pendana['Id']               = $get_wallet_pendana['Id'];
							$upw_pendana['Date_transaction'] = $nowdate;
							$upw_pendana['Amount']           = $tambah_saldo;
							$upw_pendana['Notes']            = 'Pengembalian dana '.$dp['Id'].' oleh pinjaman No.'. $transaksi_id;
							$upw_pendana['tipe_dana']        = 1;
							$upw_pendana['User_id']          = $dp['User_id'];
							$upw_pendana['kode_transaksi']   = $indetail['Master_loan_id'];
							$upw_pendana['balance']          = $get_wallet_pendana['Amount'] + $upw_pendana['Amount'];
							$this->Wallet_model->insert_detail_wallet($upw_pendana);
						}
						// ----- End of pengembalian Saldo ke Pendana ------

						// update table mod_tempo. isi is_paid =1
						$uptempo['is_paid']     = 1;
						$uptempo['date_paid']   = $nowdatetime;
						$this->Content_model->update_table_tempo($indetail['Master_loan_id'], 1, $uptempo);
						
						$this->session->set_userdata('message','Sukses melakukan pembayaran.');
						$this->session->set_userdata('message_type','success');
					}
				}

			}else{
				$this->session->set_userdata('message','Jumlah pembayaran cicilan tidak sesuai.');
				$this->session->set_userdata('message_type','error');
			}
			redirect('transaksi/detail/?tid=' . $transaksi_id);
		}
	}

	function submit_cicilan_mikro()
	{
		if($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$post = $this->input->post(NULL, TRUE);
			$lunas = 0;

			$uid = strip_tags($_SESSION['_bkduser_']);

			$transaksi_id       = trim($post['transaksi_id']);
			$jml_cicilan_hidden = trim($post['jml_cicilan']);	// jml cicilan asli
			$jml_cicilan        = trim($post['jml_bayar']);		// jml dari input user

			$filter    = explode('.', $jml_cicilan);
			$jml_bayar = ($filter[1]=='00')? str_replace(',', '', $filter[0]) : str_replace(',', '', $filter[0]) .'.'. $filter[1];

			if (!empty($uid) && $transaksi_id != '' AND $jml_cicilan != '' AND strlen($jml_cicilan) > 4 && $jml_cicilan_hidden == $jml_bayar)
			{
				$nowdate     = date('Y-m-d');
				$nowdatetime = date('Y-m-d H:i:s');
				$get_master_wallet = $this->Wallet_model->get_wallet_bymember($uid);

				// Cek apakah saldo cukup
				if (count($get_master_wallet)>0 && isset($get_master_wallet['Id']) && $get_master_wallet['Amount'] >= $jml_bayar)
				{
					// hitung nomor cicilan ke berapa 
					$last_number   = $this->Content_model->get_nomor_angsuran($transaksi_id);
					$nomor_cicilan = $last_number['itotal'] + 1;

					// hitung total cicilan seluruhnya
					$getlog         = $this->Content_model->get_log_transaksi_pinjam($transaksi_id);
					$total_angsuran = $getlog['ltp_lama_angsuran'] * $getlog['ltp_jml_angsuran'];

					// ------ Insert cicilan ke table detail_profil_permohonan_pinjaman -----
					$indetail['Master_loan_id']   = antiInjection($transaksi_id);
					$indetail['Date_repaid']      = $nowdate;
					$indetail['Amount_repayment'] = antiInjection($jml_bayar);
					$indetail['Nomor_angsuran']   = $nomor_cicilan;

					$detail_id = $this->Content_model->insert_cicilan($indetail);

					if ($detail_id)
					{
						// update profil pinjaman tambah total_loan_repayment, kurangi total_loan_outstanding
						$this->Content_model->update_total_loan_repayment($indetail['Master_loan_id'], $uid, $indetail['Amount_repayment']);

						// master wallet -> kurangi saldo peminjam
						$this->Wallet_model->kurangi_saldo_wallet($uid, $jml_bayar);

						// detail transaksi wallet 
						$detail_w['Id']               = $get_master_wallet['Id'];
						$detail_w['Date_transaction'] = $nowdate;
						$detail_w['Amount']           = $jml_bayar;
						$detail_w['Notes']            = 'Pembayaran cicilan '.$nomor_cicilan.' - '. $indetail['Master_loan_id'];
						$detail_w['tipe_dana']        = 2;
						$detail_w['User_id']          = $get_master_wallet['User_id'];
						$detail_w['kode_transaksi']   = $indetail['Master_loan_id'];
						$detail_w['balance']          = $get_master_wallet['Amount'] - $detail_w['Amount'];
						$this->Wallet_model->insert_detail_wallet($detail_w);

						$get_data_pinjam = $this->Content_model->get_transaksi_pinjam_byid($transaksi_id); // get total yg sdh diangsur

						// cek apakah cicilan sudah seluruhnya dibayar
						if ($get_data_pinjam['Total_loan_repayment'] >= $total_angsuran)
						{
							// ------ status lunas, date close -------
							$this->Content_model->close_pinjaman($indetail['Master_loan_id']);
							$this->Content_model->update_status_pendana($indetail['Master_loan_id'], 'received');

							$lunas = 1;

							if ($getlog['ltp_frozen'] > 1)
							{
								// --------- frozen dicairkan -----------
								$get_master_2 = $this->Wallet_model->get_wallet_bymember($uid);

								// tambah saldo peminjam
								$this->Wallet_model->update_master_wallet_saldo($get_data_pinjam['Id_pengguna'], $getlog['ltp_frozen']);

								// wallet detail
								$sf['Id']               = $get_master_2['Id'];
								$sf['Date_transaction'] = $nowdate;
								$sf['Amount']           = $getlog['ltp_frozen'];
								$sf['Notes']            = 'Saldo Frozen Fee '. $transaksi_id;
								$sf['tipe_dana']        = 1;
								$sf['User_id']          = $get_data_pinjam['Id_pengguna'];
								$sf['kode_transaksi']   = $transaksi_id;
								$sf['balance']          = $get_master_2['Amount'] + $sf['Amount'];
								$this->Wallet_model->insert_detail_wallet($sf);

								// insert table log frozen
								$infr['frozen_amount'] = $getlog['ltp_frozen'];
								$infr['transaksi_id']  = $transaksi_id;
								$infr['Id_pengguna']   = $get_data_pinjam['Id_pengguna'];
								$infr['date']          = $nowdatetime;
								$this->Content_model->insert_log_frozen($infr);
							}
						}

						// update table mod_tempo. isi is_paid =1
						$uptempo['is_paid']     = 1;
						$uptempo['date_paid']   = $nowdatetime;
						$this->Content_model->update_table_tempo($indetail['Master_loan_id'], $nomor_cicilan, $uptempo);

						// ----- pengembalian Saldo ke Pendana ------
						$list_pendana = $this->Content_model->get_pendanaan_byloan($transaksi_id);

						foreach ($list_pendana as $dp) {
							
							$get_wallet_pendana = $this->Wallet_model->get_wallet_byuser($dp['User_id']);

							$tambah_saldo = $dp['jml_angsuran_ke_pendana'];

							// tambah saldo pendana
							$this->Wallet_model->update_master_wallet_saldo($dp['User_id'], $tambah_saldo);

							// wallet detail
							$upw_pendana['Id']               = $get_wallet_pendana['Id'];
							$upw_pendana['Date_transaction'] = $nowdate;
							$upw_pendana['Amount']           = $tambah_saldo;
							$upw_pendana['Notes']            = 'Pengembalian dana '.$nomor_cicilan.' - '.$dp['Id'].' oleh pinjaman No.'. $transaksi_id;
							$upw_pendana['tipe_dana']        = 1;
							$upw_pendana['User_id']          = $dp['User_id'];
							$upw_pendana['kode_transaksi']   = $indetail['Master_loan_id'];
							$upw_pendana['balance']          = $get_wallet_pendana['Amount'] + $upw_pendana['Amount'];
							$this->Wallet_model->insert_detail_wallet($upw_pendana);
							
							$this->send_email($dp['mum_email'], $dp['Id'], $upw_pendana['Amount'], $upw_pendana['Notes']);
							
						}
						// ----- End of pengembalian Saldo ke Pendana ------

						// ------ insert ke LO -------
						$this->pay_to_LO($getlog, $nomor_cicilan);
						
						$this->session->set_userdata('message','Sukses melakukan pembayaran.');
						$this->session->set_userdata('message_type','success');
					}
				}

			}else{
				$this->session->set_userdata('message','Jumlah pembayaran cicilan tidak sesuai.');
				$this->session->set_userdata('message_type','error');
			}
			redirect('transaksi/detail/?tid=' . $transaksi_id);
		}
	}

	function pay_to_LO($logpinjam, $nomor_cicilan)
	{
		if ( $logpinjam['ltp_loan_organizer_id'] !='' OR $logpinjam['ltp_loan_organizer_id'] !='0' )
		{
			// tambah saldo LO
			$this->Wallet_model->update_wallet_bylo($logpinjam['ltp_loan_organizer_id'], $logpinjam['ltp_LO_fee']);
			$master_wallet = $this->Wallet_model->get_wallet_bylo($logpinjam['ltp_loan_organizer_id']);
			
			// wallet detail
			$upw_pendana['Id']                = $master_wallet['Id'];
			$upw_pendana['Date_transaction']  = date('Y-m-d H:i:s');
			$upw_pendana['Amount']            = $logpinjam['ltp_LO_fee'];
			$upw_pendana['Notes']             = 'Pembayaran Cicilan '.$nomor_cicilan.' - '. $logpinjam['ltp_Master_loan_id'];
			$upw_pendana['tipe_dana']         = 1;
			$upw_pendana['User_id']           = 0;
			$upw_pendana['kode_transaksi']    = $logpinjam['ltp_Master_loan_id'];
			$upw_pendana['loan_organizer_id'] = $logpinjam['ltp_loan_organizer_id'];
			$upw_pendana['balance']           = $master_wallet['Amount'] + $upw_pendana['Amount'];
			$this->Wallet_model->insert_detail_wallet($upw_pendana);
		}
	}

	function send_email($email, $code, $jml, $notes)
	{
		$html_content = '
        Hai '.$email.',<br><br>

            Anda telah menerima pengembalian dana dengan rincian sebagai berikut:<br><br>
            No.Pendanaan : '.$code.' <br>
            Nominal : Rp '.number_format($jml).' <br>
            Remark : '.$notes.'
            
            <br><br>

            <span style="color:#858C93;">
            	Email ini dibuat secara otomatis. Mohon tidak mengirimkan balasan ke Email ini.
            	<br><br>

            	&copy; BKDana.com, '.date("Y").'. All rights reserved.
            </span>
			';

		include(APPPATH.'libraries/phpmailer-5.2.23/PHPMailerAutoload.php');
    	$mail = new phpmailer();
        $mail->IsSMTP();
		$mail->SMTPAuth    = true;
		//$mail->SMTPSecure  = 'ssl';
		$mail->Host        = 'smtp.gmail.com';
		$mail->Port        = 587;
		$mail->IsHTML(true);
		$mail->Username    = $this->config->item('mail_username');
		$mail->Password    = $this->config->item('mail_password');
		$mail->SetFrom('bkdanafinansial@gmail.com', 'BKDana');	
		$mail->AddAddress($email);
		$mail->Subject     = 'Pengembalian Dana';
		$mail->AltBody     = 'To view the message, please use an HTML compatible email viewer!';
		$mail->MsgHTML($html_content);	
		$mail->SMTPDebug   = 0;
        if(!$mail->Send()) {
            //echo $mail->ErrorInfo;exit;
        	$result = 'failed';		

        }else{
            $result = 'success';		                		               	
        }	

        return TRUE;
	}

	public function search()
	{
		$data['top_css']   = '';
		$data['top_js']    = '';
		$data['bottom_js'] = '';

		$data['top_css'] .= add_css('js/validationengine/validationEngine.jquery.css');

		$data['bottom_js'] .= add_js('js/jquery-loading-overlay/dist/loadingoverlay.min.js');
		$data['bottom_js'] .= add_js('js/validationengine/languages/jquery.validationEngine-en.js');
		$data['bottom_js'] .= add_js('js/validationengine/jquery.validationEngine.js');
		$data['bottom_js'] .= add_js('js/dsn.js');

		$data['title'] = $this->M_settings->title;
		$data['meta_tag'] = $this->M_settings->meta_tag_noindex('bkdana.com', 'website bkdana.com');

		$search_string = antiInjection(trim($this->input->get('q', TRUE)));
		
		$uid       = htmlentities($_SESSION['_bkduser_']);
		$logintype = htmlentities($_SESSION['_bkdtype_']); // 1.peminjam, 2.pendana

		$limit_per_page = 10;
        $page           = (int)antiInjection($this->uri->segment(3));

        if (empty($page)) {
	        $start_index    = 0;;
	    }else{
	        $start_index    = ($page*$limit_per_page)-$limit_per_page;
	    }

		$data['logintype'] = $logintype;
		$data['memberid']  = $uid;
		$data['memberdata']     = $this->Member_model->get_member_byid($uid);
		$data['total_pinjaman'] = $this->Content_model->get_jml_pinjam($uid);
		$data['total_invest']   = $this->Content_model->get_jml_invest($uid);
		$data['total_saldo']    = $this->Content_model->get_total_saldo($uid);

		if ($logintype == '1') {
			$data['list_transaksi'] = $this->Content_model->get_my_transactions_pinjam($uid, $limit_per_page, $start_index, $search_string);
			$total_records          = $this->Content_model->get_total_pinjam($uid);
			$data['pages']          = 'v_transaksi';
		}else{
			$data['list_transaksi'] = $this->Content_model->get_my_transactions_pendana($uid, $limit_per_page, $start_index, $search_string);
			$total_records          = $this->Content_model->get_total_pendana($uid);
			$data['pages']          = 'v_transaksi_pendana';
		}  
        
 
        if (is_array($total_records) && $total_records['itotal'] > 0) 
        {             
            $config['base_url']    = base_url() . 'transaksi/page';
            $config['total_rows']  = $total_records['itotal'];
            $config['per_page']    = $limit_per_page;
            $config["uri_segment"] = 3;
            // custom paging configuration
            $config['num_links']   = 2;
            $config['use_page_numbers']   = TRUE;
            $config['reuse_query_string'] = TRUE;
             
            $config['full_tag_open'] = '<ul class="pagination">';
            $config['full_tag_close'] = '</ul>';
             
            $config['first_link'] = 'First';
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
             
            $config['last_link'] = 'Last';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
             
            $config['next_link'] = '&raquo;';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
 
            $config['prev_link'] = '&laquo;';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
 
            $config['cur_tag_open'] = '<li><a style="background-color:#f0f8ff;">';
            $config['cur_tag_close'] = '</a></li>';
 
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
             
            $this->pagination->initialize($config);
             
            // build paging links
            $data["pagination"] = $this->pagination->create_links();
        }
		
		$this->load->view('template', $data);
	}

	
	
}