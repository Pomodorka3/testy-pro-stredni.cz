$(document).ready(function(){

      page = '1';

  		filter_data();

      //Refresh every 3 seconds
      /*setInterval(function(){
        filter_data(page);
      }, 3000);*/

  		function filter_data(page){
  			var username = $('#username').val();
        var firstNameSearch = $('#first_name_search').val();
        var lastNameSearch = $('#last_name_search').val();
        var instagramSearch = $('#instagram_search').val();
        var facebookSearch = $('#facebook_search').val();
        var show = get_filter('checkbox_filter_admin');
        var group = get_filter('group');
        var activated = $('#activated');
        var banned = $('#banned');
        var online = $('#online');
        var offline = $('#offline');
        if (activated.prop('checked')) {
          activated = 1;
        } else {
          activated = 0;
        }
        if (banned.prop('checked')) {
          banned = 1;
        } else {
          banned = 0;
        }
        if (online.prop('checked')) {
          online = 1;
        } else {
          online = 0;
        }
        if (offline.prop('checked')) {
          offline = 1;
        } else {
          offline = 0;
        }
  			$.ajax({
  				url:"ajax/ajaxUsersearch.php",
  				method:"POST",
  				data:{username:username, show:show, activated:activated, banned:banned, firstNameSearch:firstNameSearch, lastNameSearch:lastNameSearch, instagramSearch:instagramSearch, facebookSearch:facebookSearch, group:group, page:page, online:online, offline:offline},
  				success:function(data)
  				{
            $('#user_table').html(data);
            $('[data-toggle="tooltip"]').tooltip();
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

  		$('.username').keyup(function(){
  			filter_data();
  		});

      $('.first_name_search').keyup(function(){
        filter_data();
      });

      $('.last_name_search').keyup(function(){
        filter_data();
      });

      $('.instagram_search').keyup(function(){
        filter_data();
      });

      $('.facebook_search').keyup(function(){
        filter_data();
      });

      $('.checkbox_filter_admin').click(function(){
        filter_data();
      });

      $('.group').click(function(){
        filter_data();
      });

      $('#activated').click(function(){
        filter_data();
      });

      $('#banned').click(function(){
        filter_data();
      });

      $('#online').click(function(){
        filter_data();
      });

      $('#offline').click(function(){
        filter_data();
      });

      $(document).on('click', '#refresh-table', function(){
        filter_data();
      });

      /*$(document).on('click', '.column_sort', function(){
        column_name = $(this).attr("id");
        order = $(this).data("order");
        filter_data(page, column_name, order);
        $('#test').append('Page: '+page+'; ');
        $('#test').append('Column_name: '+column_name+'; ');
        $('#test').append('Order: '+order+'; ');
      });
      */

      $(document).on('click', '.page', function(){
        page = $(this).attr("id");
        filter_data(page);
        //$('#test').append(page);
      });
});