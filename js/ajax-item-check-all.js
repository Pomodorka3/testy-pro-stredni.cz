$(document).ready(function(){

  column_name = 's.create_date';
  order = 'DESC';
  page = '1';

  filter_data();

  function filter_data(page, column_name, order){
    var schoolName = $('#search_school').val();
    var itemName = $('#search_name').val();
    var itemDescription = $('#search_description').val();
    var itemSubject = $('#item_subject').val();
    var itemType = $('.itemType:checked').val();
    var itemAnswers = $('.itemAnswers:checked').val();
    var schoolClass = $('.schoolClass:checked').val();
    $.ajax({
      url:"ajax/ajaxItemCheckAll.php",
      method:"POST",
      data:{schoolName:schoolName, itemName:itemName, itemDescription:itemDescription, itemSubject:itemSubject, column_name:column_name, order:order, itemType:itemType, schoolClass:schoolClass, page:page, itemAnswers:itemAnswers},
      success:function(data)
      {
        $('#item_check_all').html(data);
        $('[data-toggle="tooltip"]').tooltip();
        /*$('#'+column_name+'').append(arrow);*/
      }
    });
  }

  $('.filter_field').keyup(function(){
    filter_data();
  });

  $('.itemType').click(function(){
    filter_data();
    column_name = 's.confirmed_date';
    order = 'ASC';
    page = '1';
  });

  $('.itemAnswers').click(function(){
    filter_data();
    column_name = 's.confirmed_date';
    order = 'ASC';
    page = '1';
  });

  $('.schoolClass').click(function(){
    filter_data();
    column_name = 's.confirmed_date';
    order = 'ASC';
    page = '1';
  });

  $(document).on('click', '.column_sort', function(){
    column_name = $(this).attr("id");
    order = $(this).data("order");
    filter_data(page, column_name, order);
    $('#test').append('Page: '+page+'; ');
    $('#test').append('Column_name: '+column_name+'; ');
    $('#test').append('Order: '+order+'; ');
  });

  $(document).on('click', '.page', function(){
    page = $(this).attr("id");
    filter_data(page, column_name, order);
    $('#test').append(page);
  });
  
});