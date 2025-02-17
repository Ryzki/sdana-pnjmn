<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Business_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Master_model');
		$this->load->model('Datatables_model');

		// table
		$this->Master_model->get_tables($this);
	}

	function get_all_dt()
	{
		// ---- Get All data show as Json ----
		
		// variable initialization
		$search 		= "";
		$start 			= 0;
		$rows 			= 10;
		$iTotal 		= 0;
		$iFilteredTotal = 0;
		$_sql_where 	= array();
		$sql_where 		= '';
		$cols 			= array( "id_mod_type_business", "type_business_name ", "type_business_status", "");
		$sort 			= "desc";
		
		// get search value (if any)
		if (isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
			$search = strtoupper($this->db->escape_str($this->input->get('sSearch', TRUE)));

			$_sql_where[] = "
				(
					UCASE(type_business_name) LIKE '%".$search."%'
				)
			";
		}

		// limit
		$start 		= $this->Datatables_model->get_start();
		$rows 		= $this->Datatables_model->get_rows();
		// sort
		$sort 		= $this->Datatables_model->get_sort($cols);		
		$sort_dir 	= $this->Datatables_model->get_sort_dir();	
		        
        //running query		
		$sql = " 	SELECT count(0) as iTotal
					FROM {$this->mod_type_business}
				";

		$q = $this->db->query($sql);
		$iTotal = $q->row('iTotal');

		$q->free_result();

		if(count($_sql_where)>0) $sql_where = " WHERE ".implode(' AND ',$_sql_where);	

		$sql = " 	SELECT *
					FROM {$this->mod_type_business} 
					$sql_where
			    ";

		if($sort!='' && $sort_dir!='') $order = " ORDER BY $sort $sort_dir ";
		
		$query 	= $this->db->query($sql. $order. " LIMIT $start,$rows ");
		$data 	= $query->result();

		if( $search!='' ){
			$iFilteredTotal = count($query->result());
		}else{
			$iFilteredTotal = $iTotal;
		}
		
        //    * Output
         
         $output = array(
             "sEcho" => $this->Datatables_model->get_echo(),
             "iTotalRecords" => $iTotal,
             "iTotalDisplayRecords" => $iFilteredTotal,
             "aaData" => $data
         );

        $query->free_result();

		return json_encode($output);
	}

	public function insert_($data)
	{
		$data = $this->Master_model->escape_all($data);

		$this->db->insert($this->mod_type_business, $data);
		return $this->db->insert_id();
	}

	function get_data_byid($id)
	{
		$this->db->select('*');
		$this->db->from($this->mod_type_business);
		$this->db->where('id_mod_type_business', $id);
		$this->db->limit(1);
		$sql = $this->db->get();

		$ret = $sql->row_array();
		$sql->free_result();

		return $ret;
	}

	public function update_($data, $ID)
	{
		$this->db->where('id_mod_type_business', $ID);
		$this->db->update($this->mod_type_business, $data);
		return $this->db->affected_rows();
	}
	
	public function delete_($id)
	{
		return $this->db->delete($this->mod_type_business, array('id_mod_type_business'=>$id));
	}

	function get_active_business()
	{
		$this->db->select('*');
		$this->db->from($this->mod_type_business);
		$this->db->where('type_business_status', '1');
		$this->db->order_by('type_business_name', 'asc');
		$sql = $this->db->get();
		$ret = $sql->result_array();
		$sql->free_result();
		return $ret;
	}

	function checkif_exist($name, $id='')
	{
		$name = strtoupper($name);

		$this->db->select('*');
		$this->db->from($this->mod_type_business);
		$this->db->where('UCASE(type_business_name) = ', $name);

		if ($id!=''){
		$this->db->where('id_mod_type_business !=', $id);
		}

		$this->db->limit(1);
		$sql = $this->db->get();

		$ret = $sql->row_array();
		$sql->free_result();

		return $ret;
	}
}