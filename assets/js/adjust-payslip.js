$(document).ready(function(){
	$('form').submit(function(e){
		e.preventDefault();
		var that = $(this);
		
		$.post(that.data('action'), that.serialize())
		.done(function(response){
			if(response.result){
				window.location.reload();
				return;
			}
			alert('Failed to perform action due to an unknown error. Please try again later.');
		})
		.fail(function(){
			alert('An internal error has occured. Please try again.');
		});
	})
})