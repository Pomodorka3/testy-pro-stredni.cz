$(document).ready(function(){

      log = 'bannedUsers';
      column_name = 'bu.ban_time';
      order = 'ASC';
      page = '1';

      show_data('bannedUsers');

      function show_data(log, page, column_name, order){
        var bannedUser = $('#banned_id').val();
        var bannedBy = $('#banned_by').val();
        var ban_active = $('#ban_active');
        if (ban_active.prop('checked')) {
          ban_active = 1;
        } else {
          ban_active = 0;
        }
        var unbannedUser = $('#unbanned_id').val();
        var unbannedBy = $('#unbanned_by').val();
        var username = $('#username').val();
        var setby_username = $('#setby_username').val();
        var invited_username = $('#invited_username').val();
        var invitedby_username = $('#invitedby_username').val();
        var buyer_username = $('#buyer_username').val();
        var seller_username = $('#seller_username').val();
        var boughtItem = $('#boughtItem');
        if (boughtItem.prop('checked')) {
          boughtItem = 1;
        } else {
          boughtItem = 0;
        }
        var transaction_username = $('#transaction_username').val();
        var transaction_description = $('.transaction_desc:checked').val();
        var depositStatus = $('.depositStatus:checked').val();
        var depositBy = $('#depositBy').val();
        var bp_username = $('#bp_username').val();
        var bp_givenby = $('#bp_givenby').val();
        var bp_active = $('#bp_active');
        if (bp_active.prop('checked')) {
          bp_active = 1;
        } else {
          bp_active = 0;
        }
        $.ajax({
          url:"ajax/ajaxLog.php",
          method:"POST",
          data:{
            log:log,
            page:page,
            column_name:column_name,
            order:order,
            bannedUser:bannedUser,
            bannedBy:bannedBy,
            ban_active:ban_active,
            unbannedUser:unbannedUser,
            unbannedBy:unbannedBy,
            username:username,
            setby_username:setby_username,
            invited_username:invited_username,
            invitedby_username:invitedby_username,
            boughtItem:boughtItem,
            buyer_username:buyer_username,
            seller_username:seller_username,
            transaction_username:transaction_username,
            transaction_description:transaction_description,
            depositStatus:depositStatus,
            depositBy:depositBy,
            bp_givenby:bp_givenby,
            bp_username:bp_username,
            bp_active:bp_active},
          success:function(data)
          {
            $('#log-container').html(data);
            $('[data-toggle="tooltip"]').tooltip();
          }
        });
      }

      $(document).on('click', '.search', function(){
        show_data(log, page, column_name, order);
      });

      $(document).on('click', '#ban_active', function(){
        show_data(log, page, column_name, order);
      });

      $(document).on('click', '#boughtItem', function(){
        show_data(log, page, column_name, order);
      });

      $(document).on('click', '#bp_active', function(){
        show_data(log, page, column_name, order);
      });

      $(document).on('click', '#bannedUsers', function(){
        log = 'bannedUsers';
        column_name = 'bu.ban_time';
        order = 'ASC';
        show_data('bannedUsers');
      });

      $(document).on('click', '#unbannedUsers', function(){
        log = 'unbannedUsers';
        column_name = 'uu.unban_time';
        order = 'ASC';
        show_data('unbannedUsers');
      });

      $(document).on('click', '#deposit', function(){
        log = 'deposit';
        column_name = 'd.time_requested';
        order = 'ASC';
        show_data('deposit');
      });

      $(document).on('click', '#groups', function(){
        log = 'groups';
        column_name = 'ug.event_date';
        order = 'ASC';
        show_data('groups');
      });

      $(document).on('click', '#referral', function(){
        log = 'referral';
        column_name = 'u1.register_date';
        order = 'ASC';
        show_data('referral');
      });

      $(document).on('click', '#shop', function(){
        log = 'shop';
        column_name = 'be.buy_time';
        order = 'ASC';
        show_data('shop');
      });

      $(document).on('click', '#transactions', function(){
        log = 'transactions';
        column_name = 't.t_date';
        order = 'ASC';
        show_data('transactions');
      });

      $(document).on('click', '#blackPoints', function(){
        log = 'blackPoints';
        column_name = 'bp.bp_date';
        order = 'ASC';
        show_data('blackPoints');
      });

      $(document).on('click', '.transaction_desc', function(){
        log = 'transactions';
        column_name = 't.t_date';
        order = 'ASC';
        show_data('transactions');
      });

      $(document).on('click', '.depositStatus', function(){
        log = 'deposit';
        column_name = 'd.time_requested';
        order = 'ASC';
        show_data('deposit');
      });

      $(document).on('click', '.column_sort', function(){
        column_name = $(this).attr("id");
        order = $(this).data("order");
        show_data(log, page, column_name, order);
        //$('#test').append('Page: '+page+'; ');
        //$('#test').append('Column_name: '+column_name+'; ');
        //$('#test').append('Order: '+order+'; ');
      });

      $(document).on('click', '.page', function(){
        page = $(this).attr("id");
        show_data(log, page, column_name, order);
        //$('#test').append(page);
      });

    });