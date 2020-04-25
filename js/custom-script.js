//custom-script.js is included on all pages

//---------------------------------------------

// Tooltips Initialization
$(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip();
})

$(document).ready(function(){
	function user_search(){
  		var username = $('#username_message_to').val();
  		$.ajax({
  			url:"ajax/ajaxUsersearch_message.php",
  			method:"POST",
  			data:{username: username},
  			success:function(data)
  			{
  				$('#users').html(data);
  			}
  		});
  	}

  	$('#username_message_to').keyup(function(){
  		user_search();
	  });
	
	//------------------------ LOTTERY USERS -------------------------

	$(document).ready(function(){
		lottery_column = 'l.lottery_date';
    	lottery_order = 'ASC';
    	page = '1';

    	filter_data();

    	function filter_data(lottery_column, lottery_order){
			var lottery_username = $('#lottery_username').val();
			$.ajax({
			url:"ajax/ajaxLottery.php",
			method:"POST",
			data:{lottery_column:lottery_column, lottery_order:lottery_order, lottery_username:lottery_username},
			success:function(data)
				{
					$('#lotteryUsers').html(data);
					$('[data-toggle="tooltip"]').tooltip();
					/*$('#'+column_name+'').append(arrow);*/
				}
			});
		}
		
		$('#lottery_username').keyup(function(){
			filter_data();
		});

    	$(document).on('click', '.lottery_sort', function(){
    	  lottery_column = $(this).attr("id");
    	  lottery_order = $(this).data("order");
    	  filter_data(lottery_column, lottery_order);
    	});

	});

	//------------------------ FAQ -------------------------

	$('.accordion').accordion({
	  highlander: false,
	  collapsible: true,
	  collapseIcons: {
	    opened: '–',
	    closed: '+'
	  },
	  prefix: '●'
	});

	$("#faq_add_button").click(function(){
		$("#faq_add_form").slideToggle("slow");
		$("html, body").animate({ scrollTop: $("#faq_add_form").offset().top }, 1000);
	});

	//------------------------ Tickets -------------------------

	$("#ticket_add_button").click(function(){
		$("#ticket_add_form").slideToggle("slow");
		$("html, body").animate({ scrollTop: $("#ticket_add_form").offset().top }, 1000);
	});
	

	
	

	










});

// Deposit modal window
/* $('#deposit-sum').keyup(function(){
	//alert('asdasd');
	$("#deposit-to-pay").slideDown("slow");
	if ($('#deposit-sum').val() == '') {
		var totalSum = 0;
	} else {
		var depositSum = parseInt($('#deposit-sum').val());
		var totalSum = depositSum + 3 +(0.0033*depositSum);
		var totalSumRounded = totalSum.toFixed(2);
	}
	$('#deposit-to-pay-count').text(totalSumRounded);
}); */



//signup.php and signin.php cookie agreement files
function cookies_agree(){
	var now = new Date();
	var time = now.getTime();
	time += 1000 * 3600 * 2;
	now.setTime(time);
  	document.cookie = "cookies_accepted=1; expires="+now.toUTCString();
}