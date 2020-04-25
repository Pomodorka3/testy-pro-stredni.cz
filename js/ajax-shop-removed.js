$(document).ready(function(){

      column_name = 'srl.removed_time';
      //Will be shown DESC, because of .php script
      order = 'ASC';
      page = '1';

  		filter_data();

  		function filter_data(page, column_name, order){
  		  var schoolName = $('#school_name').val();
        var removedBy = $('#removed_by').val();
        var itemName = $('#item_name').val();
        var itemCreatedby = $('#item_createdby').val();
  			$.ajax({
  				url:"ajax/ajaxShopRemoved.php",
  				method:"POST",
  				data:{schoolName:schoolName, removedBy:removedBy, itemName:itemName, itemCreatedby:itemCreatedby, column_name:column_name, order:order, page:page},
  				success:function(data)
  				{
            $('#shop_removed').html(data);
            $('[data-toggle="tooltip"]').tooltip();
  				}
  			});
  		}

  	$('.item_name').keyup(function(){
  		filter_data();
  	});

    $('.removed_by').keyup(function(){
      filter_data();
    });

    $('.school_name').keyup(function(){
      filter_data();
    });
    
    $('.item_createdby').keyup(function(){
      filter_data();
    });

    $(document).on('click', '.column_sort', function(){
      column_name = $(this).attr("id");
      order = $(this).data("order");
      filter_data(page, column_name, order);
    });

    $(document).on('click', '.page', function(){
      page = $(this).attr("id");
      filter_data(page, column_name, order);
      //$('#test').append(page);
    });
});