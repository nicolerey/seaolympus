$(document).ready(function(){

	var lockUrl = $('#lockUrl').data('value');

	$('.lock').change(function(){
		var id = $(this).closest('tr').data('pk');
		$.post(lockUrl, {id:id})
		.done(function(response){
			
		})
		.fail(function(){
			alert('An internal server error has occured. Please try again');
		});
	});
})