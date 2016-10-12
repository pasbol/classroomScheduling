<script type="text/javascript">
$(document).ready(function(){
	var theme = 'classic';
	var url = '<?php echo base_url().''.$manager; ?>/accountSettingsManagementJSON/';
    var source =
    {
        datatype: 'json',
        datafields: [
            { name: 'personID', type: 'int' },
            { name: 'username', type: 'string' },
            { name: 'idnumber', type: 'string' },
            { name: 'lastname', type: 'string' },
            { name: 'firstname', type: 'string' },
            { name: 'middlename', type: 'string' },
            { name: 'extname', type: 'string' },
            { name: 'department_code', type: 'string' },
        ],
        id: 'personID',
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
            var resetButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/reset.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Reset</span></div>");
            var reloadButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/reload.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Reload</span></div>");
            container.append(resetButton);
            container.append(reloadButton);
            statusbar.append(container);
            resetButton.jqxButton({width: 85, height: 15});
            reloadButton.jqxButton({width: 80, height: 15});

            // reset selected row.
            resetButton.click(function (event) {
            	$('#jqxwindow').remove(); 
            	var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            	var rowdata = $('#jqxgrid').jqxGrid('getrowdata', rowindex);       

				var container = $("<div id='jqxwindow'></div>");
				var windowContent = $("<div></div>");
				var windowButton = $("<div></div>");
				var okButton = $("<input type='button' style='margin-top: 15px; float: right;' value='OK' id='okButton' />");
				var cancelButton = $("<input type='button' style='margin-top: 15px; margin-left: 5px; float: right;' value='Cancel' id='okButton' />");
				var resetID = $('<input id="resetID" type="hidden" value='+rowdata['personID']+' />');
				var resetNumber = $('<input id="resetNumber" type="hidden" value='+rowdata['idnumber']+' />');
				var resetContent = $('<div>Do you really want to reset this account? '+rowdata['idnumber']+' | '+rowdata['lastname']+','+rowdata['firstname']+' '+rowdata['middlename']+'</div>');

				windowContent.append(resetID);
				windowContent.append(resetNumber);	
				windowContent.append(resetContent);									
				windowButton.append(cancelButton);
				windowButton.append(okButton);
				windowContent.append(windowButton);
				container.append(windowContent);	
				
				okButton.jqxButton({ theme: theme, width: 50, height: 22 });
				cancelButton.jqxButton({ theme: theme, width: 65, height: 22 });

				okButton.bind('click', function(event) {
					var postData = new Array();
					postData.push($('#resetID').val());
					postData.push($('#resetNumber').val());

					var url = '<?php echo base_url().''.$manager; ?>/accountSettingsManagementReset/';
					$.post(url, { postData: postData }, 
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
					title: 'Reset Password', 
					resizable: false, 
					theme: theme, 
					width: 450,                         
					isModal: true,                        
					draggable: false                        
				}); 			
            });
            
            // reload jqxgrid.
            reloadButton.click(function (event) {
            	 $('#jqxgrid').jqxGrid({ source: dataAdapter });
            });
        },
        columns: [
        	{ text: 'ID NO', datafield: 'idnumber'},
          	{ text: 'Lastname', datafield: 'lastname'},
          	{ text: 'Firstname', datafield: 'firstname'},
          	{ text: 'Middlename', datafield: 'middlename'},
          	{ text: 'Username', datafield: 'username'},
          	{ text: 'Department', datafield: 'department_code', filtertype: 'list'},
        ]
    });
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'idnumber'); 
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'lastname');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'firstname');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'middlename');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'extname');
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'username');
});
</script>
