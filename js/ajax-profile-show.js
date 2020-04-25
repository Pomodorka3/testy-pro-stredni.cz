$(document).ready(function(){

      column_name = 'confirmed_date';
      order = 'ASC';
      page = '1';

      url_string = window.location.href;
      url = new URL(url_string);
      profileId = url.searchParams.get("profile_id");

      filter_data();

      function filter_data(page, column_name, order){
        $.ajax({
          url:"ajax/ajaxProfileShow.php",
          method:"POST",
          data:{column_name:column_name, order:order, page:page, profileId:profileId},
          success:function(data)
          {
            $('#user-items').html(data);
            $('[data-toggle="tooltip"]').tooltip();
            /*$('#'+column_name+'').append(arrow);*/
          }
        });
      }

      /*function get_filter(class_name){
        var filter = [];
        $('.'+class_name+':checked').each(function(){
          filter.push($(this).val());
        });
        return filter;
      }*/

      $(document).on('click', '.column_sort', function(){
        column_name = $(this).attr("id");
        order = $(this).data("order");
        filter_data(page, column_name, order);
        //$('#test').append('Page: '+page+'; ');
       // $('#test').append('Column_name: '+column_name+'; ');
       // $('#test').append('Order: '+order+'; ');
      });

      $(document).on('click', '.page', function(){
        page = $(this).attr("id");
        filter_data(page, column_name, order);
        //$('#test').append(page);
      });

    });