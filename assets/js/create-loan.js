function generate_payment_terms(element){
	var payment_count = $('.payment_count').val();
	var payment_amount = ($('.loan_amount').val())/payment_count;

	//alert(payment_amount);

	$('.payment_terms').remove();

	for(var x=0; x<payment_count; x++){
		var payment_term_fields = $('.payment_terms_fields').first().clone().removeClass('hidden').addClass('payment_terms');
		payment_term_fields.find('.loan_date_field').attr('name', 'payment_date[]').datepicker();
		payment_term_fields.find('.loan_amount_field').attr('name', 'payment_amount[]').val(payment_amount.toFixed(2)).priceFormat({prefix:''});

		$('.payment_terms_tbody').append(payment_term_fields);
	}
}

$(document).ready(function(){	
	$('.datepicker').datepicker();

	$('.pformat').priceFormat({prefix:''});

	$('form').submit(function(e){
		e.preventDefault();

		var that = $(this),
			submitBtn = that.find('[type=submit]'),
			msgBox = $('.alert-danger');

		submitBtn.attr('disabled', 'disabled');
		msgBox.addClass('hidden');

		$.post(that.data('action'), that.serialize())
		.done(function(response){
			if(response.result){
				window.location.href = $('.cancel').attr('href');
				return;
			}
			msgBox.removeClass('hidden').find('ul').html('<li>'+response.messages.join('</li><li>')+'</li>');
			$('html, body').animate({scrollTop: 0}, 'slow');
		})
		.fail(function(){
			alert('An internal server error has occured');
		}).always(function(){
			submitBtn.removeAttr('disabled');
		});
	});
});
