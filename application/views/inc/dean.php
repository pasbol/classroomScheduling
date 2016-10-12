<ul class='sidebar-menu'>
	<li class="treeview">
		<li><a href="<?php echo base_url() .''. $manager; ?>/home"><i class="fa fa-home"></i><span>Home</span></a></li>
	</li>	
	<li class="treeview">
		<li><a href="<?php echo base_url() .''. $manager; ?>/subjectManagement"><i class="fa fa-files-o" aria-hidden="true"></i><span>Manage Subject</span></a></li>
	</li>	
	<li class="treeview">
		<li><a href="<?php echo base_url() .''. $manager; ?>/subjectCurriculumManagement"><i class="fa fa-files-o" aria-hidden="true"></i><span>Manage Curriculum</span></a></li>
	</li>	
	<li class="treeview">
	    <a href="#"><i class="fa fa-files-o" aria-hidden="true"></i><span>Manage Classroom</span> <i class="fa fa-angle-left pull-right"></i></a>
		<ul class="treeview-menu">
			<li><a href="<?php echo base_url() .''. $manager; ?>/classroomManagement"><i class="fa fa-plus-square-o" aria-hidden="true"></i><span>Create Schedule<span></a></li>
			<li><a href="<?php echo base_url() .''. $manager; ?>/classroomManagementVacantSchedule"><i class="fa fa-circle-thin" aria-hidden="true"></i><span>Vacant Schedule<span></a></li>
		</ul>
	</li>
	<li class="treeview">
		<a href="#"><i class="fa fa-files-o" aria-hidden="true"></i><span>Reports</span> <i class="fa fa-angle-left pull-right"></i></a>
		<ul class="treeview-menu">
            <li><a href="<?php echo base_url() .''. $manager; ?>/subjectsOfferedSummary"><i class="fa fa-file-o" aria-hidden="true"></i><span>Subjects Offered Summary</span></a></li>
			<li><a href="<?php echo base_url() .''. $manager; ?>/classroomScheduleResults"><i class="fa fa-clipboard" aria-hidden="true"></i><span>Classroom Schedule Results</span></a></li>
		</ul>
	</li>
	<li class="treeview">
		<li><a href="<?php echo base_url() .''. $manager; ?>/generalSettings"><i class="fa fa-cog" aria-hidden="true"></i><span>General Settings</span></a></li>
	</li>
	<li class="treeview">
		<li><a href="<?php echo base_url() .''. $manager; ?>/logout"><i class="fa fa-sign-out" aria-hidden="true"></i><span>Logout</span></a></li>
	</li>
</ul>

