<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class myModel extends CI_Model {
	public function __construct() {
		parent::__construct();
	} 
	
	/*
	 * LOGS	========================================================================
	 */ 	
	public function actionLogs($deanID, $action, $date){
		$logsData = array(
			'dean_deanID' => $deanID,
			'action' => $action,
			'date' => $date
		);
		$this->db->insert('logs', $logsData);
	}
	
	/*
	 * PAGINTATION ==================================================================
	 */	
	 
	public function findAllLogs(){
		return $this->db->count_all('logs');
	}
	
	/*
	 * DELETE =======================================================================
	 */ 
	public function deleteSubject($postID){
		if($this->db->delete('subject', 'subjectID = ' .$postID)){
			return true;
		}
	}
	public function deleteDepartmentSubject($postID){
		if($this->db->delete('departmentsubject', 'subject_subjectID = ' .$postID)){
			return true;
		}
	}
	public function deleteSubjectCurriculum($postID){
		$sql = 'SELECT * FROM subject s
			JOIN departmentsubject ds ON s.subjectID = ds.subject_subjectID
			JOIN subjectcurriculum sc ON ds.departmentSubjectID = sc.departmentsubject_departmentSubjectID
			WHERE s.subjectID = ?';
		$q = $this->db->query($sql, array($postID));
		$row = $q->row();
		$subjectCurriculumID = $row->subjectCurriculumID;
		
		if($this->db->delete('subjectcurriculum', 'subjectCurriculumID = ' .$subjectCurriculumID)){
			return true;
		}
	}
	public function deleteSubjectOffered($postID){
		$sql = 'SELECT * FROM subject s
			JOIN departmentsubject ds ON s.subjectID = ds.subject_subjectID
			JOIN subjectcurriculum sc ON ds.departmentSubjectID = sc.departmentsubject_departmentSubjectID
			JOIN subjectoffered so ON sc.subjectCurriculumID = so.subjectcurriculum_subjectCurriculumID
			WHERE s.subjectID = ?';
		$q = $this->db->query($sql, array($postID));
		$row = $q->row();
		$subjectOfferedID = $row->subjectOfferedID;
		
		if($this->db->delete('subjectoffered', 'subjectOfferedID = ' .$subjectOfferedID)){
			return true;
		}
	}
	public function deleteClassroomSchedule($postID){
		$sql = 'SELECT * FROM subject s
			JOIN departmentsubject ds ON s.subjectID = ds.subject_subjectID
			JOIN subjectcurriculum sc ON ds.departmentSubjectID = sc.departmentsubject_departmentSubjectID
			JOIN subjectoffered so ON sc.subjectCurriculumID = so.subjectcurriculum_subjectCurriculumID
			JOIN departmentsubjectschedule dss ON so.subjectOfferedID = dss.subjectoffered_subjectOfferedID
			WHERE s.subjectID = ?';
		$q = $this->db->query($sql, array($postID));
		$row = $q->row();
		$departmentSubjectScheduleID = $row->departmentSubjectScheduleID;
		
		if($this->db->delete('departmentsubjectschedule', 'departmentSubjectScheduleID = ' .$departmentSubjectScheduleID)){
			return true;
		}		
	}
	
	/*
	 * TIME ARRAYS =====================================================================
	 */	
	
	
	/*
	 * DROPDOWNS =======================================================================
	 */
	 
	public function departmentDropdownJSON(){
		$sql = 'SELECT * FROM department';
		$q = $this->db->query($sql);
		$arr = array();
		foreach ($q->result_array() as $row){
			$arr[] = array(
					'departmentID' => $row['departmentID'],
					'department_code' => $row['department_code']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");		
	} 
	public function courseDropdownJSON(){
		$sql = 'SELECT * FROM course ORDER BY course_code ASC';
		$q = $this->db->query($sql);
		$arr = array();
		foreach($q->result_array() as $row){
			$arr[] = array(
					'courseID' => $row['courseID'],
					'course_code' => $row['course_code']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");	
	}
	public function startTimeDropdownJSON(){
		$sql = 'SELECT * FROM starttime ORDER BY starttime ASC';
		$q = $this->db->query($sql);
		$arr = array();
		foreach ($q->result_array() as $row){
			$arr[] = array(
					'startTimeID' => $row['startTimeID'],
					'startTime' => $row['startTime']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");		
	}	
	public function endTimeDropdownJSON(){
		$sql = 'SELECT * FROM endTime ORDER BY endTime ASC';
		$q = $this->db->query($sql);
		$arr = array();
		foreach ($q->result_array() as $row){
			$arr[] = array(
					'endTimeID' => $row['endTimeID'],
					'endTime' => $row['endTime']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");		
	}
	public function dayDropdownJSON(){
		$sql = 'SELECT * FROM day';
		$q = $this->db->query($sql);
		$arr = array();
		foreach ($q->result_array() as $row){
			$arr[] = array(
					'dayID' => $row['dayID'],
					'day' => $row['day']
			);
		}
		header ( "Content-type: application/json" );
		echo ("{\"data\":".json_encode($arr)."}");		
	}
	public function subjectDropdownJSON(){
		$sql = 'SELECT * FROM subject s
			JOIN departmentsubject ds ON s.subjectID = ds.subject_subjectID
			JOIN subjectcurriculum  sc ON ds.departmentSubjectID = sc.departmentsubject_departmentSubjectID
			JOIN subjectoffered so ON sc.subjectCurriculumID = so.subjectcurriculum_subjectCurriculumID';
		$q = $this->db->query($sql);
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
	
	/*
	 * HASH =========================================================================
	 */ 
	 
	public function hash($string) {
		return hash('md5', $string . config_item('encryption_key'));
	}
}