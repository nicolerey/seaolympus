$(document).ready(function(){
	$.fn.editable.defaults.mode = 'popup';
	
	$('.editable_time').editable({
		format: 'YYYY-MM-DD hh:mm A',   
        template: 'YYYY - MM - DD hh : mm  A',
        success: function(response, newValue){
            var json_response = jQuery.parseJSON(response)
        	if(json_response.status == 'error') return json_response.msg;
        },
        combodate: {
                minuteStep: 1,
                minYear: 2000,
				maxYear: 3000,
				weekStart: 0
           }
    });
	
	$('.datepicker').datepicker();
});
