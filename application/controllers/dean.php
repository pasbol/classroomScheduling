<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dean extends CI_Controller{

	private $title = '';
	private $manager = '';
	private $js = '';
	private $menu = '';
	private $content = '';
	
	public function __construct(){
		parent::__construct();
		$this->load->helper(array('url', 'form', 'html'));
		$this->load->library(array('session','form_validation'));	
		$this->load->model('myModel');
	}
	/* INDEX
	 * ==========================================================================
	 */	
	public function index() {
		$this->home();
		$this->myModel->actionLogs($this->deanID(), 'Logged In' , Date('Y-m-d h:i:s'));
	}
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
			
			$q = $this->db->select('*')
			->from('dean d')
			->join('person p', 'p.personID = d.person_personID')
			->join('department dt', 'dt.departmentID = d.department_departmentID')
			->where('username', $username)
			->where('password', $password)
			->get();
	
			if($q->num_rows() > 0){
				$row = $q->row();
				$personID = $row->personID;
				$departmentID = $row->departmentID;
				$CI =& get_instance();
				$CI->load->library('session');
				$data = array(
						base_url().''.strtolower(get_class($CI)).'/personID'  => $personID,
						base_url().''.strtolower(get_class($CI)).'/departmentID'  => $departmentID,
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
		$this->myModel->actionLogs($this->deanID(), 'Logged Out' , Date('Y-m-d h:i:s'));
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
			$q = $this->db->select('*')
			->from('dean d')
			->join('person p', 'p.personID = d.person_personID')
			->join('department dt', 'dt.departmentID = d.department_departmentID')
			->where('personID', $this->personID())
			->get();
			foreach ($q->result_array() as $row):
				$this->content .= '<div class="well">';
					$this->content .= heading($row['idnumber'].' | '.$row['lastname'].', '.$row['firstname'].' '.$row['middlename'].' '.$row['extname'], 4);
					$this->content .= '<span class="label label-warning">'.$row['department_title'].'</span>'.nbs(2).'<span class="label label-primary">'.$row['username'].'</span>';
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
	/* SUBJECT MANAGEMENT
	 * ==========================================================================
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
		$q = $this->db->select('*')
		->from('subject s')
		->join('departmentsubject ds', 'ds.subject_subjectID = s.subjectID')
		->join('department d', 'd.departmentID = ds.department_departmentID')
		->where('departmentID', $this->departmentID())
		->get();
		
		$arr = array();
		foreach ($q->result_array() as $row){
			$arr[] = array(
					'subjectID' => $row['subjectID'],
					'sc_code' => $row['sc_code'],
					'subject_title' => $row['subject_title'],
					'units' => $row['units'],
					'lab' => $row['lab'],
					'lec' => $row['lec']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}
	
	public function subjectManagementInsert(){
		$postData = $this->input->post('postData');
		$sc_code = htmlentities(strtoupper($postData[0]));
		$subject_title = htmlentities(ucfirst($postData[1]));
		$units = htmlentities($postData[2]);
		$lab = htmlentities($postData[3]);
		$lec = htmlentities($postData[4]);
		
		$q = $this->db->select('*')
		->from('subject')
		->where('sc_code', $sc_code)
		->get();

		if($q->num_rows() > 0){
			echo 'exist';
		}elseif(empty($sc_code) || empty($subject_title) || empty($units) || empty($lab) || empty($lec)){	
			echo 'empty';
		}else{
			$subjectData = array(
				'sc_code' => $sc_code,
				'subject_title' => $subject_title,
				'units' => $units,
				'lab' => $lab,
				'lec' => $lec		
			);
			$this->db->insert('subject', $subjectData);
			$departmentSubjectData = array(
				'department_departmentID' => $this->departmentID(),
				'subject_subjectID' => $this->db->insert_id()
			);
			$this->db->insert('departmentsubject', $departmentSubjectData);
			$this->myModel->actionLogs($this->deanID(), 'Subject Insert' , Date('Y-m-d h:i:s'));
		}
	}
	
	public function subjectManagementUpdate(){
		$postID = $this->input->post('postID');
		$postData = $this->input->post('postData');
		$sc_code = htmlentities(strtoupper($postData[0]));
		$subject_title = htmlentities(ucfirst($postData[1]));
		$units = htmlentities($postData[2]);
		$lab = htmlentities($postData[3]);
		$lec = htmlentities($postData[4]);
		
		if(empty($sc_code) || empty($subject_title) || empty($units) || empty($lab) || empty($lec)){
			echo 'error';
		}else{
			$data = array(
					'sc_code' => $sc_code,
					'subject_title' => $subject_title,
					'units' => $units,
					'lab' => $lab,
					'lec' => $lec
			);
			$this->db->where('subjectID', $postID);
			$this->db->update('subject', $data);
			$this->myModel->actionLogs($this->deanID(), 'Subject Update' , Date('Y-m-d h:i:s'));
		}
	}
	
	public function subjectManagementDelete(){
		$postID = $this->input->post('postID');
		
		$this->myModel->deleteClassroomSchedule($postID);
		$this->myModel->deleteSubjectOffered($postID);
		$this->myModel->deleteSubjectCurriculum($postID);
		$this->myModel->deleteDepartmentSubject($postID);
		$this->myModel->deleteSubject($postID);
		$this->myModel->actionLogs($this->deanID(), 'Deleted Subject' , Date('Y-m-d h:i:s'));
	}
	/* CURRICULUM SUBJECT MANAGEMENT
	 * ==========================================================================
	 */	
	public function subjectCurriculumManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Subject Curriculum Management';
		$this->manager = $this->manager();
		$this->js = 'subjectCurriculumManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Subject Curriculum Management', 1, 'class="content-title"');
		$this->content .= '<div id="jqxgrid"></div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);		
	}

	public function subjectCurriculumManagementJSON(){
		$q = $this->db->select('*')
		->from('curriculum cm')
		->join('course ce', 'ce.courseID = cm.course_courseID')
		->join('department dt', 'dt.departmentID = ce.department_departmentID')
		->where('departmentID', $this->departmentID())
		->order_by('academicYear', 'DESC')
		->get();
		
		$arr = array();
		foreach ($q->result_array() as $row){
			$arr[] = array(
					'curriculumID' => $row['curriculumID'],
					'curriculumTitle' => $row['curriculumTitle'],
					'academicYear' => $row['academicYear']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");	
	}

	public function subjectCurriculumManagementAddSubject(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$curriculumID = $this->uri->segment(3);
		$yearLevel = ((is_null($this->uri->segment(4)) || $this->uri->segment(4) == 0 ) ? 'No selected year level' : $this->uri->segment(4));
		$semester = ((is_null($this->uri->segment(5)) || $this->uri->segment(5) == 0 ) ? 'No selected semester' : $this->uri->segment(5));
		
		$this->title = 'Subject Curriculum Management';
		$this->manager = $this->manager();
		$this->js = 'subjectCurriculumManagementAddSubject_js';
		$this->menu = $this->menu();
		$this->content = heading('Subject Curriculum Management', 1, 'class="content-title"');
		$this->content .= '<div class="col-xs-10 content-container contentWrapper">';
			$this->content .= '<div class="panel panel-default">';
				$this->content .= '<div class="panel-body">';
				$this->content .= '<p class="instruction">*Please select year level and semester.</p>';
				$this->content .= '<label>Year Level*'.nbs(2).':'.nbs(2).'</label>';
				$this->content .= '<select id="yearLevel">';
					$this->content .= '<option value = 0>Select Options</option>';
					$this->content .= '<option value = 1stYear '.(($yearLevel == '1stYear') ? 'selected="selected"' : null).'>1stYear</option>';
					$this->content .= '<option value = 2ndYear '.(($yearLevel == '2ndYear') ? 'selected="selected"' : null).'>2ndYear</option>';
					$this->content .= '<option value = 3rdYear '.(($yearLevel == '3rdYear') ? 'selected="selected"' : null).'>3rdYear</option>';
					$this->content .= '<option value = 4thYear '.(($yearLevel == '4thYear') ? 'selected="selected"' : null).'>4thYear</option>';
					$this->content .= '<option value = 5thYear '.(($yearLevel == '5thYear') ? 'selected="selected"' : null).'>5thYear</option>';
				$this->content .= '</select>';
				$this->content .= '<label>Semester*'.nbs(2).':'.nbs(2).'</label>';
				$this->content .= '<select id="semester">';
					$this->content .= '<option value = 0>Select Options</option>';
					$this->content .= '<option value = 1stSemester '.(($semester == '1stSemester') ? 'selected="selected"' : null).'>1stSemester</option>';
					$this->content .= '<option value = 2ndSemester '.(($semester == '2ndSemester') ? 'selected="selected"' : null).'>2ndSemester</option>';
					$this->content .= '<option value = summer '.(($semester == 'summer') ? 'selected="selected"' : null).'>Summer</option>';
				$this->content .= '</select>';
				$this->content .= '</div>';		
			$this->content .= '</div>';
			$this->content .= '<div class="panel panel-default">';
				$this->content .= '<div class="panel-body">';
					$this->content .= '<div id="selected"><p>&raquo; '.$this->curriculum($curriculumID).nbs(3).'<a href="'.base_url().''.$this->manager().'/subjectCurriculumManagementViewSubject/'.$curriculumID.'">(View Subjects)</a></p></div>';
					$this->content .= '<div id="selected"><p>&raquo; '.$yearLevel.' - '.$semester.'</p></div>';
				$this->content .= '</div>';		
			$this->content .= '</div>';		
			$this->content .= heading($this->department() .nbs().'Subject List', 4);
			$this->content .= '<div class="table-responsive">';
				$this->content .= '<table class="subjectTable">';
					$this->content .= '<thead>';
						$this->content .= '<tr>';
							$this->content .= '<th>SC CODE</th>';
							$this->content .= '<th>Descriptive Title</th>';
							$this->content .= '<th>Units</th>';
							$this->content .= '<th>Lec</th>';
							$this->content .= '<th>Lab</th>';
							$this->content .= '<th></th>';
						$this->content .= '</tr>';
					$this->content .= '</thead>';
					$this->content .= '<tfoot>';
						$this->content .= '<tr>';
							$this->content .= '<th>SC CODE</th>';
							$this->content .= '<th>Descriptive Title</th>';
							$this->content .= '<th>Units</th>';
							$this->content .= '<th>Lec</th>';
							$this->content .= '<th>Lab</th>';
							$this->content .= '<th></th>';
						$this->content .= '</tr>';
					$this->content .= '</tfoot>';
					$this->content .= '<tbody>';
					
					$q = $this->db->select('*')
					->from('subject s')
					->join('departmentsubject ds', 'ds.subject_subjectID = s.subjectID')
					->join('department d', 'd.departmentID = ds.department_departmentID')
					->where('departmentID', $this->departmentID())
					->order_by('sc_code', 'ASC')
					->get();
					
					foreach($q->result_array() as $row):
					$this->content .= '<tr>';
						$this->content .= '<td>'.$row['sc_code'].'</td>';
						$this->content .= '<td>'.$row['subject_title'].'</td>';
						$this->content .= '<td>'.$row['units'].'</td>';
						$this->content .= '<td>'.$row['lec'].'</td>';
						$this->content .= '<td>'.$row['lab'].'</td>';
						$this->content .= '<td><span id="'.$row['departmentSubjectID'].'" class="subject ui-icon ui-icon-plus"></span></td>';
					$this->content .= '</tr>';
					endforeach;
					$this->content .= '</tbody>';
				$this->content .= '</table>';
			$this->content .= '</div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);
	}

	public function curriculumManagementAddSubjectExe(){
		$postData = $this->input->post('postData');
		$curriculumID = $postData[0];
		$departmentSubjectID = $postData[1];
		$yearLevel = htmlentities($postData[2]);
		$semester = htmlentities($postData[3]);
		
		if(empty($yearLevel) || empty($semester)){
			echo 'error';
		}else{
			$data = array(
				'curriculum_curriculumID' => $curriculumID,
				'departmentsubject_departmentSubjectID' => $departmentSubjectID,
				'yearLevel' => $yearLevel,
				'semester' => $semester
			);
			$this->db->insert('subjectcurriculum', $data);
			$this->myModel->actionLogs($this->deanID(), 'Added subject to curriculum' , Date('Y-m-d h:i:s'));
		}
	}
	
	public function subjectCurriculumManagementViewSubject(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$curriculumID = $this->uri->segment(3);
		
		$this->title = 'Subject Curriculum Management';
		$this->manager = $this->manager();
		$this->js = 'subjectCurriculumManagementViewSubject_js';
		$this->menu = $this->menu();
		$this->content = heading('Subject Curriculum Management', 1, 'class="content-title"');
		$this->content .= '<div class="col-xs-10 content-container contentWrapper">';
			
			$this->content .= '<div id="selected"><p>'.$this->curriculum($curriculumID).' Subject List </p></div>';
			$this->content .= '<a class="link" href="'.base_url().''.$this->manager().'/subjectCurriculumManagementAddSubject/'.$curriculumID.'">(Add Subjects)</a>';			
			
			$yearLevel = array('1stYear','2ndYear','3rdYear','4thYear', '5thYear');
			$semester = array('1stSemester','2ndSemester','Summer');
			
			$this->content .= '<div class="row">';
				for($yl = 0; $yl < sizeof($yearLevel); $yl++){
						$this->content .= '<div class="col-xs-12">';
							$this->content .= '<div class="panel panel-default">';
							$this->content .= '<div class="panel-body viewSubjectCurriculum">';
								for($s = 0; $s < sizeof($semester); $s++){
								
								$this->content .= heading($yearLevel[$yl].' - '.$semester[$s], 5);
								$q = $this->db->select('*')
								->from('subject s')
								->join('departmentsubject ds', 'ds.subject_subjectID = s.subjectID')
								->join('department d', 'd.departmentID = ds.department_departmentID')
								->join('subjectcurriculum sc', 'ds.departmentSubjectID = sc.departmentsubject_departmentSubjectID')
								->where('departmentID', $this->departmentID())
								->where('curriculum_curriculumID', $curriculumID)
								->where('yearLevel', $yearLevel[$yl])
								->where('semester', $semester[$s])
								->get();

								if($q->num_rows() < 1){
									$this->content .= '';
								}else{					
									$this->content .= '<div id="table" class="table-responsive">';
										$this->content .= '<table class="">';
											$this->content .= '<thead>';
												$this->content .= '<tr>';
													$this->content .= '<th>SC CODE</th>';
													$this->content .= '<th>Descriptive Title</th>';
													$this->content .= '<th>Units</th>';
													$this->content .= '<th>Lec</th>';
													$this->content .= '<th>Lab</th>';
													$this->content .= '<th colspan="2"></th>';
												$this->content .= '</tr>';
											$this->content .= '</thead>';
											$this->content .= '<tbody>';
											foreach($q->result_array() as $row):
												$this->content .= '<tr>';
													$this->content .= '<td>'.$row['sc_code'].'</td>';
													$this->content .= '<td>'.$row['subject_title'].'</td>';
													$this->content .= '<td>'.$row['units'].'</td>';
													$this->content .= '<td>'.$row['lec'].'</td>';
													$this->content .= '<td>'.$row['lab'].'</td>';
													$this->content .= '<td><span id="'.$row['subjectCurriculumID'].'_'.$row['sc_code'].'_'.$row['subject_title'].'" class="offer ui-icon ui-icon-plus"></span></td>';
													$this->content .= '<td><span id="'.$row['subjectID'].'" class="remove ui-icon ui-icon-close"></span></td>';
												$this->content .= '</tr>';
											endforeach;
											$this->content .= '</tbody>';
										$this->content .= '</table>';
									$this->content .= '</div>';
								}
							}
						$this->content .= '</div>';
						$this->content .= '</div>';
					$this->content .= '</div>';
				}
			$this->content .= '</div>';
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);		
	}
	
	public function subjectCurriculumManagementOfferSubject(){
		$posData = $this->input->post('postData');
		$subjectCurriculumID = $posData[0];
		$scid = htmlentities($posData[1]);

		$scidStart = $this->scidStart();
		$scidEnd = $this->scidEnd();
		
		if(!empty($scid)) {
			if($scid < $scidStart){
				echo 'lesserThan';
			}elseif($scid > $scidEnd){
				echo 'greaterThan';
			}elseif($this->scidExist($scid) == false){
				echo 'exist';
			}else{
				$data = array(
					'subjectCurriculum_subjectCurriculumID' => $subjectCurriculumID,
					'sc_id' => $scid
				);
				$this->db->insert('subjectoffered', $data);
				$this->myModel->actionLogs($this->deanID(), 'Offered subject' , Date('Y-m-d h:i:s'));
			}
		}else{
			echo 'empty';
		}	
	}
	
	public function subjectCurriculumManagementRemoveSubject(){
		$postID = $this->uri->segment(3);
		
		$this->myModel->deleteClassroomSchedule($postID);
		$this->myModel->deleteSubjectOffered($postID);
		$this->myModel->deleteSubjectCurriculum($postID);
		$this->myModel->actionLogs($this->deanID(), 'Removed subject from curriculum' , Date('Y-m-d h:i:s'));
	}
	/* CLASSROOM MANAGEMENT
	 * ==========================================================================
	 */	
	public function classroomManagement(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Classroom Management';
		$this->manager = $this->manager();
		$this->js = 'classroomManagement_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Classroom Management', 1, 'class="content-title"');
		$this->content .= heading('&raquo; Create Schedule', 4);
		$this->content .= '<div class="col-xs-12" id="message"></div>';
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
		$q = $this->db->select('*')
		->from('room')
		->order_by('room', 'ASC')
		->get();
		
		$arr = array();
		foreach ($q->result_array() as $row){
			$arr[] = array(
					'roomID' => $row['roomID'],
					'room' => $row['room'],
					'description' => $row['description']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}

	public function classroomManagementScheduleJSON($roomID){
		$sql = 'SELECT * FROM room r
			JOIN departmentsubjectschedule dss ON r.roomID = dss.room_roomID
			JOIN subjectoffered so ON dss.subjectoffered_subjectOfferedID = so.subjectOfferedID
			JOIN subjectcurriculum sc ON so.subjectcurriculum_subjectCurriculumID = sc.subjectCurriculumID
			JOIN departmentsubject ds ON sc.departmentsubject_departmentSubjectID = ds.departmentSubjectID
			JOIN subject s ON ds.subject_subjectID = s.subjectID
			JOIN department dt ON ds.department_departmentID = dt.departmentID
			JOIN curriculum c ON sc.curriculum_curriculumID = c.curriculumID
			WHERE r.roomID = ? ORDER BY dss.startTime ASC';
		$q = $this->db->query($sql, array($roomID));
		$arr = array();
		foreach ($q->result_array() as $row){
			$arr[] = array(
				'departmentSubjectScheduleID' => $row['departmentSubjectScheduleID'],
				'day' => $row['day'],
				'startTime' => $row['startTime'],
				'endTime' => $row['endTime'],
				'sc_id' => $row['sc_id'],
				'sc_code' => $row['sc_code'],
				'subject_title' => $row['subject_title'],
				'department_code' => $row['department_code'],
				'generated' => $row['generated']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}

	public function classroomManagementScheduleCreate(){
		$postData = $this->input->post('postData');
		$day = $this->input->post('days');
		$roomID = $postData[0];
		$subjectID = $postData[1];
		$startTime = $postData[2];
		$endTime = $postData[3];
		$generated = $postData[4];
		
		$data = array(
			'room_roomID' => $roomID,
			'subjectoffered_subjectOfferedID' => $subjectID,
			'day' => $day,
			'startTime' => $startTime,
			'endTime' => $endTime,
			'generated' => $generated
		);
				
		if(strtotime($endTime) < strtotime($startTime)){
			echo 'Invalid end time.';
		}else{
		
			$sql = 'SELECT * FROM departmentsubjectschedule WHERE room_roomID = ? AND day = ?';
			$q = $this->db->query($sql, array($roomID, $day));
			
			if($q->num_rows() < 1){
				
				$this->db->insert('departmentsubjectschedule', $data);
				echo 'Success';
				
			}else{
				
				$sql = 'SELECT * FROM departmentsubjectschedule WHERE room_roomID = ? AND day = ? ORDER BY startTime ASC';
				$query = $this->db->query($sql, array($roomID, $day));
				
				//$conflict = false;
				
				$stimeArr = array();
				$etimeArr = array();
				
				foreach($query->result_array() as $row){
					
					array_push($stimeArr , strtotime($row['startTime']));
					array_push($etimeArr , strtotime($row['endTime']));
					
					//var_dump($row);
					/*
					var_dump($startTime .'-->'.$endTime."-----");
					var_dump($row['startTime'].'-->'.$row['endTime'].'<br/>');
					
					//if( ( strtotime($endTime) <= strtotime($row['startTime']) )  ){
					var_dump(strtotime($endTime) > strtotime($row['startTime']));
					var_dump(strtotime($endTime) < strtotime($row['endTime']));
					
					
					if(!$conflict){
					
						if(( strtotime($endTime) > strtotime($row['startTime']) && strtotime($endTime) < strtotime($row['endTime']) )   ){
							
							echo "conflict middle";
							$conflict = true;
							break;
						
						}else{
							
							echo "not middle";
							
							if(strtotime($startTime) >= strtotime($row['endTime'])){
								$this->db->insert('departmentsubjectschedule', $data);
								echo 'Success first';
								break;
							}elseif(strtotime($endTime) <= strtotime($row['startTime'])){	
								$this->db->insert('departmentsubjectschedule', $data);
								echo 'Success second';
								break;
							}else{
								echo 'Conflict';
								$conflict = true;
							}	
						
							
							
						}
					
					}*/
				}
				
			
				
				for($s = 0; $s < sizeof($stimeArr); $s++){
					for($e = 0; $e < sizeof($etimeArr); $e++){
						if(strtotime($startTime) == $etimeArr[$e]){ // true
							if(strtotime($endTime) > $stimeArr[$e+1] ){
								echo 'conflict greater';
								break;
							}else{
								echo 'success first';
								break;
							}
							
						}elseif(strtotime($startTime) <= $stimeArr[$s]){ // true
							if(strtotime($endTime) <= $stimeArr[$e]){
								echo 'success second';
								break;
							}else{
								echo 'conflict lesser';
								break;
							}
						}elseif(strtotime($startTime) > $stimeArr[$s]){

							if(strtotime($startTime) > $stimeArr[$s] && strtotime($startTime) < $etimeArr[$e]){
								echo 'conflict third';
								break;
							}else{
								echo 'success third';
								break;
							}
						}else{
							echo 'conflict all';
							break;
						}
					}
					
					break;
				}
				
				
				
				
			}
		
		}
		
		/*if($q->num_rows() < 1){
			$data = array(
				'room_roomID' => $roomID,
				'subjectoffered_subjectOfferedID' => $subjectID,
				'day' => $day,
				'startTime' => $startTime,
				'endTime' => $endTime,
				'generated' => $generated
			);
			$this->db->insert('departmentsubjectschedule', $data);
			echo '<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<i class="fa fa-check" aria-hidden="true"></i> Successful.</div>';
			
			$this->myModel->actionLogs($this->deanID(), 'Created new schedule' , Date('Y-m-d h:i:s'));
		}else{
			$sql = 'SELECT * FROM departmentsubjectschedule dss
				JOIN room r ON dss.room_roomID = r.roomID
				WHERE day = ? AND room_roomID = ?';
			$query = $this->db->query($sql, array($day, $roomID));
			
			$stime = strtotime($startTime); //inputted start time
			$etime = strtotime($endTime); 	//inputted end time

			foreach($query->result_array() as $row):
			
				$existingStime = strtotime($row['startTime']); //existing start time
				$existingEtime = strtotime($row['endTime']); 	//existing end time
				
				if($stime == $existingStime){
					echo '<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span> Conflict schedule.</div>';
					break;
				}elseif($stime < $existingEtime && $etime > $existingStime){
					echo '<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span> Conflict schedule.</div>';
					break;
				}else{
					$data = array(
						'room_roomID' => $roomID,
						'subjectoffered_subjectOfferedID' => $subjectID,
						'day' => $day,
						'startTime' => $startTime,
						'endTime' => $endTime,
						'generated' => $generated
					);
					$this->db->insert('departmentsubjectschedule', $data);
					echo '<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<i class="fa fa-check" aria-hidden="true"></i> Successful.</div>';
					$this->myModel->actionLogs($this->deanID(), 'Created new schedule' , Date('Y-m-d h:i:s'));
				}
			endforeach;	
		}*/
	}
	
	public function classroomManagementVacantSchedule(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Classroom Management';
		$this->manager = $this->manager();
		$this->js = 'classroomManagementVacantSchedule_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('Classroom Management', 1, 'class="content-title"');
		$this->content .= heading('&raquo; Vacant Schedule', 4);
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
	
	public function classroomManagementVacantScheduleView(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$roomID = $this->uri->segment(3);

		$this->title = 'Classroom Management';
		$this->manager = $this->manager();
		$this->js = 'classroomManagementVacantScheduleView_js';
		$this->menu = $this->menu();
		$this->content = heading('Classroom Management', 1, 'class="content-title"');
		$this->content .= '<div class="col-xs-10 content-container contentWrapper">';
		$this->content .= heading('Vacant Schedule '.$this->classroom($roomID).'', 4);
		
		$roomSQL = 'SELECT * FROM room r 
			JOIN departmentsubjectschedule dss ON r.roomID = dss.room_roomID
			WHERE r.roomID = ?';
		$sql = $this->db->query($roomSQL, array($roomID));	
		
		if($sql->num_rows() > 0){
			$mondayArr 		= array();
			$tuesdayArr 	= array();
			$wednesdayArr 	= array();
			$thursdayArr 	= array();
			$fridayArr 		= array();
			$saturdayArr 	= array();
			$sundayArr 		= array();
			
			foreach($sql->result_array() as $q){
				switch($q['day']){
					case 'M':
						array_push($mondayArr, $q['departmentsubjectscheduleID']);	
						break;
					case 'T':
						array_push($tuesdayArr, $q['departmentsubjectscheduleID']);
						break;
					case 'W':
						array_push($wednesdayArr, $q['departmentsubjectscheduleID']);
						break;
					case 'TH':
						array_push($thursdayArr, $q['departmentsubjectscheduleID']);
						break;
					case 'F':
						array_push($fridayArr, $q['departmentsubjectscheduleID']);
						break;
					case 'SAT':
						array_push($saturdayArr, $q['departmentsubjectscheduleID']);
						break;
					case 'SUN':
						array_push($sundayArr, $q['departmentsubjectscheduleID']);
						break;
				}
			}
			
			/*$starttime 	= '7:00:00';   // start time
			$endtime 	= '20:00:00';  // end time
			$duration 	= '30';  	   // split by 30 mins

			$monday_arr_time 	= array ();
			$start_time    		= strtotime ($starttime); //change to strtotime
			$end_time      		= strtotime ($endtime); //change to strtotime
			$add_mins 			= $duration * 60;

			while ($start_time <= $end_time){ // loop between time
			   $monday_arr_time[] = date ('H:i:s', $start_time);
			   $start_time += $add_mins; // to check endtime
			}
			
			for($m = 0; $m < sizeof($mondayArr); $m++){
				
				$timeSQL = 'SELECT * FROM room r 
					JOIN departmentsubjectschedule dss ON r.roomID = dss.room_roomID
					JOIN day d ON dss.day_dayID = d.dayID
					JOIN starttime st ON dss.starttime_startTimeID = st.startTimeID
					JOIN endtime et ON dss.endtime_endTimeID = et.endTimeID';
				$tsql = $this->db->query($timeSQL, array($mondayArr[$m]));
				$t = $tsql->row();

				$stime = strtotime($t->stime);
				$etime = strtotime($t->etime);
				
				$startingTime = $t->stime;
				
				foreach($monday_arr_time as $key => $value){
					//$val = strtotime($value);
					if(strtotime($value) > $stime && strtotime($value) < $etime){
						unset($monday_arr_time[$key]);
						/*if(strtotime($value) == $stime){
							if(strtotime($startingTime) == strtotime($starttime)){
								unset($monday_arr_time[$key]);
							}else{
								$q = 'SELECT * FROM room r 
									JOIN departmentsubjectschedule dss ON r.roomID = dss.room_roomID
									JOIN day d ON dss.day_dayID = d.dayID
									JOIN time t ON dss.time_timeID = t.timeID
									WHERE t.etime = ? ';
								$que = $this->db->query($q, array($startingTime));
								
								if($que->num_rows() > 0){
									unset($monday_arr_time[$key]);
								}
							}
							
						}else{
							unset($monday_arr_time[$key]);
						}
					}
				}
			}
			$this->content .= '<div class="col-xs-2 well">';
				$this->content .= heading('Monday', 4);
				foreach($monday_arr_time as $key => $value){
					$this->content .= '<ul style="list-style: none;">';
						$q = 'SELECT * FROM room r 
							JOIN departmentsubjectschedule dss ON r.roomID = dss.room_roomID
							JOIN day d ON dss.day_dayID = d.dayID
							JOIN time t ON dss.time_timeID = t.timeID';
						$que = $this->db->query($q);
						$row = $que->row();
						if(strtotime($row->stime) != strtotime($value)){
							$this->content .= '<li>'.date('h:i:s A', strtotime($value)).'</li>';
						}elseif(strtotime($row->stime) == strtotime($value)){
							$this->content .= '<li style="color: #800000;">'.date('h:i:s A', strtotime($value)).'</li>';
						}
					
						
					$this->content .= '</ul>';
				}
			$this->content .= '</div>';	*/
			
		}else{
			$this->content .= 'VACANT CLASSROOM';
		}		
			
		
			
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);			
		
	}
	
	public function classroomManagementScheduleDelete(){
		$postID = $this->input->post('postID');
		$this->db->delete('departmentsubjectschedule', array('departmentSubjectScheduleID' => $postID));
		$this->myModel->actionLogs($this->deanID(), 'Deleted schedule' , Date('Y-m-d h:i:s'));
	}
	/* REPORTS
	 * (subject offered summary)
	 * (classroom schedule results)
	 * ================================================================================
	 */
	public function subjectsOfferedSummary(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$this->title = 'Subject Offered Summary';
		$this->manager = $this->manager();
		$this->js = 'subjectsOfferedSummary_js';
		$this->menu = $this->menu();
		$this->content = heading('Subject Offered Summary', 1, 'class="content-title"');
		$this->content .= '<div class="col-xs-10 content-container contentWrapper">';
			$this->content .= '<div class="well">';
				$this->content .= '<label>Show or Hide Column: </label>'.nbs(2);
				$this->content .= '<a class="toggle-vis" data-column="0">SC ID</a>'.nbs(2).'|'.nbs(2);
				$this->content .= '<a class="toggle-vis" data-column="1">SC CODE</a>'.nbs(2).'|'.nbs(2);
				$this->content .= '<a class="toggle-vis" data-column="2">Descriptive Title</a>'.nbs(2).'|'.nbs(2);
				$this->content .= '<a class="toggle-vis" data-column="3">Units</a>'.nbs(2).'|'.nbs(2);
				$this->content .= '<a class="toggle-vis" data-column="4">Lec</a>'.nbs(2).'|'.nbs(2);
				$this->content .= '<a class="toggle-vis" data-column="5">Lab</a>'.nbs(2).'|'.nbs(2);
				$this->content .= '<a class="toggle-vis" data-column="6">Year Level</a>';
			$this->content .= '</div>';

			$this->content .= '<div class="table-responsive">';
				$this->content .= '<table id="subjectsOfferedSummaryTable" class="display">';
					$this->content .= '<thead>';
						$this->content .= '<tr>';
							$this->content .= '<th>SC ID</th>';
							$this->content .= '<th>SC CODE</th>';
							$this->content .= '<th>Descriptive Title</th>';
							$this->content .= '<th>Units</th>';
							$this->content .= '<th>Lec</th>';
							$this->content .= '<th>Lab</th>';
							$this->content .= '<th>Year Level</th>';
						$this->content .= '</tr>';
					$this->content .= '</thead>';
					$this->content .= '<tfoot>';
						$this->content .= '<tr>';
							$this->content .= '<th>SC ID</th>';
							$this->content .= '<th>SC CODE</th>';
							$this->content .= '<th>Descriptive Title</th>';
							$this->content .= '<th>Units</th>';
							$this->content .= '<th>Lec</th>';
							$this->content .= '<th>Lab</th>';
							$this->content .= '<th>Year Level</th>';
						$this->content .= '</tr>';
					$this->content .= '</tfoot>';
					$this->content .= '<tbody>';

					$query = $this->db->select('*')
					->from('subjectoffered so')
					->join('subjectcurriculum sc', 'sc.subjectCurriculumID = so.subjectcurriculum_subjectCurriculumID')
					->join('departmentsubject ds', 'ds.departmentSubjectID = sc.departmentsubject_departmentSubjectID')
					->join('department dpt', 'dpt.departmentID = ds.department_departmentID')
					->join('curriculum c', 'c.curriculumID = sc.curriculum_curriculumID')
					->join('subject s', 's.subjectID = ds.subject_subjectID')
					->where('departmentID', $this->departmentID())
					->get();

					foreach ($query->result_array() as $row):
						$this->content .= '<tr>';
							$this->content .= '<td>'.$row['sc_id'].'</td>';
							$this->content .= '<td>'.$row['sc_code'].'</td>';
							$this->content .= '<td>'.$row['subject_title'].'</td>';
							$this->content .= '<td>'.$row['units'].'</td>';
							$this->content .= '<td>'.$row['lec'].'</td>';
							$this->content .= '<td>'.$row['lab'].'</td>';
							$this->content .= '<td>'.$row['yearLevel'].'</td>';
						$this->content .= '</tr>';
					endforeach;

					$this->content .= '</tbody>';
				$this->content .= '</table>';
			$this->content .= '</div>';
		
		$this->content .= '</div>';
		
		$data['title'] = $this->title;
		$data['manager'] = $this->manager;
		$data['js'] = $this->js;
		$data['menu'] = $this->menu;
		$data['content'] = $this->content;
		
		$this->load->view('index', $data);		
	}

	public function classroomScheduleResults(){
		if(!$this->loggedin()) $this->redirectTo('login');
		
		$roomID = (($this->uri->segment(3) > 0) ? $this->uri->segment(3) : null );
		$dayID = (($this->uri->segment(4) > 0) ? $this->uri->segment(4) : null );
		
		$this->title = 'Classroom Schedule Results';
		$this->manager = $this->manager();
		$this->js = 'classroomScheduleResults_js';
		$this->menu = $this->menu();
		$this->content = heading('Classroom Schedule Results', 1, 'class="content-title"');
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
			$this->content .= '<input id="searchButton" type="button" value="Search"/>';
			$this->content .= '</div>';		
		$this->content .= '</div>';
	
		$this->content .= '<div id="printBody">';
			$this->content .= '<div class="table-responsive">';	
				$this->content .= heading(($this->uri->segment(3) > 0 ) ? $this->classroom($roomID) : 'All', 4).br();
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
					$this->db->where('departmentID', $this->departmentID());
					(($roomID > 0)? $this->db->where('r.roomID', $roomID) : null);
					(($dayID > 0)? $this->db->where('d.dayID', $dayID) : null);
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
	/* GENERAL SETTINGS
	 * ================================================================================
	 */
	public function generalSettings(){
		if(!$this->loggedin()) $this->redirectTo('login');
	
		$this->title = 'General Settings';
		$this->manager = $this->manager();
		$this->js = 'generalSettings_js';
		$this->menu = $this->menu();
		$this->content = '<div class="content-container">';
		$this->content .= heading('General Settings', 1, 'class="content-title"');
	
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
			$personID = $this->personID();
			
			$sql = 'SELECT * FROM person JOIN dean ON person.personID = dean.person_personID WHERE username = ? AND personID = ?';
			$query = $this->db->query($sql, array($username, $personID));
	
			if($query->num_rows() > 0) {
				$data = array('password' => $this->hash($password));
				$this->db->where('personID', $personID);
				$this->db->update('person', $data);
				$this->content .= '<div class="col-xs-9 alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><span class="glyphicon glyphicon-info-sign"></span> Successfully changed password.</div>';
			
				$this->myModel->actionLogs($this->deanID(), 'Changed password' , Date('Y-m-d h:i:s'));

			}else{
				$this->content .= '<div class="col-xs-9 alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><span class="glyphicon glyphicon-info-sign"></span> Incorrect username <strong>'.$username.'</strong></div>';
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
	
	/* DROPDOWN JSON
	 * ================================================================================
	 */	
	public function subjectDropdownJSON(){
		$sql = 'SELECT * FROM subject s
			JOIN departmentsubject ds ON s.subjectID = ds.subject_subjectID
			JOIN subjectcurriculum  sc ON ds.departmentSubjectID = sc.departmentsubject_departmentSubjectID
			JOIN subjectoffered so ON sc.subjectCurriculumID = so.subjectcurriculum_subjectCurriculumID
			WHERE ds.department_departmentID = ? ';
		$q = $this->db->query($sql, array($this->departmentID()));
		$arr = array();
		foreach ($q->result_array() as $row){
			$arr[] = array(
					'subjectOfferedID' => $row['subjectOfferedID'],
					'subject' => $row['sc_id'].' | '.$row['sc_code'] .' - '.$row['subject_title']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");
	}	
	public function startTimeDropdownJSON(){
		$this->myModel->startTimeDropdownJSON();	
	}
	public function endTimeDropdownJSON(){
		$this->myModel->endTimeDropdownJSON();	
	}	
	public function dayDropdownJSON(){
		$this->myModel->dayDropdownJSON();	
	}
	/* PRIVATE FUNCTIONS
	 * ================================================================================
	 */
	private function manager() {
		return strtolower(get_class());
	}
	private function menu() {
		return 'inc/dean';
	}
	private function personID(){
		return $this->session->userdata(base_url().''.$this->manager().'/personID');
	}
	private function loggedin() {
		return $this->session->userdata(base_url().''.$this->manager().'/loggedin');
	}
	private function departmentID(){
		return $this->session->userdata(base_url().''.$this->manager().'/departmentID');
	}
	private function deanID(){
		$sql = 'SELECT * FROM dean d JOIN person p ON d.person_personID = p.personID WHERE p.personID = ? ';
		$q = $this->db->query($sql, array($this->personID()));
		$row = $q->row();
		return $row->deanID;
	}
	private function department(){
		$sql = 'SELECT * FROM department WHERE departmentID = ? ';
		$q = $this->db->query($sql, array($this->departmentID()));
		$row = $q->row();
		return $row->department_code .' - '. $row->department_title;
	}
	private function curriculum($curriculumID){
		$sql = 'SELECT * FROM curriculum WHERE curriculumID = ? ';
		$q = $this->db->query($sql, array($curriculumID));
		$row = $q->row();
		return $row->curriculumTitle .' | '. $row->academicYear;
	}
	private function scidStart(){
		$sql = 'SELECT * FROM scid s JOIN department d ON s.department_departmentID = d.departmentID WHERE d.departmentID = ? ';
		$q = $this->db->query($sql, array($this->departmentID()));
		$row = $q->row();
		return $row->scid_start;
	}
	private function scidEnd(){
		$sql = 'SELECT * FROM scid s JOIN department d ON s.department_departmentID = d.departmentID WHERE d.departmentID = ? ';
		$q = $this->db->query($sql, array($this->departmentID()));
		$row = $q->row();
		return $row->scid_end;
	}
	private function scidExist($x){
		$sql = 'SELECT * FROM subjectoffered WHERE sc_id = ? ';
		$q = $this->db->query($sql, array($x));
		if($q->num_rows() > 0) {
			return false;
		}else{
			return true;
		}
	}
	private function classroom($roomID){
		$sql = 'SELECT * FROM room WHERE roomID = ? ';
		$q = $this->db->query($sql, array($roomID));
		$row = $q->row();
		return $row->room .' - '.$row->description;
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
	private function hash($string) {
		return $this->myModel->hash($string);
	}
	private function redirectTo($to) {
		redirect($this->manager().'/'.$to);
	}
}