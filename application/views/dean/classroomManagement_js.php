<script type="text/javascript">
$(document).ready(function(){
	var theme = 'classic';
	var url = '<?php echo base_url().''.$manager; ?>/classroomManagementJSON/';

    var source =
    {
        datatype: 'json',
        datafields: [
            { name: 'roomID', type: 'int' },
            { name: 'room', type: 'string' },
            { name: 'description', type: 'string' }
        ],
        id: 'roomID',
        url: url
    };

	// filter functions
	var addfilter = function () {
		var filtergroup = new $.jqx.filter();
		var filter_or_operator = 1;
		var filtervalue = '';
		var filtercondition = 'contains';
		var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
		filtervalue = '';
		filtercondition = 'starts_with';
		var filter2 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
		filtergroup.addfilter(filter_or_operator, filter1);
		filtergroup.addfilter(filter_or_operator, filter2);
		$("#jqxgrid").jqxGrid('addfilter', '', filtergroup);
		$("#jqxgrid").jqxGrid('applyfilters');
	}

    var dataAdapter = new $.jqx.dataAdapter(source, {
        downloadComplete: function (data, status, xhr) { },
        loadComplete: function (data) { },
        loadError: function (xhr, status, error) { }
    });

    // initialize init nested jqxgrid
	var initRoomSchedule =  function (index, parentElement, gridElement, record) {
		var id = record.uid.toString();    
		var grid = $($(parentElement).children()[0]);

		var roomID = record['roomID'].toString();
		var room = record['room'].toString();
		var description = record['description'].toString();
		
		if (grid != null) { 
			grid.jqxGrid({
				source: initClassroomManagementJSON(roomID), 
                theme: theme, 
                altrows: true,               
                enabletooltips: false,
                width: '95%', 
				height: 330, 
                sortable: true,
                filterable: true,
                showfilterrow: true,
				showstatusbar: true,
				renderstatusbar: function (statusbar) {
					// appends buttons to the status bar.
					var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
					var addButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/insert.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Create New Schedule</span></div>");
					var deleteButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/delete.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Delete Created Schedule</span></div>");
					var reloadButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/reload.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Reload</span></div>");
					container.append(addButton);
					container.append(deleteButton);
					container.append(reloadButton);
					statusbar.append(container);
					addButton.jqxButton({width: 175, height: 15, theme: theme});
					deleteButton.jqxButton({width: 200, height: 15, theme: theme});
					reloadButton.jqxButton({width: 80, height: 15, theme: theme});

					 // add new row.
					addButton.click(function (event) {
						$('#jqxwindow').remove(); 
						var container = $('<div id="jqxwindow"></div>');
						var windowContent = $('<div></div>');
						var windowButton = $('<div></div>');
						var okButton = $('<input type="button" style="margin-top: 15px; float: right;" value="OK" />');
						var cancelButton = $('<input type="button" style="margin-top: 15px; margin-left: 5px; float: right;" value="Cancel"/>');

						var roomInput = $('<input id="roomInput" type="hidden" value="'+roomID+'"/>');
						var dayLabel = $('<label style="float: left; width: 35%; text-align: center;">Day(s): </label>');
						var dayInput = $('<div style="margin: 3px;" id="daysInput"></div>');
						var startTimeLabel = $('<label style="float: left; width: 35%; text-align: center;">Start Time: </label>');
						var startTimeInput = $('<div style="margin: 3px;" id="startTimeInput"></div>');
						var endTimeLabel = $('<label style="float: left; width: 35%; text-align: center;">End Time: </label>');
						var endTimeInput = $('<div style="margin: 3px;" id="endTimeInput"></div>');
						var subjectLabel = $('<label style="float: left; width: 35%; text-align: center;">Subject: </label>');
						var subjectInput = $('<div style="margin: 3px;" id="subjectInput"></div>');

						windowContent.append(roomInput);
						windowContent.append(subjectLabel);
						windowContent.append(subjectInput);	
						windowContent.append(dayLabel);
						windowContent.append(dayInput);	
						windowContent.append(startTimeLabel);
						windowContent.append(startTimeInput);
						windowContent.append(endTimeLabel);
						windowContent.append(endTimeInput);
						windowButton.append(cancelButton);
						windowButton.append(okButton);
						windowContent.append(windowButton);
						container.append(windowContent);										

						var dayArr = ['M','T','W','TH','F','SAT','SUN'];
						dayInput.jqxDropDownList({ 
							//source: dayDropdownJSON(),
							source: dayArr,
							theme: theme, 
							placeHolder: 'Select Day',
							displayMember: 'day',
							valueMember: 'dayID',
							filterPlaceHolder: 'day',
							checkboxes: true,
							autoDropDownHeight: true,
							 autoItemsHeight: true,
							selectedIndex: -1,
							width: '282', 
							height: '25'
						});	
						
						startTimeInput.jqxDateTimeInput({ width: '282px', height: '25px', formatString: 'HH:mm tt', showTimeButton: true, showCalendarButton: false});
						endTimeInput.jqxDateTimeInput({ width: '282px', height: '25px', formatString: 'HH:mm tt', showTimeButton: true, showCalendarButton: false});
						
						/*timeInput.jqxDropDownList({ 
							source: timeDropdownJSON(),
							theme: theme, 
							filterable: true,
							displayMember: 'time',
							valueMember: 'timeID',
							placeHolder: 'Select Time',
							filterPlaceHolder: 'start time',
							selectedIndex: -1,
							width: '282', 
							height: '25'
						});	*/

						subjectInput.jqxDropDownList({ 
							source: subjectDropdownJSON(),
							theme: theme, 
							filterable: true,
							displayMember: 'subject',
							valueMember: 'subjectOfferedID',
							placeHolder: 'Select Subject',
							filterPlaceHolder: 'sc_id',
							selectedIndex: -1,
							width: '282', 
							height: '25'
						});							
						
						okButton.jqxButton({width: 50, height: 22, theme: theme});
						cancelButton.jqxButton({width: 65, height: 22, theme: theme});
						
						okButton.bind('click', function(event) {
						 	var items = $('#daysInput').jqxDropDownList('getCheckedItems');
							var checkedItems = new Array();
							$.each(items, function (index) {
								checkedItems.push(this.value);                          
							});
							var now = new Date();
							var postData = new Array();
							postData.push($('#roomInput').val());
							postData.push($('#subjectInput').val());
							postData.push($('#startTimeInput').val());
							postData.push($('#endTimeInput').val());
							postData.push(now);

							for(var i = 0; i < checkedItems.length; i++){
								var url = '<?php echo base_url() .''.$manager; ?>/classroomManagementScheduleCreate/';
								$.post(url, { postData : postData, days: checkedItems[i]}, 
									function(data) {
										container.jqxWindow('close');
										grid.jqxGrid('updatebounddata');
										$('#message').html(data);
									}
								);
							}
						});
						cancelButton.click(function(event) {
							container.jqxWindow('close');
						});
						container.jqxWindow({                    
							title: 'Create Schedule for '+room+' - '+description, 
							resizable: false, 
							theme: theme, 
							width: 450,                        
							isModal: true,
							position: 'center',
							draggable: true                        
						});
					});

		            // delete selected row.
		            deleteButton.click(function (event) {
		            	$('#jqxwindow').remove(); 
		            	var rowindex = grid.jqxGrid('getselectedrowindex');
		            	var rowdata = grid.jqxGrid('getrowdata', rowindex);
		                
		            	var container = $('<div id="jqxwindow"></div>');
						var windowContent = $('<div></div>');
						var windowButton = $('<div></div>');
						var data = $('<div></div>');
						var dataNote = $('<p class="note">Note: Action cannot be undone.</p>');
						var dataContent = $('<p>Do you really want to delete? ['+rowdata['sc_id']+' | '+rowdata['sc_code']+' '+rowdata['subject_title']+' '+rowdata['stime']+'-'+rowdata['etime']+' '+rowdata['day']+' ]</p>');
						var okButton = $('<input type="button" style="margin-top: 15px; float: right;" value="OK" />');
						var cancelButton = $('<input type="button" style="margin-top: 15px; margin-left: 5px; float: right;" value="Cancel"/>');

						data.append(dataNote);
						data.append(dataContent);
						windowButton.append(cancelButton);
						windowButton.append(okButton);
						windowContent.append(data);
						windowContent.append(windowButton);
						container.append(windowContent);

						okButton.jqxButton({width: 50, height: 22, theme: theme});
						cancelButton.jqxButton({width: 65, height: 22, theme: theme});
						
						okButton.bind('click', function(event) {
							var postID = rowdata['departmentSubjectScheduleID'];
							var url = '<?php echo base_url().''.$manager; ?>/classroomManagementScheduleDelete/';
							$.post(url, { postID : postID}, 
								function(data) {
									container.jqxWindow('close');
									grid.jqxGrid('updatebounddata');
								}
							);
						});
						cancelButton.click(function(event) {
							container.jqxWindow('close');
						});
						container.jqxWindow({
							title: 'Delete Classroom Schedule',
							resizable: false,
							theme: theme,
							width: 450,
							isModal: true,
							position: 'center',
							draggable: false
						});
		            });
							  
					// reload jqxgrid.
					reloadButton.click(function (event) {
						 grid.jqxGrid('updatebounddata');
					});
				},
				columns: [
					{ text: 'Department', datafield: 'department_code', filtertype: 'list'},
					{ text: 'SC_ID', datafield: 'sc_id'},
					{ text: 'SC CODE', datafield: 'sc_code'},
					{ text: 'Descriptive Title', datafield: 'subject_title'},
					{ text: 'Start Time', datafield: 'startTime'},
					{ text: 'End Time', datafield: 'endTime'},
					{ text: 'Day', datafield: 'day', filtertype: 'list'},
					{ text: 'Generated', datafield: 'generated'}
				]
			});
			
            grid.bind("bindingcomplete", function (event) {
            	grid.jqxGrid('autoresizecolumn', 'sc_id');            	            
            	grid.jqxGrid('autoresizecolumn', 'sc_code');
                grid.jqxGrid('autoresizecolumn', 'subject_title');  
                grid.jqxGrid('autoresizecolumn', 'startTime');
                grid.jqxGrid('autoresizecolumn', 'endTime');
                grid.jqxGrid('autoresizecolumn', 'day');   
                grid.jqxGrid('autoresizecolumn', 'generated');                      
            });	
		}
	};

    // initialize jqxGrid
    $('#jqxgrid').jqxGrid({
        width: '85%',
        source: dataAdapter,
        theme: theme,                
        pageable: true,
        autoheight: true,
        sortable: true,
        altrows: true,
        enabletooltips: true,
        editable: false,
		filterable: true,
		showfilterrow: true,
        selectionmode: 'singlerow',
		rendergridrows: function() {
			  return dataadapter.records;     
		},
		ready: function () { 
			addfilter(); 
		},
        showstatusbar: true,
        renderstatusbar: function (statusbar) {
            // appends buttons to the status bar.
            var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
 			var reloadButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/reload.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Reload</span></div>");
            container.append(reloadButton);
            statusbar.append(container);
            reloadButton.jqxButton({width: 80, height: 15, theme: theme});
                      
            // reload jqxgrid.
            reloadButton.click(function (event) {
            	 $('#jqxgrid').jqxGrid({ source: dataAdapter });
            });
        },
		rowdetails: true,
		initrowdetails: initRoomSchedule,
		rowdetailstemplate: { 
			rowdetails: "<div id='grid' style='margin-top: 6px;'></div>", 
			rowdetailsheight: 350, 
			rowdetailshidden: true 
		},
        columns: [
          { text: 'Classroom', datafield: 'room' },
          { text: 'Description', datafield: 'description', filtertype: 'list' }
        ]
    });

    $('#jqxgrid').jqxGrid('autoresizecolumn', 'room'); 
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'description');

	function initClassroomManagementJSON(roomID){
		var url = '<?php echo base_url().''.$manager; ?>/classroomManagementScheduleJSON/'+roomID;
		var source =  {
			datatype: 'json',
			datafields: [
				{ name: 'departmentSubjectScheduleID', type: 'int'},
				{ name: 'day', type: 'string'},
				{ name: 'startTime', type: 'string'},
				{ name: 'endTime', type: 'string'},
				{ name: 'sc_id', type: 'string'},
				{ name: 'sc_code', type: 'string'},
				{ name: 'subject_title', type: 'string'}, 
				{ name: 'department_code', type: 'string'},
				{ name: 'generated', type: 'string'}
			],
			id: 'departmentSubjectScheduleID',
			url: url 
		};
		var dataAdapter = new $.jqx.dataAdapter(source, {
			downloadComplete: function (data, status, xhr) { },
			loadComplete: function (data) { },
			loadError: function (xhr, status, error) { }
		});
		return dataAdapter;
	} 
	
	// dropdowns
 	function subjectDropdownJSON(){
    	var url = '<?php echo base_url().''.$manager; ?>/subjectDropdownJSON/';
        var source =
        {
            datatype: 'json',
            datafields: [
                { name: 'subjectOfferedID', type: 'int' },
                { name: 'subject', type: 'string' }
            ],
            id: 'subjectOfferedID',
            url: url
        };
        var dataAdapter = new $.jqx.dataAdapter(source, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) { },
            loadError: function (xhr, status, error) { }
        });
        return dataAdapter;
    }       
	/*function dayDropdownJSON(){
    	var url = '<?php echo base_url().''.$manager; ?>/dayDropdownJSON/';
        var source =
        {
            datatype: 'json',
            datafields: [
                { name: 'dayID', type: 'int' },
                { name: 'day', type: 'string' }
            ],
            id: 'dayID',
            url: url
        };
        var dataAdapter = new $.jqx.dataAdapter(source, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) { },
            loadError: function (xhr, status, error) { }
        });
        return dataAdapter;
    }	
	function timeDropdownJSON(){
    	var url = '<?php echo base_url().''.$manager; ?>/timeDropdownJSON/';
        var source =
        {
            datatype: 'json',
            datafields: [
                { name: 'timeID', type: 'int' },
                { name: 'time', type: 'string' }
            ],
            id: 'timeID',
            url: url
        };
        var dataAdapter = new $.jqx.dataAdapter(source, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) { },
            loadError: function (xhr, status, error) { }
        });
        return dataAdapter;
    }*/	
});
</script>













