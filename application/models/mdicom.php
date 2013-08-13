<?php
Class Mdicom extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
	
	function addpatient($info)
	{
		$data = array(
			'PatientID' =>'',
			'TemporaryID'=>$info['id'],
			'PatientName' =>$info['name'],
			'PatientSex'=>$info['sex'],
			'PatientAge'=>$info['age'],
			'PatientEthnic'=>$info['ethnic'],
			'PatientNativePlace'=>$info['address'],
			'PatientHeight'=>$info['height'],
			'PatientWeight'=>$info['weight'],
			'PatientBirthDate'=>substr($info['birth'],0,4).'-'.substr($info['birth'],4,2).'-'.substr($info['birth'],6,2),
			'PatientDiagnosisInfo'=>$info['position']
		);
		
		$this->db->trans_start();
		$this->db->insert('patientinfo', $data); 
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		return  $insert_id;
	}
	
	function addscan($info)
	{
		$data = array(
			'ScanID' =>'',
			'Temporary_ID'=>$info['sop_id'],
			'ScanType' =>$info['modality'],
			'ScanDevice'=>$info['manufacturer']."-".$info['model_id'],
			'SequenceDescription'=>$info['position'],
			'ScanOperator'=>$info['operator'],
			'ScanAnatomy'=>$info['anatomy'],
			'Scandate'=>substr($info['appt_date'],0,4).'-'.substr($info['appt_date'],4,2).'-'.substr($info['appt_date'],6,2),
			'ImageRowCount'=>$info['row'],
			'ImageColumnCount'=>$info['coloumn'],
			'ImageSliceCount'=>$info['number'],
			'Patient_ID'=>$info['insertid']
		);
		
		$this->db->insert('scansequence', $data);
		return TRUE;
	}
	
	function isexist_patient($info)
	{
		$this -> db -> select('PatientID, PatientName, PatientBirthDate, TemporaryID');
		$this -> db -> from('patientinfo');
		$this -> db -> where('TemporaryID', $info['id']);
		$this -> db -> where('PatientName', $info['name']);
		$this -> db -> limit(1);
	   	$query = $this -> db -> get();

		if($query -> num_rows() == 1)
		{
			foreach($query->result() as $row)
			{
				$result = $row->PatientID;
			}
		 	return $result;
		}
		else
		{
		 return false;
		}
		
	}

	function isexist_scan($info)
	{
		$this -> db -> select('ScanID, Patient_ID, Scandate, Temporary_ID');
		$this -> db -> from('scansequence');
		$this -> db -> where('Temporary_ID', $info['sop_id']);
		$this -> db -> limit(1);
	   	$query = $this -> db -> get();
		if($query -> num_rows() == 1)
		{
			foreach($query->result() as $row)
			{
				$result = $row->ScanID;
			}
		 	return $result;
		}
		else
		{
			return FALSE;
		}
	}
	
	function insert_pa($info)
	{
		if($result_id = isexist_patient($info))
		{
			return $result_id;
		}
		else
		{
			return $result_id = addpatient($info);
		}
	}
	
	function insert_se($info)
	{
		if($result_id = isexist_scan($info))
		{
			return $result_id;
		}
		else
		{
			return $result_id = addscan($info);
		}		
	}
	
	function insert_sop($info)
	{
		$this -> db -> select('DcmID,ImportName');
		$this -> db -> from('dcminfo');
		$this -> db -> where('Sop_id', $info['sop_id']);
		$this -> db -> limit(1);
	   	$query = $this -> db -> get();
		if($query -> num_rows() == 1)
		{
			foreach($query->result() as $row)
			{
				$result = $row->DcmID;
			}
		 	return $result;
		}
		else
		{
		$session_data = $this->session->userdata('logged_in');	
		$data = array(
			'DcmID' =>'',
			'UserID'=>$session_data['id'],
			'ScanID' =>$info['scanid '],
			'Sop_id'=>$info['sop_id'],
			'ImportDate'=>date("Y-m-d h:i:s"),
			'rawname'=>$info['rawname'],
		);
		
		$this->db->trans_start();
		$this->db->insert('dcminfo', $data); 
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		return  $insert_id;		 
		}
		
	}
	
}
?>  