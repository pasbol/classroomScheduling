<script type="text/javascript">
	$(document).ready(function () {
	
		selectedSubject();
		
		var theme = 'classic';
		$('h4').css({
			'font-weight': 'bold',
			'font-size': '16px'
		});
		$('table').css({
			'width': '100%'
		});
		$('table thead').css({
			'background-color': '#800000',
			'border-top': 'solid 2px #191919',
			'border-bottom': 'solid 1px #191919'
		});
		$('table thead tr th').css({
			'padding': '8px',
			'color': '#ffffff'
		});
		$('table tbody').css({
			'background-color': '#fffcf7'
		});
		$('table tbody tr').css({
			'border-bottom': 'solid 1px #cdcccc'
		});
		$('table tbody tr td').css({
			'padding': '2px'
		});	
		$('table tfoot').css({
			'background-color': '#800000',
			'border-bottom': 'solid 2px #191919',
			'border-top' : 'solid 1px #191919'
		});
		$('table tfoot tr th').css({
			'padding': '8px',
			'color': '#ffffff'
		});
		$('span').css({ 'cursor' : 'pointer' });
		$('div#selected p').css({
			'font-size' : '14px',
			'font-weight' : 'normal',
			'text-indent' : '1.26em',
			'color' : '#800000'
		});
		$('div#selected p a').css({
			'font-size' : '14px',
			'font-weight' : 'normal'
		});
		
		var curriculumID = '<?php echo $this->uri->segment(3); ?>';
		var yearLevel = $('#yearLevel').val();
		var semester = $('#semester').val();
		
		$('#yearLevel').on('change',function() {
			var yearLevel = $(this).val();
			var url = '<?php echo base_url() .''.$manager; ?>/subjectCurriculumManagementAddSubject/'+curriculumID+'/'+yearLevel+'/'+semester;
			$('#removeSubjectCurriculumDialog').dialog('close');
			window.location.replace(url);
		});
		
		$('#semester').on('change',function() {
			var semester = $(this).val();
			var url = '<?php echo base_url() .''.$manager; ?>/subjectCurriculumManagementAddSubject/'+curriculumID+'/'+yearLevel+'/'+semester;
			$('#removeSubjectCurriculumDialog').dialog('close');
			window.location.replace(url);
		});
		
		function selectedSubject() {
			$('.subject').bind('click', function() {
				var departmentSubjectID = new String($(this).attr('id'));
				var curriculumID = '<?php echo $this->uri->segment(3); ?>';
				var yearLevel = $('#yearLevel').val();
				var semester = $('#semester').val();
				
				$('#selectedSubjectDialog').remove();
				var dialog = '<div id="selectedSubjectDialog"><p>Do you really want to add this subject?</p></div>';
				$('body').append(dialog);
				createDialog('selectedSubjectDialog', 'Add Subject Confirmation', 350, 200);
				$('#selectedSubjectDialog').dialog({
					buttons : {
						'OK' : function() {
							var postData = new Array();
							postData.push(curriculumID);
							postData.push(departmentSubjectID);
							postData.push(yearLevel);
							postData.push(semester);
							var url = '<?php echo base_url() .''.$manager; ?>/curriculumManagementAddSubjectExe';
							$.post(url, { postData : postData }, function(data) {
								$('#selectedSubjectDialog').dialog('close');
								if(data == 'error'){
									var divNotification = $('<div id="divNotification">Year level and semester are required.</div>');
									divNotification.jqxNotification({
										width: 300, 
										position: 'bottom-right', 
										opacity: 1.28,
										autoOpen: false, 
										animationOpenDelay: 1000, 
										autoClose: true, 
										autoCloseDelay: 6000, 
										template: 'warning'
									});
									divNotification.jqxNotification('open');							
								}else{
									var divNotification = $('<div id="divNotification">Successful.</div>');
									divNotification.jqxNotification({
										width: 300, 
										position: 'bottom-right', 
										opacity: 1.28,
										autoOpen: false, 
										animationOpenDelay: 1000, 
										autoClose: true, 
										autoCloseDelay: 6000, 
										template: 'success'
									});
									divNotification.jqxNotification('open');		
								}		
							});
						},
						'Cancel' : function() {
							$(this).dialog('close');
						}
					}
				});			
			});
		}		
		$('.subject').jqxTooltip({ content: 'Add to curriculum', theme: 'black', position: 'right', name: 'movieTooltip'});
	});
</script>