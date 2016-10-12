<script type="text/javascript">
	$(document).ready(function(){
		
		var theme = 'classic';
		$('table').css({
			'width': '100%'
		});
		$('table thead').css({
			'background-color': '#800000',
			'border-top': 'solid 2px #191919',
			'border-bottom': 'solid 1px #191919'
		});
		$('table thead tr th').css({
			'padding': '8px',
			'color': '#ffffff'
		});
		$('table tbody').css({
			'background-color': '#fffcf7'
		});
		$('table tbody tr').css({
			'border-bottom': 'solid 1px #cdcccc'
		});
		$('table tbody tr td').css({
			'padding': '2px'
		});	
		$('table tfoot').css({
			'background-color': '#800000',
			'border-bottom': 'solid 2px #191919',
			'border-top' : 'solid 1px #191919'
		});
		$('table tfoot tr th').css({
			'padding': '8px',
			'color': '#ffffff'
		});

		$('#searchButton').jqxButton({theme: theme});
		//$('#printButton').jqxButton({theme: theme});
		
		$('#searchButton').bind('click',function(){
			var roomID = $('#selectedRoom').val();
			var dayID = $('#selectedDay').val();
			var url = '<?php echo base_url().''.$manager; ?>/classroomScheduleResults/'+roomID+'/'+dayID;
			window.location.replace(url);
		});

		/*$('#printButton').bind('click',function(){
			window.print('#printBody');
		});*/

	});
</script>