$(document).ready(function(){

      column_name = 'sch.school_id';
      order = 'DESC';
      page = '1';

  		filter_data();

  		function filter_data(page, column_name, order){
  			var schoolName = $('#schoolName').val();
        var cityName = $('#cityName').val();
        var districtName = $('#districtName').val();
  			$.ajax({
  				url:"ajax/ajaxSchoolsearch.php",
  				method:"POST",
  				data:{schoolName:schoolName, cityName:cityName, districtName:districtName, page:page, column_name:column_name, order:order},
  				success:function(data)
  				{
            $('#user_table').html(data);
            $('[data-toggle="tooltip"]').tooltip();
  				}
  			});
  		}

  		$('.schoolName').keyup(function(){
  			filter_data();
  		});

      $('.cityName').keyup(function(){
        filter_data();
      });

      $('.districtName').keyup(function(){
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