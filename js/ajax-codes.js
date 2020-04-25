$(document).ready(function(){

      column_name = 'c.code_id';
      order = 'DESC';
      page = '1';

      filter_data();

      function filter_data(page, column_name, order){
        var searchCode = $('#search_code').val();
        var searchCreatedby = $('#search_createdby').val();
        var searchActivatedBy = $('#search_activatedby').val();
        var codeType = $('#code_type').val();
        var activated = $('#activated');
        if (activated.prop('checked')) {
          activated = 1;
        } else {
          activated = 0;
        }
        $.ajax({
          url:"ajax/ajaxCodes.php",
          method:"POST",
          data:{searchCode:searchCode, searchCreatedby:searchCreatedby, searchActivatedBy:searchActivatedBy, codeType:codeType, column_name:column_name, order:order, page:page, activated:activated},
          success:function(data)
          {
            $('#shop_table').html(data);
            $('[data-toggle="tooltip"]').tooltip();
            /*$('#'+column_name+'').append(arrow);*/
          }
        });
      }

      function get_filter(class_name){
        var filter = [];
        $('.'+class_name+':checked').each(function(){
          filter.push($(this).val());
        });
        return filter;
      }

      $('.filter_field').keyup(function(){
        filter_data();
      });

      $('#activated').click(function(){
        filter_data();
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