$(document).ready(function(){

      column_name = 'confirmed_date';
      order = 'ASC';
      page = '1';

      filter_data();

      function filter_data(page, column_name, order){
        var itemName = $('#search_name').val();
        var itemDescription = $('#search_description').val();
        var itemSubject = $('#item_subject').val();
        var teacher = $('#teacher').val();
        var itemType = $('.itemType:checked').val();
        var itemAnswers = $('.itemAnswers:checked').val();
        var schoolClass = $('.schoolClass:checked').val();
        $.ajax({
          url:"ajax/ajaxSellingItems.php",
          method:"POST",
          data:{itemName:itemName, itemDescription:itemDescription, itemSubject:itemSubject, teacher:teacher, column_name:column_name, order:order, itemType:itemType, schoolClass:schoolClass, page:page, itemAnswers:itemAnswers},
          success:function(data)
          {
            $('#selling_items').html(data);
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

      $('.filter_field').keyup(function(){
        filter_data();
      });

      $('.itemType').click(function(){
        filter_data();
        column_name = 'confirmed_date';
        order = 'ASC';
        page = '1';
      });

      $('.itemAnswers').click(function(){
        filter_data();
        column_name = 'confirmed_date';
        order = 'ASC';
        page = '1';
      });

      $('.schoolClass').click(function(){
        filter_data();
        column_name = 'confirmed_date';
        order = 'ASC';
        page = '1';
      });

      $(document).on('click', '.column_sort', function(){
        column_name = $(this).attr("id");
        order = $(this).data("order");
        filter_data(page, column_name, order);
        //$('#test').append('Page: '+page+'; ');
        //$('#test').append('Column_name: '+column_name+'; ');
        //$('#test').append('Order: '+order+'; ');
      });

      $(document).on('click', '.page', function(){
        page = $(this).attr("id");
        filter_data(page, column_name, order);
        //$('#test').append(page);
      });

    });