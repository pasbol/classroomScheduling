<script type="text/javascript">
	$(document).ready(function(){

		$('a.toggle-vis').css({
			'cursor' : 'pointer'
		});

	    var table = $('#subjectsOfferedSummaryTable').DataTable({
		    dom: 'Bfrtip',
		    buttons: [
		        'excel', 'pdf', 'print'
		    ]
	    });
	 
	    $('a.toggle-vis').on( 'click', function (e) {
	        e.preventDefault();
	        var column = table.column($(this).attr('data-column'));
	        column.visible(!column.visible());

	      	if(!column.visible()){
	      		$(this).css({'color':'#800000'});
	      	}else{
	      		$(this).css({'color':'#337ab7'});
	      	}

	    });
	});
</script>