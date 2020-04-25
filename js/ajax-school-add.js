$(document).ready(function(){

  column_name = 's.request_date';
  order = 'DESC';
  page = '1';

  filter_data();

  function filter_data(page, column_name, order){
    $.ajax({
      url:"ajax/ajaxSchoolAdd.php",
      method:"POST",
      data:{column_name:column_name, order:order, page:page},
      success:function(data)
      {
        $('#requests_list').html(data);
        $('[data-toggle="tooltip"]').tooltip();
        /*$('#'+column_name+'').append(arrow);*/
      }
    });
  }

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