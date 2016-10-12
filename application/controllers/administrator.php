<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administrator extends CI_Controller{
	
	private $title = '';
	private $manager = '';
	private $js = '';
	private $menu = '';
	private $content = '';
	
	public function __construct(){
		parent::__construct();
		
		$this->load->helper(array('url', 'form', 'html', 'captcha'));
		$this->load->library(array('session','form_validation'));
		$this->load->model('myModel');
	}
	
	public function index() {
		$this->home();
	}

	/* CREATE ADMIN USER
	 * ==========================================================================
	 */	
	/*public function createAdminUser(){
		$idnumber = '00444';
		$lastname = 'Ayawan';
		$firstname = 'Jaypee';
		$middlename = 'Es-esa';
		$extname = '';

		$person = array(
				'idnumber' => $idnumber,
				'lastname' => $lastname,
				'firstname' => $firstname,
				'middlename' => $middlename,
				'extname' => $extname,
				'username' => $idnumber,
				'password' => $this->hash($idnumber) 
			);
		$this->db->insert('person', $person);
		$personID = $this->db->insert_id();
		$admin = array(
				'person_personID' => $personID,
				'department_departmentID' => 
			);
		$this->db->insert('admin', $admin);
	}*/
	
	/* LOGIN
	 * ==========================================================================
	 */
	
	public function login() {
		if($this->loggedin()) $this->redirectTo('index');
	
		$data['title'] = $this->title = 'Log In';
		$data['content'] = $this->content = '';
		$data['manager'] = $this->manager();
		$rules = array(
				'username' => array(
						'field' => 'username',
						'label' => 'Username',
						'rules' => 'trim|required'
				),
				'password' => array(
						'field' => 'password',
						'label' => 'Password',
						'rules' => 'trim|required'
				)
				
		);
		$this->form_validation->set_rules($rules);
	
		if($this->form_validation->run() == FALSE){
			$this->load->view('login', $data);
		}else{
			$username = $this->input->post(htmlentities('username'));
			$password = $this->hash($this->input->post(htmlentities('password')));

			$sql = 'SELECT * FROM admin a
				JOIN person p ON a.person_personID = p.personID
				WHERE p.username = ? AND p.password = ? ';
			$query = $this->db->query($sql, array($username, $password));	
			if($query->num_rows() > 0){
				$row = $query->row();
				$personID = $row->personID;
	
				$CI =& get_instance();
				$CI->load->library('session');
				$data = array(
						base_url().''.strtolower(get_class($CI)).'/personID'  => $personID,
						base_url().''.strtolower(get_class($CI)).'/loggedin' => TRUE
				);
				$CI->session->set_userdata($data);
				redirect(base_url().''.strtolower(get_class($CI)).'/');
			}else{
				$data['content'] = $this->content .= '<div class="alert alert-default alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Login failed, Incorrect username and password combination.</div>';
			}
		}
		$this->load->view('login', $data);
	}
	/* LOGOUT
	 * =============================================================================
	 */
	 
	public function logout() {
		$this->session->sess_destroy();
		$this->redirectTo('login');
	}
	
	/* HOME
	 * =============================================================================
	 */
	 
	public function home() {
		if(!$this->loggedin()) $this->redirectTo('login');
	
		$this->title = 'Home';
		$this->manager = $this->manager();
		$this->js = 'home_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Home', 1, 'class="content-title"');
			$sql = 'SELECT * FROM admin a
				JOIN person p ON a.person_personID = p.personID
				JOIN department d ON a.department_departmentID = d.departmentID
				WHERE p.personID = ? ';
			$query = $this->db->query($sql, array($this->personID()));	
			foreach ($query->result_array() as $row):
				$this->content .= '<div class="well">';
					$this->content .= heading($row['idnumber'].' | '.$row['lastname'].', '.$row['firstname'].' '.$row['middlename'].' '.$row['extname'], 4);
					$this->content .= '<span class="label label-warning" id="deptTooltip">'.$row['department_title'].'</span>&nbsp;<span class="label label-primary" id="unameTooltip">'.$row['username'].'</span>';
				$this->content .= '</div>';
			endforeach;
		$this->content .='</div>';
	
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
	
		$this->load->view('index', $data);
	}
	
	/* DEPARTMENT MANAGEMENT
	 * ================================================================================
	 */	
	 
	public function departmentManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Department Management';
		$this->manager = $this->manager();
		$this->js = 'departmentManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Department Management', 1, 'class="content-title"');
		$this->content .= '<div id="jqxgrid"></div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);
	}
	
	public function departmentManagementJSON(){
		$sql = 'SELECT * FROM department ORDER BY department_code ASC';
		$query = $this->db->query($sql);
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
				'departmentID' => $row['departmentID'],
				'department_code' => $row['department_code'],
				'department_title' => $row['department_title']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}
	
	public function departmentManagementInsert(){
		$postData = $this->input->post('postData');
		$departmentCode = strtoupper($postData[0]);
		$departmentTitle = ucwords($postData[1]);
		
		$data = array(
			'department_code' => $departmentCode,
			'department_title' => $departmentTitle	
		);
		$this->db->insert('department', $data);
	}
	
	public function departmentManagementUpdate(){
		$postID = $this->input->post('postID');
		$postData = $this->input->post('postData');
		$departmentCode = strtoupper($postData[0]);
		$departmentTitle = ucwords($postData[1]);
	
		$data = array(
				'department_code' => $departmentCode,
				'department_title' => $departmentTitle
		);
		$this->db->where('departmentID', $postID);
		$this->db->update('department', $data);
	}
	
	public function departmentManagementDelete(){
		$postID = $this->input->post('postID');
		$this->db->delete('department', array('departmentID' => $postID));		
	}
	
	/* 	COURSE MANAGEMENT
	 * ================================================================================
	 */	

	public function courseManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Course Management';
		$this->manager = $this->manager();
		$this->js = 'courseManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Course Management', 1, 'class="content-title"');
		$this->content .= '<div id="jqxgrid"></div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);		
	}

	public function courseManagementJSON(){
		$sql = 'SELECT * FROM course c 
			JOIN department d ON c.department_departmentID = d.departmentID
			ORDER BY course_code ASC';
		$query = $this->db->query($sql);	
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
				'courseID' => $row['courseID'],
				'course_code' => $row['course_code'],
				'course_title' => $row['course_title'],
				'departmentID' => $row['departmentID'],
				'department_code' => $row['department_code']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}

	public function courseManagementInsert(){
		$postData = $this->input->post('postData');
		$c_code = htmlentities(strtoupper($postData[0]));
		$c_title = htmlentities(ucwords($postData[1]));
		$c_dept = htmlentities($postData[2]);

		$data = array(
			'course_code' => $c_code,
			'course_title' => $c_title,
			'department_departmentID' => $c_dept
		);
		$this->db->insert('course', $data);
	}

	public function courseManagementUpdate(){
		$postID = $this->input->post('postID');
		$postData = $this->input->post('postData');
		$c_code = htmlentities(strtoupper($postData[0]));
		$c_title = htmlentities(ucwords($postData[1]));
		$n_dept = htmlentities($postData[2]);
		$o_dept = htmlentities($postData[3]);

		$c_dept = (!empty($n_dept)) ? $n_dept : $o_dept;
		$data = array(
			'department_departmentID' => $c_dept,
			'course_code' => $c_code,
			'course_title' => $c_title
		);
		$this->db->where('courseID', $postID);
		$this->db->update('course', $data);		
	}

	public function courseManagementDelete(){
		$postID = $this->input->post('postID');
		$this->db->delete('course', array('courseID' => $postID));
	}

	/* CURRICULUM MANAGEMENT
	 * ================================================================================
	 */
	
	public function curriculumManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Curriculum Management';
		$this->manager = $this->manager();
		$this->js = 'curriculumManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Curriculum Management', 1, 'class="content-title"');
		
		$this->content .= '<div id="message"></div>';
		$this->content .= '<div id="jqxgrid"></div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);
	}
	
	public function curriculumManagementJSON(){
		$sql = 'SELECT * FROM curriculum cm
			JOIN course ce ON cm.course_courseID = ce.courseID
			JOIN department dt ON ce.department_departmentID = dt.departmentID
			ORDER BY ce.course_code ASC';
		$query = $this->db->query($sql);	
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
				'curriculumID' => $row['curriculumID'],
				'curriculumTitle' => $row['curriculumTitle'],
				'academicYear' => $row['academicYear'],
				'courseID' => $row['courseID'],
				'course_code' => $row['course_code'],
				'course_title' => $row['course_title'],
				'department_code' => $row['department_code']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");	
	}
	
	public function curriculumManagementInsert(){
		$postData = $this->input->post('postData');
		$curriculumTitle = htmlentities(ucwords($postData[0]));
		$ay = htmlentities($postData[1]);
		$c_course = $postData[2];
		$data = array(
				'course_courseID' => $c_course,
				'curriculumTitle' => $curriculumTitle,
				'academicYear' => $ay
		);
		$this->db->insert('curriculum', $data);
	}
	
	public function curriculumManagementUpdate(){
		$postID = $this->input->post('postID');
		$postData = $this->input->post('postData');
		$curriculumTitle = htmlentities(ucwords($postData[0]));	
		$academicYear = htmlentities($postData[1]);
		$o_course = $postData[2];
		$n_course = $postData[3];

		$course = (!empty($n_course)) ? $n_course : $o_course;
		$data = array(
				'course_courseID' => $course,
				'curriculumTitle' => $curriculumTitle,
				'academicYear' => $academicYear
		);
		$this->db->where('curriculumID', $postID);
		$this->db->update('curriculum', $data);
	}
	
	public function curriculumManagementDelete(){
		$postID = $this->input->post('postID');
		$this->db->delete('curriculum', array('curriculumID' => $postID));
	}

	/* EMPLOYEE MANAGEMENT
	 * ================================================================================
	 */	
	
	public function employeeManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Employee Management';
		$this->manager = $this->manager();
		$this->js = 'employeeManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Employee Management', 1, 'class="content-title"');
		$this->content .= '<div id="jqxgrid"></div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);
	}
	
	public function employeeManagementJSON(){
		$sql = 'SELECT * FROM department dt
			JOIN dean dn ON dt.departmentID = dn.department_departmentID
			JOIN person pn ON dn.person_personID = pn.personID';
		$query = $this->db->query($sql);	
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
				'personID' => $row['personID'],
				'idnumber' => $row['idnumber'],
				'lastname' => $row['lastname'],
				'firstname' => $row['firstname'],
				'middlename' => $row['middlename'],
				'extname' => $row['extname'],
				'departmentID' => $row['departmentID'],
				'department_code' => $row['department_code']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}
	
	public function employeeManagementInsert(){
		$postData = $this->input->post('postData');
		$idnumber = htmlentities($postData[0]);
		$lastname = htmlentities(ucfirst($postData[1]));
		$firstname = htmlentities(ucfirst($postData[2]));
		$middlename = htmlentities(ucfirst($postData[3]));
		$extname = htmlentities(ucfirst($postData[4]));
		$department = $postData[5];
		$password = $this->hash($idnumber);
		
		$personData = array(
			'idnumber' => $idnumber,
			'lastname' => $lastname,
			'firstname' => $firstname,
			'middlename' => $middlename,
			'extname' => $extname,
			'username' => $idnumber,
			'password' => $password	
		);
		$this->db->insert('person', $personData);

		$personID = $this->db->insert_id();
		$deanData = array(
			'person_personID' => $personID,
			'department_departmentID' => $department	
		);
		$this->db->insert('dean', $deanData);
	}
	
	public function employeeManagementUpdate(){
		$postID = $this->input->post('postID');
		$postData = $this->input->post('postData');
		$idnumber = htmlentities($postData[0]);
		$lastname = htmlentities(ucfirst($postData[1]));
		$firstname = htmlentities(ucfirst($postData[2]));
		$middlename = htmlentities(ucfirst($postData[3]));
		$extname = htmlentities(ucfirst($postData[4]));
		$newDepartment = $postData[5];
		$oldDepartment = $postData[6];
		
		$department = (!empty($newDepartment)) ? $newDepartment : $oldDepartment;
		$password = $this->hash($idnumber);
		
		$dataPerson = array(
			'idnumber' => $idnumber,
			'lastname' => $lastname,
			'firstname' => $firstname,
			'middlename' => $middlename,
			'extname' => $extname,
			'username' => $idnumber,
			'password' => $password
		);
		
		$this->db->where('personID', $postID);
		$this->db->update('person', $dataPerson);
		
		$this->db->set('department_departmentID', $department);
		$this->db->where('person_personID', $postID);
		$this->db->update('dean');
	}
	
	public function employeeManagementDelete(){
		$postID = $this->input->post('postID');
		$this->db->delete('dean', array('person_personID' => $postID));
		$this->db->delete('person', array('personID' => $postID));		
	}

	/* SC_ID MANAGEMENT
	 * ================================================================================
	 */	

	public function scidManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'SC ID Management';
		$this->manager = $this->manager();
		$this->js = 'scidManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('SC ID Management', 1, 'class="content-title"');
		$this->content .= '<div id="jqxgrid"></div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);
	}
	
	public function scidManagementJSON(){
		$sql = 'SELECT * FROM department d
			JOIN scid s ON d.departmentID = s.department_departmentID
			ORDER BY department_code ASC';
		$query = $this->db->query($sql);	
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
					'scidID' => $row['scidID'],
					'scid_start' => $row['scid_start'],
					'scid_end' => $row['scid_end'],
					'departmentID' => $row['departmentID'],
					'department_code' => $row['department_code']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}
	
	public function scidManagementInsert(){
		$postData = $this->input->post('postData');
		$start = htmlentities($postData[0]);
		$end = htmlentities(ucfirst($postData[1]));
		$department = $postData[2];
		
		$data = array(
			'department_departmentID' => $department,
			'scid_start' => $start,
			'scid_end' => $end
		);
		$this->db->insert('scid', $data);
	}
	
	public function scidManagementUpdate(){
		$posID = $this->input->post('postID');
		$postData = $this->input->post('postData');
		$start = htmlentities($postData[0]);
		$end = htmlentities(ucfirst($postData[1]));
		$newDepartment = $postData[2];
		$oldDepartment = $postData[3];
		
		$department = (!empty($newDepartment)) ? $newDepartment : $oldDepartment;
		$data = array(
			'department_departmentID' => $department,
			'scid_start' => $start,
			'scid_end' => $end
		);
		
		$this->db->where('scidID', $posID);
		$this->db->update('scid', $data);
	}
	
	public function scidManagementDelete(){
		$postID = $this->input->post('postID');
		$this->db->delete('scid', array('scidID' => $postID));	
	}

	/* SUBJECT MANAGEMENT
	 * ================================================================================
	 */	
	
	public function subjectManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Subject Management';
		$this->manager = $this->manager();
		$this->js = 'subjectManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Subject Management', 1, 'class="content-title"');
		$this->content .= '<div id="jqxgrid"></div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);		
	}
	
	public function subjectManagementJSON(){
		$sql = 'SELECT * FROM subject s
			JOIN departmentsubject ds ON s.subjectID = ds.subject_subjectID
			JOIN department d ON ds.department_departmentID = d.departmentID
			ORDER BY s.sc_code ASC';
		$query = $this->db->query($sql);	
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
				'subjectID' => $row['subjectID'],
				'sc_code' => $row['sc_code'],
				'subject_title' => $row['subject_title'],
				'units' => $row['units'],
				'lab' => $row['lab'],
				'lec' => $row['lec'],
				'departmentID' => $row['departmentID'],
				'department_code' => $row['department_code']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}
	
	public function subjectManagementInsert(){
		$postData = $this->input->post('postData');
		$sc_code = htmlentities(strtoupper($postData[0]));
		$subject_title = htmlentities(ucfirst($postData[1]));
		$units = htmlentities(ucfirst($postData[2]));
		$lab = htmlentities(ucfirst($postData[3]));
		$lec = htmlentities(ucfirst($postData[4]));
		$dept = htmlentities(ucfirst($postData[5]));
		
		$data = array(
			'sc_code' => $sc_code,
			'subject_title' => $subject_title,
			'units' => $units,
			'lab' => $lab,
			'lec' => $lec		
		);
		$this->db->insert('subject', $data);
		$deptSub = array(
			'department_departmentID' => $dept,
			'subject_subjectID' => $this->db->insert_id()
		);
		$this->db->insert('departmentsubject', $deptSub);
	}
	
	public function subjectManagementUpdate(){
		$postID = $this->input->post('postID');
		$postData = $this->input->post('postData');
		$sc_code = htmlentities(strtoupper($postData[0]));
		$subject_title = htmlentities(ucfirst($postData[1]));
		$units = htmlentities(ucfirst($postData[2]));
		$lab = htmlentities(ucfirst($postData[3]));
		$lec = htmlentities(ucfirst($postData[4]));
		$newDepartment = $postData[5];
		$oldDepartment = $postData[6];
		
		$department = (!empty($newDepartment)) ? $newDepartment : $oldDepartment;
		$data = array(
			'sc_code' => $sc_code,
			'subject_title' => $subject_title,
			'units' => $units,
			'lab' => $lab,
			'lec' => $lec
		);
		
		$this->db->where('subjectID', $postID);
		$this->db->update('subject', $data);

		$this->db->where('subject_subjectID', $postID);
		$this->db->set('department_departmentID', $department);
		$this->db->update('departmentsubject');
	}
	
	public function subjectManagementDelete(){
		$postID = $this->input->post('postID');
		$this->db->delete('departmentsubject', array('subject_subjectID' => $postID));
		$this->db->delete('subject', array('subjectID' => $postID));
	}

	/* DAYS MANAGEMENT
	 * ================================================================================
	 */	

	public function daysManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Start Time Management';
		$this->manager = $this->manager();
		$this->js = 'daysManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Days Management', 1, 'class="content-title"');
		$this->content .= '<div id="jqxgrid"></div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);			
	}

	public function daysManagementJSON(){
		$sql = 'SELECT * FROM day';
		$query = $this->db->query($sql);
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
				'dayID' => $row['dayID'],
				'day' => $row['day']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}

	public function insertDaysManagement(){
		$postData = $this->input->post('postData');
		$day = htmlentities(strtoupper($postData[0]));
		$data = array(
			'day' => $day
		);
		$this->db->insert('day', $data);	
	}

	public function deleteDaysManagement(){
		$postID = $this->input->post('postID');
		$this->db->delete('day', array('dayID' => $postID));
	}	

	/* TIME MANAGEMENT
	 * ================================================================================
	 */		
	
	public function timeManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Time Management';
		$this->manager = $this->manager();
		$this->js = 'timeManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Time Management', 1, 'class="content-title"');
		$this->content .= '<div id="jqxgrid"></div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);	
	}
	
	public function timeManagementJSON(){
		$sql = 'SELECT * FROM time ORDER BY stime ASC';
		$query = $this->db->query($sql);
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
				'timeID' => $row['timeID'],
				'stime' => $row['stime'],
				'etime' => $row['etime']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}

	public function insertTimeManagement(){
		$postData = $this->input->post('postData');
		$startTime = htmlentities($postData[0]);
		$entTime = htmlentities($postData[1]);
		
		$data = array(
			'stime' => $startTime,
			'etime' => $entTime
		);
		$this->db->insert('time', $data);	
	}

	public function deleteTimeManagement(){
		$postID = $this->input->post('postID');
		$this->db->delete('time', array('timeID' => $postID));
	}	

	/* CLASSROOM MANAGEMENT
	 * ================================================================================
	 */
	
	public function classroomManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Classroom Management';
		$this->manager = $this->manager();
		$this->js = 'classroomManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Classroom Management', 1, 'class="content-title"');
		$this->content .= '<div id="message"></div>';
		$this->content .= '<div id="jqxgrid"></div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);		
	}
	
	public function classroomManagementJSON(){
		$sql = 'SELECT * FROM room ORDER BY room ASC';
		$query = $this->db->query($sql);
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
				'roomID' => $row['roomID'],
				'room' => $row['room'],
				'description' => $row['description']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}

	public function classroomManagementInsert(){
		$postData = $this->input->post('postData');
		$room = htmlentities(strtoupper($postData[0]));
		$description = htmlentities(ucfirst($postData[1]));
		
		$data = array(
			'room' => $room,
			'description' => $description	
		);
		$this->db->insert('room', $data);
	}
	
	public function classroomManagementUpdate(){
		$postID = $this->input->post('postID');
		$postData = $this->input->post('postData');
		$room = htmlentities(strtoupper($postData[0]));
		$description = htmlentities(ucfirst($postData[1]));
		
		$data = array(
			'room' => $room,
			'description' => $description
		);
		$this->db->where('roomID', $postID);
		$this->db->update('room', $data);		
	}
	
	public function classroomManagementDelete(){
		$postID = $this->input->post('postID');
		$this->db->delete('room', array('roomID' => $postID));
	}
	
	/* SCHEDULE MANAGEMENT
	 * ================================================================================
	 */
	 
	public function scheduleManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Schedule Management';
		$this->manager = $this->manager();
		$this->js = 'scheduleManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Schedule Management', 1, 'class="content-title"');
		$this->content .= '<div class="col-xs-5" id="message"></div>';
		$this->content .= '<div id="jqxgrid"></div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);		
	}
	
	public function scheduleManagementJSON(){
		$sql = 'SELECT * FROM room ORDER BY room ASC';
		$query = $this->db->query($sql);
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
				'roomID' => $row['roomID'],
				'room' => $row['room'],
				'description' => $row['description']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}

	public function scheduleManagementScheduleInitJSON($roomID){
		$sql = 'SELECT * FROM room r
			JOIN departmentsubjectschedule dss ON r.roomID = dss.room_roomID
			JOIN day d ON dss.day_dayID = d.dayID
			JOIN time t ON dss.time_timeID = t.timeID
			JOIN subjectoffered so ON dss.subjectoffered_subjectOfferedID = so.subjectOfferedID
			JOIN subjectcurriculum sc ON so.subjectcurriculum_subjectCurriculumID = sc.subjectCurriculumID
			JOIN departmentsubject ds ON sc.departmentsubject_departmentSubjectID = ds.departmentSubjectID
			JOIN subject s ON ds.subject_subjectID = s.subjectID
			JOIN department dt ON ds.department_departmentID = dt.departmentID
			JOIN curriculum c ON sc.curriculum_curriculumID = c.curriculumID
			WHERE r.roomID = ? ';
		$query = $this->db->query($sql, array($roomID));
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
				'departmentSubjectScheduleID' => $row['departmentSubjectScheduleID'],
				'generated' => $row['generated'],
				'semester' => $row['semester'],
				'day' => $row['day'],
				'time' => $row['stime'].'-'.$row['etime'],
				'subjectID' => $row['subjectID'],
				'sc_id' => $row['sc_id'],
				'sc_code' => $row['sc_code'],
				'subject_title' => $row['subject_title'],
				'department_code' => $row['department_code']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}
	
	public function scheduleManagementInsert(){
		$postData = $this->input->post('postData');
		$days = $this->input->post('days');
		$roomID = $postData[0];
		$subjectID = $postData[1];
		$timeID = $postData[2];
		$generated = $postData[3];
		
		$sql = 'SELECT * FROM departmentsubjectschedule WHERE day_dayID = ? AND room_roomID = ?';
		$q = $this->db->query($sql, array($days, $roomID));
		
		if($q->num_rows() < 1){
			$data = array(
				'room_roomID' => $roomID,
				'day_dayID' => $days,
				'time_timeID' => $timeID,
				'subjectoffered_subjectOfferedID' => $subjectID,
				'generated' => $generated
			);
			$this->db->insert('departmentsubjectschedule', $data);
			echo '<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<i class="fa fa-check" aria-hidden="true"></i> Successful.</div>';
			
		}else{
			$sql = 'SELECT * FROM departmentsubjectschedule dss
				JOIN room r ON dss.room_roomID = r.roomID
				JOIN day d ON dss.day_dayID = d.dayID
				JOIN time t ON dss.time_timeID = t.timeID
				WHERE d.dayID = ? AND r.roomID = ?';
			$query = $this->db->query($sql, array($days, $roomID));
		
			$startTime = $this->startTime($timeID);
			$endTime = $this->endTime($timeID);

			foreach($query->result_array() as $row):
				$existingStimes = strtotime($row['stime']);
				$existingEtimes = strtotime($row['etime']);	
				
				if($startTime == $existingStimes){
					echo '<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span> Conflict schedule.</div>';
					break;
				}elseif($startTime > $existingStimes && $startTime < $existingEtimes){
					echo '<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span> Conflict schedule.</div>';
					break;
				}else{
					$data = array(
						'room_roomID' => $roomID,
						'day_dayID' => $days,
						'time_timeID' => $timeID,
						'subjectoffered_subjectOfferedID' => $subjectID,
						'generated' => $generated
					);
					$this->db->insert('departmentsubjectschedule', $data);
					echo '<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<i class="fa fa-check" aria-hidden="true"></i> Successful.</div>';
					continue;
				}
			endforeach;	
		}
	}
	
	public function scheduleManagementDelete(){
		$postID = $this->input->post('postID');
		$this->db->delete('departmentsubjectschedule', array('departmentSubjectScheduleID' => $postID));
	}
	
	/* REPORTS
	 * (classroom schedule summary)
	 * (subject offered summary)
	 * (logs result summary)
	 * ================================================================================
	 */

	public function classroomScheduleSummary(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$roomID = (($this->uri->segment(3) > 0) ? $this->uri->segment(3) : null );
		$dayID = (($this->uri->segment(4) > 0) ? $this->uri->segment(4) : null );
		$departmentID = (($this->uri->segment(5) > 0) ? $this->uri->segment(5) : null );
		
		$this->title = 'Reports';
		$this->manager = $this->manager();
		$this->js = 'classroomScheduleSummary_js';
		$this->menu = $this->menu();
		$this->content = heading('Classroom Schedule Summary', 1, 'class="content-title"');
		$this->content .= '<div class="col-xs-10 content-container contentWrapper">';

		$this->content .= '<div class="panel panel-default">';
			$this->content .= '<div class="panel-body">';
			$this->content .= '<p class="instruction">*Search Record.</p>';
			$room = $this->db->query('SELECT * FROM room ORDER BY room ASC');
			$this->content .= '<label>Classroom*'.nbs(2).':'.nbs(2).'</label>';
			$this->content .= '<select id="selectedRoom">';
				$this->content .= '<option value = "0">All</option>';
				foreach($room->result_array() as $row){
					$this->content .= '<option value = "'.$row['roomID'].'" '.(($roomID == $row['roomID']) ? 'selected="selected"' : null).'>'.$row['room'].' - '.$row['description'].'</option>';
				}
			$this->content .= '</select>';
			$day = $this->db->query('SELECT * FROM day');
			$this->content .= '<label>Day*'.nbs(2).':'.nbs(2).'</label>';
			$this->content .= '<select id="selectedDay">';
				$this->content .= '<option value = "0">All</option>';
				foreach($day->result_array() as $row){
					$this->content .= '<option value = "'.$row['dayID'].'" '.(($dayID == $row['dayID']) ? 'selected="selected"' : null).'>'.$row['day'].'</option>';
				}
			$this->content .= '</select>';
			$department = $this->db->query('SELECT * FROM department ORDER BY department_code ASC');
			$this->content .= '<label>Department*'.nbs(2).':'.nbs(2).'</label>';
			$this->content .= '<select id="selectedDepartment">';
				$this->content .= '<option value = "0">All</option>';
				foreach($department->result_array() as $row){
					$this->content .= '<option value = "'.$row['departmentID'].'" '.(($departmentID == $row['departmentID']) ? 'selected="selected"' : null).'>'.$row['department_code'].'</option>';
				}
			$this->content .= '</select>';
			$this->content .= '<input id="searchButton" type="button" value="Search"/>';
			$this->content .= '</div>';		
		$this->content .= '</div>';
	
		$this->content .= '<div id="printBody">';
			$this->content .= '<div class="table-responsive">';	
				$this->content .= heading(($this->uri->segment(3) > 0 ) ? $this->classroom($roomID) : 'All Classroom', 4).br();
				$this->content .= heading(($this->uri->segment(5) > 0 ) ? $this->department($departmentID) : 'All Department', 4).br();
				$this->content .= '<table>';
					$this->content .= '<thead>';
						$this->content .= '<tr>';
							$this->content .= '<th>Classroom</th>';
							$this->content .= '<th>Description</th>';
							$this->content .= '<th>SC ID</th>';
							$this->content .= '<th>SC CODE</th>';
							$this->content .= '<th>Descriptive Title</th>';
							$this->content .= '<th>Days</th>';
							$this->content .= '<th>Time</th>';
						$this->content .= '</tr>';
					$this->content .= '</thead>';
					$this->content .= '<tbody>';

					$this->db->select('*');
					$this->db->from('room r');
					$this->db->join('departmentsubjectschedule dss', 'dss.room_roomID = r.roomID');
					$this->db->join('day d', 'd.dayID = dss.day_dayID');
					$this->db->join('time t', 't.timeID = dss.time_timeID');
					$this->db->join('subjectoffered so', 'so.subjectOfferedID = dss.subjectoffered_subjectOfferedID');
					$this->db->join('subjectcurriculum sc', 'sc.subjectCurriculumID = so.subjectcurriculum_subjectCurriculumID');
					$this->db->join('departmentsubject ds', 'ds.departmentSubjectID = sc.departmentsubject_departmentSubjectID');
					$this->db->join('subject s', 's.subjectID = ds.subject_subjectID');
					$this->db->join('department dpt', 'dpt.departmentID = ds.department_departmentID');
					$this->db->join('curriculum c', 'c.curriculumID = sc.curriculum_curriculumID');
					(($roomID > 0)? $this->db->where('r.roomID', $roomID) : null);
					(($dayID > 0)? $this->db->where('d.dayID', $dayID) : null);
					(($departmentID > 0)? $this->db->where('dpt.departmentID', $departmentID) : null);
					$query = $this->db->get();
					
					if($query->num_rows() < 1){
						$this->content .= '<tr><td colspan="7">No Search Result Found.</td></tr>';
					}else{
						foreach ($query->result_array() as $row):
							$this->content .= '<tr>';
								$this->content .= '<td>'.$row['room'].'</td>';
								$this->content .= '<td>'.$row['description'].'</td>';
								$this->content .= '<td>'.$row['sc_id'].'</td>';
								$this->content .= '<td>'.$row['sc_code'].'</td>';
								$this->content .= '<td>'.$row['subject_title'].'</td>';
								$this->content .= '<td>'.$row['day'].'</td>';
								$this->content .= '<td>'.$row['stime'].' - '.$row['etime'].'</td>';
							$this->content .= '</tr>';
						endforeach;
					}
					$this->content .= '</tbody>';
				$this->content .= '</table>';
			$this->content .= '</div>';	
		$this->content .= '</div>';	
			
		//$this->content .= '<input id="printButton" type="button" value="Print"/>';	
			
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);	
	}	
	
	public function subjectOfferedSummary(){
		if(!$this->loggedin()) $this->redirectTo('login');

		$departmentID = (($this->uri->segment(3) > 0) ? $this->uri->segment(3) : null );
		
		$this->title = 'Reports';
		$this->manager = $this->manager();
		$this->js = 'subjectOfferedSummary_js';
		$this->menu = $this->menu();
		$this->content = heading('Subject Offered Summary', 1, 'class="content-title"');
		$this->content .= '<div class="col-xs-10 content-container contentWrapper">';

		$this->content .= '<div class="panel panel-default">';
			$this->content .= '<div class="panel-body">';
			$this->content .= '<p class="instruction">*Search Record.</p>';
			$department = $this->db->query('SELECT * FROM department ORDER BY department_code ASC');
			$this->content .= '<label>Department*'.nbs(2).':'.nbs(2).'</label>';
			$this->content .= '<select id="selectedDepartment">';
				$this->content .= '<option value = "0">All</option>';
				foreach($department->result_array() as $row){
					$this->content .= '<option value = "'.$row['departmentID'].'" '.(($departmentID == $row['departmentID']) ? 'selected="selected"' : null).'>'.$row['department_code'].'</option>';
				}
			$this->content .= '</select>';
			$this->content .= '<input id="searchButton" type="button" value="Search"/>';
			$this->content .= '</div>';		
		$this->content .= '</div>';
	
		$this->content .= '<div id="printBody">';
			$this->content .= '<div class="table-responsive">';	
				$this->content .= heading(($this->uri->segment(3) > 0 ) ? $this->department($departmentID) : 'All Department', 4).br();
				$this->content .= '<table>';
					$this->content .= '<thead>';
						$this->content .= '<tr>';
							$this->content .= '<th>SC ID</th>';
							$this->content .= '<th>SC CODE</th>';
							$this->content .= '<th>Descriptive Title</th>';
							$this->content .= '<th>Units</th>';
							$this->content .= '<th>Lec</th>';
							$this->content .= '<th>Lab</th>';
						$this->content .= '</tr>';
					$this->content .= '</thead>';
					$this->content .= '<tbody>';

					$this->db->select('*');
					$this->db->from('subjectoffered so');
					$this->db->join('subjectcurriculum sc', 'sc.subjectCurriculumID = so.subjectcurriculum_subjectCurriculumID');
					$this->db->join('departmentsubject ds', 'ds.departmentSubjectID = sc.departmentsubject_departmentSubjectID');
					$this->db->join('subject s', 's.subjectID = ds.subject_subjectID');
					$this->db->join('department dpt', 'dpt.departmentID = ds.department_departmentID');
					$this->db->join('curriculum c', 'c.curriculumID = sc.curriculum_curriculumID');

					(($departmentID > 0)? $this->db->where('dpt.departmentID', $departmentID) : null);
					
					$this->db->order_by('dpt.department_code', 'ASC');
					$query = $this->db->get();
					
					if($query->num_rows() < 1){
						$this->content .= '<tr><td colspan="6">No Search Result Found.</td></tr>';
					}else{
						foreach ($query->result_array() as $row):
							$this->content .= '<tr>';
								$this->content .= '<td>'.$row['sc_id'].'</td>';
								$this->content .= '<td>'.$row['sc_code'].'</td>';
								$this->content .= '<td>'.$row['subject_title'].'</td>';
								$this->content .= '<td>'.$row['units'].'</td>';
								$this->content .= '<td>'.$row['lec'].'</td>';
								$this->content .= '<td>'.$row['lab'].'</td>';
							$this->content .= '</tr>';
						endforeach;
					}
					$this->content .= '</tbody>';
				$this->content .= '</table>';
			$this->content .= '</div>';	
		$this->content .= '</div>';	
			
		//$this->content .= '<input id="printButton" type="button" value="Print"/>';	
			
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);		
	}

	public function logsResultsSummary(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$departmentID = (($this->uri->segment(3) > 0) ? $this->uri->segment(3) : 0 );
		
		$this->load->library(array('pagination'));
		$config['base_url'] = base_url() .''. $this->manager().'/logsResultsSummary/'.$departmentID.'/';
		$config['total_rows'] = $this->myModel->findAllLogs();
		$config['per_page'] = 20;
		$config['first_link'] = 'First';
		$config['last_link'] = 'Last';
		$config['num_links'] = 2;

		$this->pagination->initialize($config);
	
		$this->title = 'Reports';
		$this->manager = $this->manager();
		$this->js = 'logsResultsSummary_js';
		$this->menu = $this->menu();		
		$this->content .= heading('Logs Results Summary', 1, 'class="content-title"');
		$this->content .= '<div class="col-xs-10 content-container contentWrapper">';
		
		$this->content .= '<div class="panel panel-default">';
			$this->content .= '<div class="panel-body">';
			$this->content .= '<p class="instruction">*Search Record.</p>';
			$department = $this->db->query('SELECT * FROM department ORDER BY department_code ASC');
			$this->content .= '<label>Department*'.nbs(2).':'.nbs(2).'</label>';
			$this->content .= '<select id="selectedDepartment">';
				$this->content .= '<option value = "0">All</option>';
				foreach($department->result_array() as $row):
					$this->content .= '<option value = "'.$row['departmentID'].'" '.(($departmentID == $row['departmentID']) ? 'selected="selected"' : null).'>'.$row['department_code'].'</option>';
				endforeach;
			$this->content .= '</select>';
			$this->content .= '<input id="searchButton" type="button" value="Search"/>';
			$this->content .= '</div>';		
		$this->content .= '</div>';
		
		$this->content .= '<div class="table-responsive">';
			$this->content .= heading(($this->uri->segment(3) > 0 ) ? $this->department($departmentID) : 'All Department', 4).br();
			$this->content .= '<table>';
				$this->content .= '<thead>';
					$this->content .= '<tr>';
						$this->content .= '<th>ID NO</th>';
						$this->content .= '<th>Lastname</th>';
						$this->content .= '<th>Firstname</th>';
						$this->content .= '<th>Middlename</th>';
						$this->content .= '<th>Action</th>';
						$this->content .= '<th>Date</th>';
					$this->content .= '</tr>';
				$this->content .= '</thead>';
				$this->content .= '<tbody>';
					$this->db->select('*')
					->from('logs l')
					->join('dean d', 'd.deanID = l.dean_deanID')
					->join('person p', 'p.personID = d.person_personID')
					->join('department dpt', 'dpt.departmentID = d.department_departmentID');
					($departmentID > 0) ? $this->db->where('dpt.departmentID', $departmentID) : null ;
	
					$query = $this->db->get('',$config['per_page'], $this->uri->segment(4));
						
					if($query->num_rows() < 1){
						$this->content .= '<tr><td colspan = "6">No Search Results Found.</td></tr>';
					}else{	
						foreach($query->result_array() as $row):
							$this->content .= '<tr>';
								$this->content .= '<td>'.$row['idnumber'].'</td>';
								$this->content .= '<td>'.$row['lastname'].'</td>';
								$this->content .= '<td>'.$row['firstname'].'</td>';
								$this->content .= '<td>'.$row['middlename'].'</td>';
								$this->content .= '<td>'.$row['action'].'</td>';
								$this->content .= '<td>'.$row['date'].'</td>';
							$this->content .= '</tr>';
						endforeach;
					}
				$this->content .= '</tbody>';
			$this->content .= '</table>';
		$this->content .= '</div>';
		$this->content .= '<div id="pagination">';
			$this->content .= '<ul class="pager">';
				$this->content .= '<li>'.$this->pagination->create_links().'</li>';
			$this->content .= '</ul>';
		$this->content .= '</div>';
		
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);		
	}
	
	/* GENERAL SETTINGS MANAGEMENT
	 * (my password settings management)
	 * (users account settings management)
	 * ================================================================================
	 */
	
	public function myPasswordSettingsManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'General Settings';
		$this->manager = $this->manager();
		$this->js = 'generalSettings_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('My Password Settings', 1, 'class="content-title"');

		$rules = array(
			'username' => array(
					'field' => 'username',
					'label' => 'Username',
					'rules' => 'trim|required'
			),
			'newPassword' => array(
					'field' => 'newPassword',
					'label' => 'New Password',
					'rules' => 'trim|required|min_length[8]'
			),
			'verifyPassword' => array(
					'field' => 'verifyPassword',
					'label' => 'Verify Password',
					'rules' => 'trim|required|matches[newPassword]'
			)
		);
		$this->form_validation->set_rules($rules);
		
		if($this->form_validation->run($rules) == FALSE){
			$this->content .= '<div class="col-xs-9 alert alert-warning" role="alert"> <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>All fields are required'.validation_errors().'</div>';
		}else {
			$username = $this->input->post('username');
			$password = $this->input->post('verifyPassword');
			
			$sql = 'SELECT * FROM person WHERE username = ? AND personID = ?';
			$query = $this->db->query($sql, array($username, $this->personID()));
			
			if($query->num_rows() > 0) {
				$data = array('password' => $this->hash($password));
				$this->db->where('personID', $this->personID());
				$this->db->update('person', $data);

				$this->content .= '<div class="col-xs-9 alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><span class="glyphicon glyphicon-info-sign"></span> Successfully changed password.</div>';
			}else{
				$this->content .= '<div class="col-xs-9 alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><span class="glyphicon glyphicon-info-sign"></span> Incorrect username.</div>';
			}
		}
		
		$this->content .= '<div class="col-xs-9 well">';
			$this->content .= form_open('', 'class="form-horizontal", id="form" role="form"');
				$this->content .= '<div class="form-group">';
					$this->content .= '<label for="username" class="col-md-3 control-label">Username : </label>';
					$this->content .= '<div class="col-md-6">';
						$this->content .=  form_input('username','','class="form-control", id="username"');
					$this->content .= '</div>';
				$this->content .= '</div>';//
				$this->content .= '<div class="form-group">';
					$this->content .= '<label for="newPassword" class="col-md-3 control-label">New Password : </label>';
					$this->content .= '<div class="col-md-6">';
						$this->content .=  form_password('newPassword','','class="form-control", id="newPassword"');
					$this->content .= '</div>';
				$this->content .= '</div>';//
				$this->content .= '<div class="form-group">';
					$this->content .= '<label for="verifyPassword" class="col-md-3 control-label">Verify Password : </label>';
					$this->content .= '<div class="col-md-6">';
						$this->content .=  form_password('verifyPassword','','class="form-control", id="verifyPassword"');
					$this->content .= '</div>';
				$this->content .= '</div>';//
				$this->content .= '<div class="form-group">';
					$this->content .= '<label for="verifyPassword" class="col-md-3 control-label"></label>';
					$this->content .= '<div class="col-md-6">';
						$this->content .=  form_submit('button', 'Submit', 'class="btn btn-sm btn-primary", id="submit"');
					$this->content .= '</div>';
				$this->content .= '</div>';//
			$this->content .= form_close();
		$this->content .= '</div>';

		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);
	}
	 
	public function usersSettingsManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'General Settings';
		$this->manager = $this->manager();
		$this->js = 'userSettingsManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('User Settings Management', 1, 'class="content-title"');
		$this->content .= '<div id="jqxgrid"></div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);
	}
	
	public function accountSettingsManagementJSON(){
		$sql = 'SELECT * FROM department dt
			JOIN dean dn ON dt.departmentID = dn.department_departmentID
			JOIN person pn ON dn.person_personID = pn.personID
			ORDER BY idnumber ASC';
		$query = $this->db->query($sql);	
		$arr = array();
		foreach ($query->result_array() as $row){
			$arr[] = array(
					'personID' => $row['personID'],
					'idnumber' => $row['idnumber'],
					'lastname' => $row['lastname'],
					'firstname' => $row['firstname'],
					'middlename' => $row['middlename'],
					'extname' => $row['extname'],
					'username' => $row['username'],
					'department_code' => $row['department_code']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");	
	}
	
	public function accountSettingsManagementReset(){
		$postData = $this->input->post('postData');
		$plID = $postData[0];
		$plNumber = $postData[1];	
		$plpword = $this->hash($plNumber);
		$data = array(
				'username' => $plNumber,
				'password' => $plpword
		);
		$this->db->where('personID', $plID);
		$this->db->update('person', $data);
	}

	/* DROPDOWN JSON
	 * ================================================================================
	 */
	 
	public function departmentDropdownJSON(){
		$this->myModel->departmentDropdownJSON();			
	}
	public function courseDropdownJSON(){
		$this->myModel->courseDropdownJSON();
	}
	public function dayDropdownJSON(){
		$this->myModel->dayDropdownJSON();	
	}
	public function timeDropdownJSON(){
		$this->myModel->timeDropdownJSON();	
	}	
	public function subjectDropdownJSON(){
		$this->myModel->subjectDropdownJSON();
	}
	
	/* PRIVATE FUNCTIONS
	 * ================================================================================
	 */	
	
	private function manager() {
		return strtolower(get_class());
	}
	private function menu() {
		return 'inc/admin';
	}
	private function personID(){
		return $this->session->userdata(base_url().''.$this->manager().'/personID');
	}
	private function loggedin() {
		return $this->session->userdata(base_url().''.$this->manager().'/loggedin');
	}
	private function hash($string) {
		return $this->myModel->hash($string);
	}
	private function redirectTo($to) {
		redirect($this->manager().'/'.$to);
	}
	private function startTime($timeID){
		$sql = 'SELECT * FROM time WHERE timeID = ? ';
		$q = $this->db->query($sql, array($timeID));
		$row = $q->row();
		return strtotime($row->stime);
	}
	private function endTime($timeID){
		$sql = 'SELECT * FROM time WHERE timeID = ? ';
		$q = $this->db->query($sql, array($timeID));
		$row = $q->row();
		return strtotime($row->etime);
	}
	private function department($departmentID){
		$sql = 'SELECT * FROM department WHERE departmentID = ?';
		$q = $this->db->query($sql, array($departmentID));
		$row = $q->row();
		return $row->department_code .' | '.$row->department_title;
	}
	private function classroom($roomID){
		$sql = 'SELECT * FROM room WHERE roomID = ? ';
		$q = $this->db->query($sql, array($roomID));
		$row = $q->row();
		return $row->room .' - '.$row->description;
	}
}
