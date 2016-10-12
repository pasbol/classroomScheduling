<script type="text/javascript">
$(document).ready(function(){

	var theme = 'classic';
	var url = '<?php echo base_url().''.$manager; ?>/subjectCurriculumManagementJSON/';

    var source =
    {
        datatype: 'json',
        datafields: [
            { name: 'curriculumID', type: 'int' },
            { name: 'curriculumTitle', type: 'string' },
            { name: 'academicYear', type: 'string' }
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
        width: '85%',
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
            var addButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/insert.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Add Subject</span></div>");
            var viewButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/show.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>View Subject</span></div>");
            var reloadButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: -3px;' src='<?php echo base_url(); ?>images/reload.png'/><span style='margin-left: 4px; position: relative; top: -1px;'>Reload</span></div>");
            container.append(addButton);
            container.append(viewButton);
            container.append(reloadButton);
            statusbar.append(container);
            addButton.jqxButton({width: 120, height: 15, theme: theme});
            viewButton.jqxButton({width: 120, height: 15, theme: theme});
            reloadButton.jqxButton({width: 80, height: 15, theme: theme});
			
			addButton.click(function(event){
				var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            	var rowdata = $('#jqxgrid').jqxGrid('getrowdata', rowindex);
				var curriculumID = rowdata['curriculumID'];
				var url = '<?php echo base_url().''.$manager; ?>/subjectCurriculumManagementAddSubject/'+curriculumID;
                window.location.replace(url);
			});
			
			viewButton.click(function(event){
				var rowindex = $('#jqxgrid').jqxGrid('getselectedrowindex');
            	var rowdata = $('#jqxgrid').jqxGrid('getrowdata', rowindex);
				var curriculumID = rowdata['curriculumID'];
				var url = '<?php echo base_url().''.$manager; ?>/subjectCurriculumManagementViewSubject/'+curriculumID;
                window.location.replace(url);
			});

            reloadButton.click(function(event) {
            	$('#jqxgrid').jqxGrid({ source: dataAdapter });
				
            });
        },
        columns: [            
            { text: 'Curriculum Title', datafield: 'curriculumTitle'},
            { text: 'Academic Year', datafield: 'academicYear', filtertype: 'list'} 
        ]
    });
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'curriculumTitle'); 
    $('#jqxgrid').jqxGrid('autoresizecolumn', 'academicYear');
});
</script>
