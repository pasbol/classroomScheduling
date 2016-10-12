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
 			var viewButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/show.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Vacant Schedule</span></div>");
			container.append(reloadButton);
			container.append(viewButton);
            statusbar.append(container);
            viewButton.jqxButton({width: 145, height: 15, theme: theme});
            reloadButton.jqxButton({width: 80, height: 15, theme: theme});

			// view vacant schedule
			viewButton.click(function(event){
				var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            	var rowdata = $('#jqxgrid').jqxGrid('getrowdata', rowindex); 
				var roomID = rowdata['roomID'];
				
				var url = '<?php echo base_url().''.$manager; ?>/classroomManagementVacantScheduleView/'+roomID;
				window.location.replace(url);
				
			});		 
            // reload jqxgrid.
            reloadButton.click(function(event) {
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













