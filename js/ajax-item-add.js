$(document).ready(function(){

      filter_data();

      function filter_data(){
        var teacher_input = $('#teacher_input').val();
        //var itemDescription = $('#search_description').val();
        $.ajax({
          url:"ajax/ajaxItemAdd.php",
          method:"POST",
          data:{teacher_input:teacher_input},
          success:function(data)
          {
            $('#teacher').html(data);
          }
        });
      }

      $('#teacher_input').keyup(function(){
        filter_data();
      });

    });