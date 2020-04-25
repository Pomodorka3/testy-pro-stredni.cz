$(document).ready(function(){

      column_name = 'be.buy_time';
      //If written ASC - .php is showing DESC.
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
          url:"ajax/ajaxBoughtItems.php",
          method:"POST",
          data:{itemName:itemName, itemDescription:itemDescription, itemSubject:itemSubject, teacher:teacher, column_name:column_name, order:order, itemType:itemType, schoolClass:schoolClass, page:page, itemAnswers:itemAnswers},
          success:function(data)
          {
            $('#bought_items').html(data);
            $('[data-toggle="tooltip"]').tooltip();
            /*$('#'+column_name+'').append(arrow);*/
          }
        });
      }

      function filter_data_hidden(page, column_name, order){
        var itemName = $('#search_name_hidden').val();
        var itemDescription = $('#search_description_hidden').val();
        var itemSubject = $('#item_subject_hidden').val();
        var teacher = $('#teacher_hidden').val();
        var itemType = $('.itemType_hidden:checked').val();
        var itemAnswers = $('.itemAnswers_hidden:checked').val();
        var schoolClass = $('.schoolClass_hidden:checked').val();
        $.ajax({
          url:"ajax/ajaxBoughtItems.php",
          method:"POST",
          data:{itemName:itemName, itemDescription:itemDescription, itemSubject:itemSubject, teacher:teacher, column_name:column_name, order:order, itemType:itemType, schoolClass:schoolClass, page:page, itemAnswers:itemAnswers},
          success:function(data)
          {
            $('#bought_items').html(data);
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
      $('.filter_field_hidden').keyup(function(){
        filter_data_hidden();
      });

      $('.itemType_hidden').click(function(){
        filter_data_hidden();
        column_name = 'be.buy_time';
        order = 'ASC';
        page = '1';
      });

      $('.itemAnswers_hidden').click(function(){
        filter_data_hidden();
        column_name = 'be.buy_time';
        order = 'ASC';
        page = '1';
      });

      $('.schoolClass_hidden').click(function(){
        filter_data_hidden();
        column_name = 'be.buy_time';
        order = 'ASC';
        page = '1';
      });

      $('.filter_field').keyup(function(){
        filter_data();
      });

      $('.itemType').click(function(){
        filter_data();
        column_name = 'be.buy_time';
        order = 'ASC';
        page = '1';
      });

      $('.itemAnswers').click(function(){
        filter_data();
        column_name = 'be.buy_time';
        order = 'ASC';
        page = '1';
      });

      $('.schoolClass').click(function(){
        filter_data();
        column_name = 'be.buy_time';
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

      $(document).on('click', '#filter-toggle', function(){
        $("#filter").slideToggle("slow");
        $("#instagram-arrow-down").toggle();
        $("#instagram-arrow-up").toggle();
        /* $("#facebook-arrow-down").show();
        $("#facebook-arrow-up").hide(); */
      });
    });