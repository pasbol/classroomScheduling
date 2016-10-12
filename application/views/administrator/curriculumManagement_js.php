<script type="text/javascript">
$(document).ready(function(){
	var theme = 'classic';
	var url = '<?php echo base_url().''.$manager; ?>/curriculumManagementJSON/';
    var source =
    {
        datatype: 'json',
        datafields: [
            { name: 'curriculumID', type: 'int'},
            { name: 'curriculumTitle', type: 'string'},
            { name: 'academicYear', type: 'string'},
            { name: 'courseID', type: 'int'},
            { name: 'course_code', type: 'string'},
            { name: 'course_title', type: 'string'},
            { name: 'department_code', type: 'string'}
        ],
        id: 'curriculumID',
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

    // initialize jqxGrid
    $('#jqxgrid').jqxGrid({
        width: 700,
        source: dataAdapter,
        theme: theme,                
        pageable: false,
        autoheight: false,
        sortable: true,
        altrows: true,
        enabletooltips: true,
        selectionmode: 'singlerow',
		filterable: true,
		showfilterrow: true,
		rendergridrows: function() {
			  return dataadapter.records;     
		},
		ready: function () { 
			addfilter(); 
		},
        selectionmode: 'singlerow',
        showstatusbar: true,
        renderstatusbar: function (statusbar) {
            // appends buttons to the status bar.
            var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
            var addButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/insert.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Insert</span></div>");
            var updateButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/update.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Update</span></div>");
            var deleteButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/delete.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Delete</span></div>");
            var reloadButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/reload.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Reload</span></div>");
            container.append(addButton);
            container.append(updateButton);
            container.append(deleteButton);
            container.append(reloadButton);
            statusbar.append(container);
            addButton.jqxButton({width: 85, height: 15});
            updateButton.jqxButton({width: 85, height: 15});
            deleteButton.jqxButton({width: 85, height: 15});
            reloadButton.jqxButton({width: 80, height: 15});

             // add new row.
            addButton.click(function (event) {
            	$('#jqxwindow').remove(); 
            	var container = $('<div id="jqxwindow"></div>');
				var windowContent = $('<div></div>');
				var windowButton = $('<div></div>');
				var okButton = $('<input type="button" style="margin-top: 15px; float: right;" value="OK" />');
				var cancelButton = $('<input type="button" style="margin-top: 15px; margin-left: 5px; float: right;" value="Cancel"/>');
				var curriculumTitleLabel = $('<label style="float: left; width: 35%;">Curriculum Title : </label>');
				var curriculumTitleInput = $('<input id="curriculumTitleInput" name="curriculumTitleInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');
				var academicYearLabel = $('<label style="float: left; width: 35%;">Academic Year: </label>');
				var academicYearInput = $('<input id="academicYearInput" name="academicYearInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');
				var courseLabel = $('<label style="float: left; width: 35%;">Course: </label>');
				var courseInput = $('<div id="courseInput"></div>');
				
				windowContent.append(curriculumTitleLabel);
				windowContent.append(curriculumTitleInput);				
				windowContent.append(academicYearLabel);
				windowContent.append(academicYearInput);
				windowContent.append(courseLabel);
				windowContent.append(courseInput);
				windowButton.append(cancelButton);
				windowButton.append(okButton);
				windowContent.append(windowButton);
				container.append(windowContent);

				curriculumTitleInput.jqxInput({ width: 280, height: 25, theme: theme});
				academicYearInput.jqxMaskedInput({ width: 280, height: 25, mask: '####-####', theme: theme});
				courseInput.jqxDropDownList({ 
					source: courseDropdownJSON(),
					theme: theme, 
					displayMember: 'course_code',
					valueMember: 'courseID',
					selectedIndex: -1,
					width: '282', 
					height: '25'
				});
				okButton.jqxButton({width: 50, height: 22, theme: theme});
				cancelButton.jqxButton({width: 65, height: 22, theme: theme});
				
				okButton.bind('click', function(event) {
					var postData = new Array();
					postData.push($('#curriculumTitleInput').val());
					postData.push($('#academicYearInput').val());
					postData.push($('#courseInput').val());
					var url = '<?php echo base_url() .''.$manager; ?>/curriculumManagementInsert/';
					$.post(url, { postData : postData }, 
						function(data) {
							container.jqxWindow('close');
							$('#jqxgrid').jqxGrid('updatebounddata');
						}
					);
				});
				cancelButton.click(function(event) {
					container.jqxWindow('close');
				});
				container.jqxWindow({                    
					title: 'Curriculum Management Insert', 
					resizable: false, 
					theme: theme, 
					width: 450,                        
					isModal: true,
					position: 'center',
					draggable: false                        
				});
            });
            
            // update selected row.
            updateButton.click(function (event) {
            	$('#jqxwindow').remove(); 
            	var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            	var rowdata = $('#jqxgrid').jqxGrid('getrowdata', rowindex);       

				var container = $("<div id='jqxwindow'></div>");
				var windowContent = $("<div></div>");
				var windowButton = $("<div></div>");
				var okButton = $("<input type='button' style='margin-top: 15px; float: right;' value='OK' id='okButton' />");
				var cancelButton = $("<input type='button' style='margin-top: 15px; margin-left: 5px; float: right;' value='Cancel' id='okButton' />");

				var curriculumTitleLabel = $('<label style="float: left; width: 35%;">Curriculum Title : </label>');
				var curriculumTitleInput = $('<input id="curriculumTitleInput" name="curriculumTitleInput" type="text" style="float: left; margin: 1px; width: 284px; height: 25px; padding: 1px;" text-indent: 1em;" value="'+rowdata['curriculumTitle']+'"></input>');
				var academicYearLabel = $('<label style="float: left; width: 35%;">Academic Year : </label>');
				var academicYearInput = $('<input id="academicYearInput" name="academicYearInput" type="text" style="float: left; margin: 1px; width: 284px; height: 25px; padding: 1px;" text-indent: 1em;" value="'+rowdata['academicYear']+'"></input>');
				var oldCourse = $('<input id="oldCourse" type="hidden" value="'+rowdata['courseID']+'"></input>');
				var courseLabel = $('<label style="float: left; width: 35%;">Course: </label>');
				var courseInput = $('<div id="courseInput"></div>');
				
				windowContent.append(curriculumTitleLabel);
				windowContent.append(curriculumTitleInput);				
				windowContent.append(academicYearLabel);
				windowContent.append(academicYearInput);
				windowContent.append(oldCourse);
				windowContent.append(courseLabel);
				windowContent.append(courseInput);										
				windowButton.append(cancelButton);
				windowButton.append(okButton);
				windowContent.append(windowButton);
				container.append(windowContent);	

				curriculumTitleInput.jqxInput({ width: 280, height: 25, theme: theme});
				academicYearInput.jqxInput({ width: 280, height: 25, theme: theme});
				courseInput.jqxDropDownList({ 
					source: courseDropdownJSON(),
					theme: theme, 
					displayMember: 'course_code',
					valueMember: 'courseID',
					selectedIndex: -1,
					width: '282', 
					height: '25'
				});				
				okButton.jqxButton({ theme: theme, width: 50, height: 22 });
				cancelButton.jqxButton({ theme: theme, width: 65, height: 22 });

				okButton.bind('click', function(event) {
					var postData = new Array();
					postData.push($('#curriculumTitleInput').val());
					postData.push($('#academicYearInput').val());
					postData.push($('#oldCourse').val());
					postData.push($('#courseInput').val());

					var url = '<?php echo base_url().''.$manager; ?>/curriculumManagementUpdate/';
					$.post(url, { postID : rowdata['curriculumID'], postData: postData }, 
						function(data) {
							container.jqxWindow('close');
							$('#jqxgrid').jqxGrid('updatebounddata');
						}
					);
					
				});
				cancelButton.click(function(event) {
					container.jqxWindow('close');
				});
				container.jqxWindow({                    
					title: 'Curriculum Management Update', 
					resizable: false, 
					theme: theme, 
					width: 450,                         
					isModal: true,                        
					draggable: false                        
				}); 			
            });
            
            // delete selected row.
            deleteButton.click(function (event) {
            	$('#jqxwindow').remove(); 
            	var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            	var rowdata = $('#jqxgrid').jqxGrid('getrowdata', rowindex);
                
            	var container = $('<div id="jqxwindow"></div>');
				var windowContent = $('<div></div>');
				var windowButton = $('<div></div>');
				var data = $('<div></div>');
				var dataNote = $('<p class="note">Note: Action cannot be undone.</p>');
				var dataContent = $('<p>Do you really want to delete? ['+rowdata['curriculumTitle']+' | '+rowdata['academicYear']+']</p>');
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
					var url = '<?php echo base_url().''.$manager; ?>/curriculumManagementDelete/';
					$.post(url, { postID : rowdata['curriculumID']}, 
						function(data) {
							container.jqxWindow('close');
							$('#jqxgrid').jqxGrid('updatebounddata');
						}
					);
				});
				cancelButton.click(function(event) {
					container.jqxWindow('close');
				});
				container.jqxWindow({
					title: 'Curriculum Management Delete',
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
            	 $('#jqxgrid').jqxGrid({ source: dataAdapter });
            });
        },
        columns: [
          { text: 'Curriculum Title', datafield: 'curriculumTitle'},
          { text: 'Course', datafield: 'course_code'},
          { text: 'Academic Year', datafield: 'academicYear'},
          { text: 'Department', datafield: 'department_code', filtertype: 'list'}
        ]
    });

    $('#jqxgrid').jqxGrid('autoresizecolumn', 'curriculumTitle'); 
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'course_code');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'academicYear');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'department_code');

    function courseDropdownJSON(){
    	var url = '<?php echo base_url().''.$manager; ?>/courseDropdownJSON/';
        var source =
        {
            datatype: 'json',
            datafields: [
                { name: 'courseID', type: 'int' },
                { name: 'course_code', type: 'string' }
            ],
            id: 'courseID',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source, {
            downloadComplete: function (data, status, xhr) { },
            loadComplete: function (data) { },
            loadError: function (xhr, status, error) { }
        });
        return dataAdapter;
    }   
});
</script>
