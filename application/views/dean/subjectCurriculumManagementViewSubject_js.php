<script type="text/javascript">
	$(function () {	
		
		offerSubjectCurriculum();
		removeSubjectCurriculum();
		var theme = 'classic';

		$('div#selected p').css({
			'font-size' : '18px',
			'font-weight' : 'bold',
			'margin-top' : '15px',
			'text-indent' : '1.26em'
		});
		$('a.link').css({
			'font-size' : '14px',
			'font-weight' : 'normal',
			'float' : 'right',
			'margin-bottom' : '5px',
			'margin-top' : '-20px'
		});
		
		$('h5').css({
			'font-weight': 'bolder',
			'font-size': '13px',
			'color': '#333333',
			'margin-bottom' : '15px',
			'margin-top' : '15px'
		});
		$('div#heading').css({
			'margin-bottom' : '15px'
		});
		$('div#heading a').css({
			'float' : 'left',
			'margin-top' : '-18px',
			'margin-left' : '130px'
		});
		$('table').css({
			'width': '100%'
		});
		$('table thead').css({
			'background': '#800000',
			'border-top': 'solid 2px #191919',
			'border-bottom': 'solid 1px #191919'
		});
		$('table thead tr th').css({
			'padding': '5px',
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
		$('span.offer, span.remove').css({ 'cursor' : 'pointer' });
		
		$("span.offer").jqxTooltip({ content: 'Offer/Open Subject', position: 'right', name: 'movieTooltip', theme: 'black'});
		$("span.remove").jqxTooltip({ content: 'Remove Subject<br/>from Curriculum', position: 'right', name: 'movieTooltip', theme: 'black'});	
		
		function offerSubjectCurriculum(){
			$('.offer').bind('click', function(){
				var ids = new String($(this).attr('id'));
				ids = ids.split('_');
				var curriculumID = ids[0];
				var scCode = ids[1];
				var subjectTitle = ids[2];
				
				$('#offerSubjectCurriculumDialog').remove();
				var dialog = '<div id="offerSubjectCurriculumDialog"><p class="confirmationMessage">Do you really want to offer this subject?<br/>[ '+scCode+'-'+subjectTitle+' ]</p>';
				dialog += '<label style="font-weight: normal; margin-top: 15px;">SC ID <font style="color: #8b0000; font-weight: normal ">(required)</font>: </label>';
				dialog += '<input id="scidInput" type="text" class="form-control"/></div>';
					
				$('body').append(dialog);
				createDialog('offerSubjectCurriculumDialog', 'Offer Subject Confirmation', 500, 300);
				$('#offerSubjectCurriculumDialog').dialog({
					buttons : {
						'OK' : function() {
							var postData = new Array();
							postData.push(curriculumID);
							postData.push($('#scidInput').val());
							var url = '<?php echo base_url() .''.$manager; ?>/subjectCurriculumManagementOfferSubject/';					
							$.post(url, { postData : postData }, function(data) {
								$('#offerSubjectCurriculumDialog').dialog('close');
								if(data == 'lesserThan'){
									var divNotification = $('<div id="divNotification">Invalid SC ID.</div>');
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
								}else if(data == 'greaterThan'){
									var divNotification = $('<div id="divNotification">Invalid SC ID.</div>');
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
								}else if(data == 'exist'){
									var divNotification = $('<div id="divNotification">Invalid SC ID. SC ID exist.</div>');
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
								}else if(data == 'empty'){
									var divNotification = $('<div id="divNotification">SC ID is required.</div>');
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
		
		function removeSubjectCurriculum() {
			$('.remove').bind('click', function() {
				var thisRow = $(this).parent().parent();
				var subjectID = new String($(this).attr('id'));
				$('#removeSubjectCurriculumDialog').remove();
				var dialog = '<div id="removeSubjectCurriculumDialog"><p class="note">Note: Action cannot be undone.</p>';
				dialog += '<p class="confirmationMessage">Do you really want to remove subject?</p></div>';
				
				$('body').append(dialog);
				createDialog('removeSubjectCurriculumDialog', 'Remove Confirmation', 500, 220);
				$('#removeSubjectCurriculumDialog').dialog({
					buttons : {
						'OK' : function() {
							var url = '<?php echo base_url() .''.$manager; ?>/subjectCurriculumManagementRemoveSubject/'+subjectID;
							$.post(url, { }, function(data) {
								thisRow.fadeTo('slow',0.7, function(){
									thisRow.remove();
								});
								$('#removeSubjectCurriculumDialog').dialog('close');
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
							});
						},
						'Cancel' : function() {
							$(this).dialog('close');
						}
					}
				});			
			});
		}
	});
</script>