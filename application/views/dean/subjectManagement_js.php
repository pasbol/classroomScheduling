<script type="text/javascript">
$(document).ready(function(){

	var theme = 'classic';
	var url = '<?php echo base_url().''.$manager; ?>/subjectManagementJSON/';
    var source =
    {
        datatype: 'json',
        datafields: [
            { name: 'subjectID', type: 'id' },
            { name: 'sc_code', type: 'string' },
            { name: 'subject_title', type: 'string' },
            { name: 'units', type: 'string' },
            { name: 'lab', type: 'int' },
            { name: 'lec', type: 'int' }
        ],
        id: 'subjectID',
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
        width: '85%',
        source: dataAdapter,
        theme: theme,                
        pageable: false,
        autoheight: false,
        sortable: true,
        altrows: true,
        enabletooltips: true,
        editable: true,
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
				var sccodeLabel = $('<label style="float: left; width: 35%;">SC_CODE : </label>');
				var sccodeInput = $('<input id="sccodeInput" name="sccodeInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');
				var subjecttitleLabel = $('<label style="float: left; width: 35%;">Subject Title : </label>');
				var subjecttitleInput = $('<input id="subjecttitleInput" name="subjecttitleInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');
				var unitsLabel = $('<label style="float: left; width: 35%;">Units : </label>');
				var unitsInput = $('<input id="unitsInput" name="unitsInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');
				var labLabel = $('<label style="float: left; width: 35%;">Lab : </label>');
				var labInput = $('<input id="labInput" name="labInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');
				var lecLabel = $('<label style="float: left; width: 35%;">Lec : </label>');
				var lecInput = $('<input id="lecInput" name="lecInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');

				windowContent.append(sccodeLabel);
				windowContent.append(sccodeInput);
				windowContent.append(subjecttitleLabel);
				windowContent.append(subjecttitleInput);
				windowContent.append(unitsLabel);
				windowContent.append(unitsInput);
				windowContent.append(labLabel);
				windowContent.append(labInput);
				windowContent.append(lecLabel);
				windowContent.append(lecInput);
				windowButton.append(cancelButton);
				windowButton.append(okButton);
				windowContent.append(windowButton);
				container.append(windowContent);

				sccodeInput.jqxInput({ width: 280, height: 25, theme: theme});
				subjecttitleInput.jqxInput({ width: 280, height: 25, theme: theme});
				unitsInput.jqxInput({ width: 280, height: 25, theme: theme});
				labInput.jqxInput({ width: 280, height: 25, theme: theme});
				lecInput.jqxInput({ width: 280, height: 25, theme: theme});
				
				okButton.jqxButton({width: 50, height: 22, theme: theme});
				cancelButton.jqxButton({width: 65, height: 22, theme: theme});
				
				okButton.bind('click', function(event) {
					var postData = new Array();
					postData.push($('#sccodeInput').val());
					postData.push($('#subjecttitleInput').val());
					postData.push($('#unitsInput').val());
					postData.push($('#labInput').val());
					postData.push($('#lecInput').val());
					var url = '<?php echo base_url() .''.$manager; ?>/subjectManagementInsert/';
					$.post(url, { postData : postData}, 
						function(data) {
							container.jqxWindow('close');
							if(data == 'exist'){
								var divNotification = $('<div id="divNotification">SC CODE Exist. </div>');
								divNotification.jqxNotification({
									width: 300, 
									position: 'top-right', 
									opacity: 1.28,
									autoOpen: false, 
									animationOpenDelay: 1000, 
									autoClose: true, 
									autoCloseDelay: 6000, 
									template: 'warning'
								});
								divNotification.jqxNotification('open');	
							}else if(data == 'empty'){	
								var divNotification = $('<div id="divNotification">All fields are required.</div>');
								divNotification.jqxNotification({
									width: 300, 
									position: 'top-right', 
									opacity: 1.28,
									autoOpen: false, 
									animationOpenDelay: 1000, 
									autoClose: true, 
									autoCloseDelay: 6000, 
									template: 'warning'
								});
								divNotification.jqxNotification('open');							
							}else{
								var divNotification = $('<div id="divNotification">Successfully inserted new subject.</div>');
								divNotification.jqxNotification({
									width: 300, 
									position: 'top-right', 
									opacity: 1.28,
									autoOpen: false, 
									animationOpenDelay: 1000, 
									autoClose: true, 
									autoCloseDelay: 6000, 
									template: 'success'
								});
								divNotification.jqxNotification('open');		
							}
							$('#jqxgrid').jqxGrid('updatebounddata');
						}
					);
				});
				cancelButton.click(function(event) {
					container.jqxWindow('close');
				});
				container.jqxWindow({                    
					title: 'Subject Management Insert', 
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

				var sccodeLabel = $('<label style="float: left; width: 35%;">SC CODE : </label>');
				var sccodeInput = $('<input id="sccodeInput" name="sccodeInput" type="text" style="float: left; margin: 1px; width: 284px; height: 25px; padding: 1px;" text-indent: 1em;" value="'+rowdata['sc_code']+'"/>');
				var subjecttitleLabel = $('<label style="float: left; width: 35%;">Subject Title : </label>');
				var subjecttitleInput = $('<input id="subjecttitleInput" name="subjecttitleInput" type="text" style="float: left; margin: 1px; width: 284px; height: 25px; padding: 1px;" text-indent: 1em;" value="'+rowdata['subject_title']+'"/>');
				var unitsLabel = $('<label style="float: left; width: 35%;">Units : </label>');
				var unitsInput = $('<input id="unitsInput" name="unitsInput" type="text" style="float: left; margin: 1px; padding: 1px;" value="'+rowdata['units']+'"/>');
				var labLabel = $('<label style="float: left; width: 35%;">Lab : </label>');
				var labInput = $('<input id="labInput" name="labInput" type="text" style="float: left; margin: 1px; padding: 1px;" value="'+rowdata['lab']+'"/>');
				var lecLabel = $('<label style="float: left; width: 35%;">Lec : </label>');
				var lecInput = $('<input id="lecInput" name="lecInput" type="text" style="float: left; margin: 1px; padding: 1px;" value="'+rowdata['lec']+'"/>');
														
				windowContent.append(sccodeLabel);
				windowContent.append(sccodeInput);
				windowContent.append(subjecttitleLabel);
				windowContent.append(subjecttitleInput);
				windowContent.append(unitsLabel);
				windowContent.append(unitsInput);
				windowContent.append(labLabel);
				windowContent.append(labInput);
				windowContent.append(lecLabel);
				windowContent.append(lecInput);
				windowButton.append(cancelButton);
				windowButton.append(okButton);
				windowContent.append(windowButton);
				container.append(windowContent);	

				sccodeInput.jqxInput({ width: 280, height: 25, theme: theme});
				subjecttitleInput.jqxInput({ width: 280, height: 25, theme: theme});
				unitsInput.jqxInput({ width: 280, height: 25, theme: theme});
				labInput.jqxInput({ width: 280, height: 25, theme: theme});
				lecInput.jqxInput({ width: 280, height: 25, theme: theme});
				
				okButton.jqxButton({ theme: theme, width: 50, height: 22 });
				cancelButton.jqxButton({ theme: theme, width: 65, height: 22 });

				okButton.bind('click', function(event) {
					var postData = new Array();
					postData.push($('#sccodeInput').val());
					postData.push($('#subjecttitleInput').val());
					postData.push($('#unitsInput').val());
					postData.push($('#labInput').val());
					postData.push($('#lecInput').val());
					var url = '<?php echo base_url().''.$manager; ?>/subjectManagementUpdate/';
					$.post(url, { postID : rowdata['subjectID'], postData: postData }, 
						function(data) {
							container.jqxWindow('close');
							if(data == 'error'){
								var divNotification = $('<div id="divNotification">All fields are required. </div>');
								divNotification.jqxNotification({
									width: 300, 
									position: 'top-right', 
									opacity: 1.28,
									autoOpen: false, 
									animationOpenDelay: 1000, 
									autoClose: true, 
									autoCloseDelay: 6000, 
									template: 'warning'
								});
								divNotification.jqxNotification('open');		
							}else{
								var divNotification = $('<div id="divNotification">Successfully updated subject.</div>');
								divNotification.jqxNotification({
									width: 300, 
									position: 'top-right', 
									opacity: 1.28,
									autoOpen: false, 
									animationOpenDelay: 1000, 
									autoClose: true, 
									autoCloseDelay: 6000, 
									template: 'success'
								});
								divNotification.jqxNotification('open');		
							}
							$('#jqxgrid').jqxGrid('updatebounddata');
						}
					);
				});
				cancelButton.click(function(event) {
					container.jqxWindow('close');
				});
				container.jqxWindow({                    
					title: 'Subject Management Update', 
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
				var dataContent = $('<p class="confirmationMessage">Do you really want to delete?<br/> ['+rowdata['sc_code']+' '+rowdata['subject_title']+']</p>');
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
					var url = '<?php echo base_url().''.$manager; ?>/subjectManagementDelete/';
					$.post(url, { postID : rowdata['subjectID'] }, 
						function(data) {
							container.jqxWindow('close');
								var divNotification = $('<div id="divNotification">Successfully deleted subject.</div>');
								divNotification.jqxNotification({
									width: 300, 
									position: 'top-right', 
									opacity: 1.28,
									autoOpen: false, 
									animationOpenDelay: 1000, 
									autoClose: true, 
									autoCloseDelay: 6000, 
									template: 'success'
								});
								divNotification.jqxNotification('open');
							$('#jqxgrid').jqxGrid('updatebounddata');
						}
					);
				});
				cancelButton.click(function(event) {
					container.jqxWindow('close');
				});
				container.jqxWindow({
					title: 'Subject Management Delete',
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
			{ text: 'SC_CODE', datafield: 'sc_code', editable: false },
			{ text: 'Subject Title', datafield: 'subject_title', editable: false },
			{ text: 'Units', datafield: 'units', editable: false },
			{ text: 'Lab', datafield: 'lab', editable: false },
			{ text: 'Lec', datafield: 'lec', editable: false }
        ]
    });

    $('#jqxgrid').jqxGrid('autoresizecolumn', 'sc_code');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'subject_title');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'units');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'lab');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'lec');
});
</script>
