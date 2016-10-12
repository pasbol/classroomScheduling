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
				var roomLabel = $('<label style="float: left; width: 35%;">Classroom : </label>');
				var roomInput = $('<input id="roomInput" name="roomInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');
				var descriptionLabel = $('<label style="float: left; width: 35%;">Description : </label>');
				var descriptionInput = $('<div id="descriptionInput"></div>');

				windowContent.append(roomLabel);
				windowContent.append(roomInput);
				windowContent.append(descriptionLabel);
				windowContent.append(descriptionInput);
				windowButton.append(cancelButton);
				windowButton.append(okButton);
				windowContent.append(windowButton);
				container.append(windowContent);

				var description = ['Lab','Lec'];
				roomInput.jqxInput({ width: 280, height: 25, theme: theme});
				descriptionInput.jqxDropDownList({ 
					source: description,
					autoDropDownHeight: true,
					theme: theme, 
					selectedIndex: -1,
					width: '282', 
					height: '25'
				});
				
				okButton.jqxButton({width: 50, height: 22, theme: theme});
				cancelButton.jqxButton({width: 65, height: 22, theme: theme});
				
				okButton.bind('click', function(event) {
					var postData = new Array();
					postData.push($('#roomInput').val());
					postData.push($('#descriptionInput').val());
					var url = '<?php echo base_url() .''.$manager; ?>/classroomManagementInsert/';
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
					title: 'Insert Classroom', 
					resizable: false, 
					//headerHeight: 30,
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

				var roomLabel = $('<label style="float: left; width: 35%;">Classroom : </label>');
				var roomInput = $('<input id="roomInput" name="roomInput" type="text" style="float: left; margin: 1px; width: 284px; height: 25px; padding: 1px;" text-indent: 1em;" value="'+rowdata['room']+'"></input>');
				var descriptionLabel = $('<label style="float: left; width: 35%;">Description : </label>');
				var descriptionInput = $('<div id="descriptionInput"></div>');
		
				windowContent.append(roomLabel);
				windowContent.append(roomInput);										
				windowContent.append(descriptionLabel);
				windowContent.append(descriptionInput);
				windowButton.append(cancelButton);
				windowButton.append(okButton);
				windowContent.append(windowButton);
				container.append(windowContent);	

				var description = [rowdata['description'],'Lab','Lec'];
				roomInput.jqxInput({ width: 280, height: 25, theme: theme});
				descriptionInput.jqxDropDownList({ 
					source: description,
					autoDropDownHeight: true,
					theme: theme, 
					selectedIndex: 0,
					width: '282', 
					height: '25'
				});
				
				okButton.jqxButton({ theme: theme, width: 50, height: 22 });
				cancelButton.jqxButton({ theme: theme, width: 65, height: 22 });

				okButton.bind('click', function(event) {
					var postData = new Array();
					postData.push($('#roomInput').val());
					postData.push($('#descriptionInput').val());

					var url = '<?php echo base_url().''.$manager; ?>/classroomManagementUpdate/';
					$.post(url, { postID : rowdata['roomID'], postData: postData }, 
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
					title: 'Update Classroom', 
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
				var dataContent = $('<p>Do you really want to delete? ['+rowdata['room']+' | '+rowdata['description']+']</p>');
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
					var postID = rowdata['roomID'];
					var url = '<?php echo base_url().''.$manager; ?>/classroomManagementDelete/';
					$.post(url, { postID : postID}, 
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
					title: 'Delete Classroom',
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
          { text: 'Classroom', datafield: 'room' },
          { text: 'Description', datafield: 'description', filtertype: 'list' }
        ]
    });

    $('#jqxgrid').jqxGrid('autoresizecolumn', 'room'); 
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'description');
});
</script>













