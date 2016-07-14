<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registration extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('Registration_model');
	}
	public function index()
	{

		$data['uuid'] = uniqid();
		$this->load->view('registration',$data);
	}

	public function registration_list()
	{
		$list = $this->Registration_model->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $user) {
			$no++;
			$row = array();
			$row[] = $user->uuid;
			$row[] = $user->nama;
			$row[] = $user->alamat;
	

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_user('."'".$user->uuid."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_user('."'".$user->uuid."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Registration_model->count_all(),
						"recordsFiltered" => $this->Registration_model->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function user_add()
	{
		$this->_validate();
		$data = array(
				'uuid' => $this->input->post('uuid'),
				'nama' => $this->input->post('nama'),
				'alamat' => $this->input->post('alamat'),
			);
	
		$found = $this->Registration_model->find($this->input->post('nama'),'nama');
		// if ($found != "")
		// {
		// 	//echo json_encode(array("status" => FALSE));
		// }
		// else
		// {	
			$insert = $this->Registration_model->save($data);
			echo json_encode(array("status" => TRUE));	
		//}
	}

	public function user_edit($id)
	{
		$data = $this->Registration_model->get_by_id($id);
		echo json_encode($data);
	}

	public function user_update()
	{
		$this->_validate();
		$data = array(
				'uuid' => $this->input->post('uuid'),
				'nama' => $this->input->post('nama'),
				'alamat' => $this->input->post('alamat'),
			);
		$this->Registration_model->update(array('uuid' => $this->input->post('uuid')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function user_delete($id)
	{
		$this->Registration_model->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('nama') == '')
		{
			$data['inputerror'][] = 'nama';
			$data['error_string'][] = 'nama name is required';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}
}
