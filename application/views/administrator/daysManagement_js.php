<script type="text/javascript">
$(document).ready(function(){

	var theme = 'classic';
	var url = '<?php echo base_url().''.$manager; ?>/daysManagementJSON/';

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
			var deleteButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/delete.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Delete</span></div>");
            var reloadButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/reload.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Reload</span></div>");
            container.append(addButton);
            container.append(deleteButton);
            container.append(reloadButton);
            statusbar.append(container);
            addButton.jqxButton({width: 85, height: 15});
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
				var daysLabel = $('<label style="float: left; width: 35%;">Day(s) : </label>');
				var daysInput = $('<input id="daysInput" name="daysInput" type="text" style="float: left; margin: 1px; padding: 1px;"/>');

				windowContent.append(daysLabel);
				windowContent.append(daysInput);
				windowButton.append(cancelButton);
				windowButton.append(okButton);
				windowContent.append(windowButton);
				container.append(windowContent);

				daysInput.jqxInput({ width: 280, height: 25, theme: theme});
				
				okButton.jqxButton({width: 50, height: 22, theme: theme});
				cancelButton.jqxButton({width: 65, height: 22, theme: theme});
				
				okButton.bind('click', function(event) {
					var postData = new Array();
					postData.push($('#daysInput').val());
					var url = '<?php echo base_url() .''.$manager; ?>/insertDaysManagement/';
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
					title: 'Insert Day(s)', 
					resizable: false, 
					//headerHeight: 30,
					theme: theme, 
					width: 450,                        
					isModal: true,
					position: 'center',
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
				var dataContent = $('<p>Do you really want to delete? ['+rowdata['day']+']</p>');
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
					var postID = rowdata['dayID'];
					var url = '<?php echo base_url().''.$manager; ?>/deleteDaysManagement/';
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
					title: 'Delete Day(s)',
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
          //{ text: 'Day ID', datafield: 'dayID', filterable: false, width: 100},
          { text: 'Day', datafield: 'day'}
        ]
    });
   	$('#jqxgrid').jqxGrid('autoresizecolumn', 'day');
    
});
</script>

