$(document).ready(function(){

  		column_name = 'default';
      	order = 'ASC';
    	page = '1';

  		filter_data();

  		function filter_data(page, column_name, order){
  			var search = $('#search').val();
  			var ticketType = $('.ticketType:checked').val();
  			$.ajax({
  				url:"ajax/ajaxTickets.php",
  				method:"POST",
  				data:{search:search, page:page, ticketType:ticketType, column_name:column_name, order:order},
  				success:function(data)
  				{
					$('#tickets_list').html(data);
					$('[data-toggle="tooltip"]').tooltip();
  				}
  			});
  		}

	  	$('#search').keyup(function(){
	  		filter_data();
		  });
		  
		$('.ticketType').click(function(){
			filter_data();
			column_name = 'default';
			order = 'ASC';
			page = '1';
		});

	  	$(document).on('click', '.column_sort', function(){
	        column_name = $(this).attr("id");
	        order = $(this).data("order");
	        filter_data(page, column_name, order);
	    });

	    $(document).on('click', '.page', function(){
	    	page = $(this).attr("id");
	    	filter_data(page, column_name, order);
	    	//$('#test').append(page, column_name, order);
	    });
	});