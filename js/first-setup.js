$(document).ready(function(){

  $image_crop = $('#image_demo').croppie({
    enableExif: true,
    enableOrientation: false,
    viewport: {
      width:200,
      height:200,
      type:'square' //circle
    },
    boundary:{
      width:300,
      height:300
    }
  });
 
  $('#profile_image').on('change', function(){
    var reader = new FileReader();
    reader.onload = function (event) {
      $image_crop.croppie('bind', {
        url: event.target.result
      }).then(function(){
        console.log('jQuery bind complete');
      });
    }
    reader.readAsDataURL(this.files[0]);
    $('#uploadimageModal').modal('show');
  });
 
  $('.crop_image').click(function(event){
    $image_crop.croppie('result', {
      type: 'canvas',
      size: 'viewport'
    }).then(function(response){
      $.ajax({
        url:"ajax/ajaxProfilePicture.php",
        type: "POST",
        data:{"image": response},
        success:function(data)
        {
          $('#uploadimageModal').modal('hide');
          //$('#uploaded_image').html(data);
        }
      });
    })
  });
  /*$( "#rotateLeft" ).click(function() {
      $image_crop.croppie('rotate', parseInt($(this).data('rotate')));
  });
  
  $( "#rotateRight" ).click(function() {
      $image_crop.croppie('rotate',parseInt($(this).data('rotate')));
  });*/
  
  $("#set-instagram").click(function(){
    $("#instagram").slideToggle("slow");
    $("#instagram-arrow-down").toggle();
    $("#instagram-arrow-up").toggle();
  });
  
  $("#set-facebook").click(function(){
    $("#facebook").slideToggle("slow");
    $("#facebook-arrow-down").toggle();
    $("#facebook-arrow-up").toggle();
  });
  
});  