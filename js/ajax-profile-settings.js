$(document).ready(function(){
      //This fn() will break ajax when there is no internet connected
      //FB.XFBML.parse();
      //-----------------------
      column_name = 's.confirmed_date';
      order = 'ASC';
      page = '1';

      url_string = window.location.href;
      url = new URL(url_string);
      settingsPage = url.searchParams.get("settingsPage");

      show_data('general');

      function show_data(page){
        if (settingsPage == 'codes') {
            page = 'codes';
            settingsPage = '';
        }
        $.ajax({
          url:"ajax/ajaxProfileSettings.php",
          method:"POST",
          data:{page:page},
          success:function(data)
          {
            $('#profile_settings').html(data);
            $('[data-toggle="tooltip"]').tooltip();
            FB.XFBML.parse();
            /*$('#'+column_name+'').append(arrow);*/
          }
        });
      }

      $(document).on('click', '#general', function(){
        show_data('general');
      });

      $(document).on('click', '#payment', function(){
        show_data('payment');
      });

      $(document).on('click', '#security', function(){
        show_data('security');
      });

      $(document).on('click', '#codes', function(){
        show_data('codes');
      });

      $(document).on('click', '#admin', function(){
        show_data('admin');
      });

      $(document).on('click', '#social_show_label', function(){
        $.ajax({
          url:"ajax/ajaxProfileSettings.php",
          method:"POST",
          data:{page:'general', socialShowChange:'1'},
          success:function(data)
          {
            $('#profile_settings').html(data);
          }
        });
      });

      $(document).on('click', '#debug_mode_label', function(){
        $.ajax({
          url:"ajax/ajaxProfileSettings.php",
          method:"POST",
          data:{page:'admin', debugModeChange:'1'},
          success:function(data)
          {
            location.reload(true);
            //$('#profile_settings').html(data);
          }
        });
      });

      $(document).on('click', '#maintain_mode_label', function(){
        $.ajax({
          url:"ajax/ajaxProfileSettings.php",
          method:"POST",
          data:{page:'admin', maintainModeChange:'1'},
          success:function(data)
          {
            location.reload(true);
          }
        });
      });

      $(document).on('click', '.column_sort', function(){
        column_name = $(this).attr("id");
        order = $(this).data("order");
        show_data(page, column_name, order);
        $('#test').append('Page: '+page+'; ');
        $('#test').append('Column_name: '+column_name+'; ');
        $('#test').append('Order: '+order+'; ');
      });

      $(document).on('click', '.page', function(){
        page = $(this).attr("id");
        show_data(page, column_name, order);
        $('#test').append(page);
      });

      $(document).on('click', '#set-instagram', function(){
        $("#edit_instagram").slideToggle("slow");
        $("#edit_facebook").slideUp("slow");
        $("#instagram-arrow-down").toggle();
        $("#instagram-arrow-up").toggle();
        $("#facebook-arrow-down").show();
        $("#facebook-arrow-up").hide();
      });
      
      $(document).on('click', '#set-facebook', function(){
        $("#edit_facebook").slideToggle("slow");
        $("#edit_instagram").slideUp("slow");
        $("#instagram-arrow-down").show();
        $("#instagram-arrow-up").hide();
        $("#facebook-arrow-down").toggle();
        $("#facebook-arrow-up").toggle();
      });

    });