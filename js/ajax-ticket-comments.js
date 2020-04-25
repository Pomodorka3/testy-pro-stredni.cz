$(document).ready(function(){

    	page = '1';

		url_string = window.location.href;
		url = new URL(url_string);
		ticketId = url.searchParams.get("ticket_id");
		
  		filter_data(1, ticketId);

  		function filter_data(page, ticketId){
  			$.ajax({
  				url:"ajax/ajaxTicketComments.php",
  				method:"POST",
  				data:{page:page, ticketId:ticketId},
  				success:function(data)
  				{
					$('#comments_list').html(data);
					$('[data-toggle="tooltip"]').tooltip();
  				}
  			});
		  }

	    $(document).on('click', '.page', function(){
	    	page = $(this).attr("id");
	    	filter_data(page, ticketId);
	    	//$('#test').append(page);
	    });
	});