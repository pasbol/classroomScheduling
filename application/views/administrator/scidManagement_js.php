<script type="text/javascript">
$(document).ready(function(){

	var theme = 'classic';
	var url = '<?php echo base_url().''.$manager; ?>/scidManagementJSON/';

    var source =
    {
        datatype: 'json',
        datafields: [
            { name: 'scidID', type: 'int' },
            { name: 'scid_start', type: 'int' },
            { name: 'scid_end', type: 'int' },
            { name: 'departmentID', type: 'int' },
            { name: 'department_code', type: 'string' }
        ],
        id: 'scidID',
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
            addButton.jqxButton({width: 85, height: 15, theme: theme});
            updateButton.jqxButton({width: 85, height: 15, theme: theme});
            deleteButton.jqxButton({width: 85, height: 15, theme: theme});
            reloadButton.jqxButton({width: 80, height: 15, theme: theme});

             // add new row.
            addButton.click(function (event) {
            	$('#jqxwindow').remove(); 
            	var container = $('<div id="jqxwindow"></div>');
				var windowContent = $('<div></div>');
				var windowButton = $('<div></div>');
				var okButton = $('<input type="button" style="margin-top: 15px; float: right;" value="OK" />');
				var cancelButton = $('<input type="button" style="margin-top: 15px; margin-left: 5px; float: right;" value="Cancel"/>');
				var startLabel = $('<label style="float: left; width: 35%;">Start : </label>');
				var startInput = $('<input id="startInput" name="startInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');
				var endLabel = $('<label style="float: left; width: 35%;">End : </label>');
				var endInput = $('<input id="endInput" name="endInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');
				var departmentLabel = $('<label style="float: left; width: 35%;">Department: </label>');
				var departmentInput = $('<div id="departmentInput"></div>');

				windowContent.append(startLabel);
				windowContent.append(startInput);
				windowContent.append(endLabel);
				windowContent.append(endInput);
				windowContent.append(departmentLabel);
				windowContent.append(departmentInput);
				windowButton.append(cancelButton);
				windowButton.append(okButton);
				windowContent.append(windowButton);
				container.append(windowContent);

				startInput.jqxInput({ width: 280, height: 25, theme: theme});
				endInput.jqxInput({ width: 280, height: 25, theme: theme});
				departmentInput.jqxDropDownList({ 
					source: departmentJSON(),
					theme: theme, 
					displayMember: 'department_code',
					valueMember: 'departmentID',
					selectedIndex: -1,
					width: '282', 
					height: '25'
				});
				
				okButton.jqxButton({width: 50, height: 22, theme: theme});
				cancelButton.jqxButton({width: 65, height: 22, theme: theme});
				
				okButton.bind('click', function(event) {
					var postData = new Array();
					postData.push($('#startInput').val());
					postData.push($('#endInput').val());
					postData.push($('#departmentInput').val());
					var url = '<?php echo base_url() .''.$manager; ?>/scidManagementInsert/';
					$.post(url, { postData : postData}, 
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
					title: 'SC ID Management Insert', 
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

				var startLabel = $('<label style="float: left; width: 35%;">Start : </label>');
				var startInput = $('<input id="startInput" name="startInput" type="text" style="float: left; margin: 1px; width: 284px; height: 25px; padding: 1px;" text-indent: 1em;" value="'+rowdata['start']+'"></input>');
				var endLabel = $('<label style="float: left; width: 35%;">End : </label>');
				var endInput = $('<input id="endInput" name="endInput" type="text" style="float: left; margin: 1px; width: 282px; height: 50px; padding: 1px;" text-indent: 1em;" value="'+rowdata['end']+'"></input>');
				var oldDepartment = $('<input id="oldDepartment" type="hidden" value="'+rowdata['departmentID']+'"></input>');
				var departmentLabel = $('<label style="float: left; width: 35%;">Department: </label>');
				var departmentInput = $('<div id="departmentInput"></div>');					

				windowContent.append(startLabel);
				windowContent.append(startInput);
				windowContent.append(endLabel);
				windowContent.append(endInput);
				windowContent.append(departmentLabel);
				windowContent.append(departmentInput);
				windowContent.append(oldDepartment);
				windowButton.append(cancelButton);
				windowButton.append(okButton);
				windowContent.append(windowButton);
				container.append(windowContent);	

				startInput.jqxInput({ width: 280, height: 25, theme: theme});
				endInput.jqxInput({ width: 280, height: 25, theme: theme});
				departmentInput.jqxDropDownList({ 
					source: departmentJSON(),
					theme: theme, 
					displayMember: 'department_code',
					valueMember: 'departmentID',
					selectedIndex: -1,
					width: '282', 
					height: '25'
				});
				
				okButton.jqxButton({ theme: theme, width: 50, height: 22 });
				cancelButton.jqxButton({ theme: theme, width: 65, height: 22 });

				okButton.bind('click', function(event) {
					var postData = new Array();
					postData.push($('#startInput').val());
					postData.push($('#endInput').val());
					postData.push($('#departmentInput').val());
					postData.push($('#oldDepartment').val());

					var url = '<?php echo base_url().''.$manager; ?>/scidManagementUpdate/';
					$.post(url, { postID : rowdata['scidID'], postData: postData }, 
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
					title: 'SC ID Management Update', 
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
				var dataContent = $('<p>Do you really want to delete? ['+rowdata['start']+' | '+rowdata['end']+ ','+rowdata['department_code']+ ']</p>');
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
					var url = '<?php echo base_url().''.$manager; ?>/scidManagementDelete/';
					$.post(url, { postID : rowdata['scidID'] }, 
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
					title: 'SC ID Management Delete',
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
          { text: 'Start', datafield: 'scid_start' },
          { text: 'End', datafield: 'scid_end' },
          { text: 'Department', datafield: 'department_code', filtertype: 'list' }
        ]
    });

    $('#jqxgrid').jqxGrid('autoresizecolumn', 'scid_start'); 
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'scid_end');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'department_code');

    function departmentJSON(){
    	var url = '<?php echo base_url().''.$manager; ?>/departmentManagementJSON/';
        var source =
        {
            datatype: 'json',
            datafields: [
                { name: 'departmentID', type: 'int' },
                { name: 'department_code', type: 'string' },
                { name: 'department_title', type: 'string' }
            ],
            id: 'departmentID',
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
