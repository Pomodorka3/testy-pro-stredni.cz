$(document).ready(function(){

      column_name = 'w.withdraw_status';
      order = 'DESC';
      page = '1';

      filter_data();

      function filter_data(page, column_name, order){
        var username = $('#username').val();
        var withdrawSum = $('#withdrawSum').val();
        var bankAccount = $('#bankAccount').val();
        var statusFilter = $('.statusFilter:checked').val();
        $.ajax({
          url:"ajax/ajaxWithdrawRequests.php",
          method:"POST",
          data:{statusFilter:statusFilter , username:username, withdrawSum:withdrawSum, bankAccount:bankAccount, statusFilter:statusFilter, column_name:column_name, order:order, page:page},
          success:function(data)
          {
            $('#withdraw_requests').html(data);
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

      $('.statusFilter').click(function(){
        filter_data();
        column_name = 'w.withdraw_date';
        order = 'ASC';
        page = '1';
      });

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