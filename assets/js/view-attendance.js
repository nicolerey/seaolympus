function calculate_time_difference()
{
    var time_in = [];
    $('.time_in').each(function(){
        time_in.push(new Date($(this).html().trim()));
    });
    var time_out = [];
    $('.time_out').each(function(){
        time_out.push(new Date($(this).html().trim()));
    });

    var count = 0;
    $('.time_diff').each(function(){
        //var time_diff = ( new Date("1970-1-1 " + (time_out[count]).trim()) - new Date("1970-1-1 " + (time_in[count]).trim()) ) / 1000 / 60 / 60;
        count += 1;
        console.log(time_out[count]+" "+time_in[count]);
        console.log(new Date(time_out[count] - time_in[count]));
    });
}

$(document).ready(function(){
	$.fn.editable.defaults.mode = 'popup';
	
	$('.editable_time').editable({
		format: 'YYYY-MM-DD hh:mm A',   
        template: 'YYYY - MM - DD hh : mm  A',
        success: function(response, newValue){
            var json_response = jQuery.parseJSON(response)
        	if(json_response.status == 'error') return json_response.msg;
            //else calculate_time_difference();
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
