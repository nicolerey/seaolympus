(function($){
	$(document).ready(function(){	

		var messageBox = $('.alert.alert-danger');

		$('form').submit(function(e){
			e.preventDefault();
			var that = $(this);
			messageBox.addClass('hidden');
			$('[type=submit]').attr('disabled', 'disabled');
			$.post(that.data('action'), that.serialize())
			.done(function(response){
				if(response.result){
					window.location.href = $('#cancel').attr('href');
				}
				messageBox.removeClass('hidden').find('ul').html('<li>'+response.errors.join('</li><li>')+'</li>');
				$('html, body').animate({scrollTop: 0}, 'slow');
			})
			.fail(function(){
				alert('An internal error has occured. Please try again in a few moment.');
			})
			.always(function(){
				$('[type=submit]').removeAttr('disabled');
			});
		});

	})
})(jQuery)