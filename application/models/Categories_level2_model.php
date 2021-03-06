<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categories_level2_model extends CI_Model
{
	var $column_search = array('CATG_MSTR.CATG_MSTR_CATEGORY_TYPE_ID','CATG_MSTR.CATG_MSTR_CATEGORY_ID','CATG_MSTR.CATG_MSTR_STATUS','CATG_MSTR.CATG_MSTR_CATEGORY_NAME','CATG_MSTR.CATG_MSTR_CREATED_BY','CATG_MSTR.CATG_MSTR_CREATED_TIME','USER_MSTR.USER_MSTR_USER_NAME','CATG_TYPE_MSTR.CATG_TYPE_MSTR_CATEGORY_TYPE','CATG_SUB_CATG_MAP.CATG_SUB_CATG_MAP_PARENT_CATEGORY_ID'); //set column field database for datatable searchable 
	function __construct()
    {
        parent::__construct();

    }
	
	private function _get_datatables_query($cond2)
	{
		 $this->db->select('CATG_MSTR.CATG_MSTR_CATEGORY_TYPE_ID,CATG_MSTR.CATG_MSTR_CATEGORY_ID,CATG_MSTR.CATG_MSTR_STATUS,CATG_MSTR.CATG_MSTR_CATEGORY_NAME,CATG_MSTR.CATG_MSTR_CREATED_BY,CATG_MSTR.CATG_MSTR_CREATED_TIME,CATG_MSTR.CATG_MSTR_MODIFIED_BY,CATG_MSTR.CATG_MSTR_MODIFIED_TIME,USER_MSTR.USER_MSTR_USER_NAME,CATG_TYPE_MSTR.CATG_TYPE_MSTR_CATEGORY_TYPE,CATG_SUB_CATG_MAP.CATG_SUB_CATG_MAP_PARENT_CATEGORY_ID');
	     $this->db->from('CATG_MSTR');
	     $this->db->join('CATG_TYPE_MSTR','CATG_TYPE_MSTR.CATG_TYPE_MSTR_CATEGORY_TYPE_ID=CATG_MSTR.CATG_MSTR_CATEGORY_TYPE_ID','left');
	     $this->db->join('USER_MSTR','USER_MSTR.USER_MSTR_USER_ID=CATG_MSTR.CATG_MSTR_CREATED_BY','left');
	     $this->db->join('CATG_SUB_CATG_MAP','CATG_SUB_CATG_MAP.CATG_SUB_CATG_MAP_CATEGORY_ID=CATG_MSTR.CATG_MSTR_CATEGORY_ID','left');
	     $this->db->where("CATG_MSTR.CATG_MSTR_CATEGORY_LEVEL='2'");
 	//	 $this->db->order_by('CATG_MSTR.CATG_MSTR_CATEGORY_ID');
	     		
		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}

		if($cond2)
             {
             	
                    $cond  = "CATG_MSTR.CATG_MSTR_STATUS='".$cond2."'";
                    
                    $this->db->where($cond);
             }
		
	}

	function get_datatables($cond2)
	{
		if($cond2=='')
		{

			$cond2='LIVE';
		}
		if($cond2=='ALL')
		{

			$cond2='';
		}
		$this->_get_datatables_query($cond2);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
	
	if(!empty($_POST['order']) && $_POST['order']['0']['dir'] == 'asc'){
		$order_by = "";
	}else{
		$order_by = "DESC";
	}
	if(!empty($_POST['order']) && ($_POST['order']['0']['column'] == 0 || $_POST['order']['0']['column'] == 2)){
		$order_data = "CATG_MSTR.CATG_MSTR_CATEGORY_ID ".$order_by;
	}
	if(!empty($_POST['order']) && ($_POST['order']['0']['column'] == 1 )){
		$order_data = "CATG_TYPE_MSTR.CATG_TYPE_MSTR_CATEGORY_TYPE ".$order_by;
	}
	if(!empty($_POST['order']) && ($_POST['order']['0']['column'] == 3 )){
		$order_data = "CATG_SUB_CATG_MAP.CATG_SUB_CATG_MAP_PARENT_CATEGORY_ID ".$order_by;
	}
	if(!empty($_POST['order']) && ($_POST['order']['0']['column'] == 4 )){
		$order_data = "CATG_MSTR.CATG_MSTR_CATEGORY_NAME ".$order_by;
	}
	if(!empty($_POST['order']) && ($_POST['order']['0']['column'] == 5 )){
		$order_data = "CATG_MSTR.CATG_MSTR_STATUS ".$order_by;
	}
	if(!empty($_POST['order']) && ($_POST['order']['0']['column'] == 6 )){
		$order_data = "CATG_MSTR.CATG_MSTR_CREATED_BY ".$order_by;
	}
	if(!empty($_POST['order']) && ($_POST['order']['0']['column'] == 7 )){
		$order_data = "CATG_MSTR.CATG_MSTR_CREATED_TIME ".$order_by;
	}
	$this->db->order_by($order_data);
		$query = $this->db->get();
		//echo $this->db->last_query(); die;
		
		return $query->result();
	}

	public function count_all($cond2)
	{    
		if($cond2=='')
		{

			$cond2='LIVE';
		}
		if($cond2=='ALL')
		{

			$cond2='';
		}
		$this->_get_datatables_query($cond2);
		if($cond2)
             {
             	    $cond  = "CATG_MSTR.CATG_MSTR_STATUS='".$cond2."'";

                    $this->db->where($cond);
             }
		return $this->db->count_all_results();
	}

	function count_filtered($cond2)
	{
		if($cond2=='')
		{

			$cond2='LIVE';
		}
		if($cond2=='ALL')
		{

			$cond2='';
		}
		$this->_get_datatables_query($cond2);
		$query = $this->db->get();
		return $query->num_rows();
	}

}
