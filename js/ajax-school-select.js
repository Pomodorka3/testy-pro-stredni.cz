$(document).ready(function () {

    city_load();

    function city_load(){
        $.ajax({
            type: 'POST',
            url: 'ajax/ajaxSchoolSelect.php',
            data: 'load=city',
            success: function(html){
                $('#citylist').html(html);
                $('#city_addlist').html(html);
            }
        });
    }

    //--------------------- SELECT SCHOOL ----------------------

    $('#city').on('keyup', function(){
        var citySearch = $(this).val();
        if (citySearch) {
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxSchoolSelect.php',
                data: {
                    load:'city',
                    citySearch:citySearch
                },
                success: function(html){
                    $('#district').val("");
                    $('#school').val("");
                    $('#citylist').html(html);
                }
            });
        }
    });

    $('#city').on('change', function(){
        cityName = $(this).val();
        if (cityName) {
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxSchoolSelect.php',
                data: 'cityName='+cityName,
                success: function(html){
                    $('#city').blur();
                    $('#district').val("");
                    $('#school').val("");
                    $('#districtlist').html(html);
                }
            });
        }
    });

    $('#district').on('keyup', function(){
        var districtSearch = $(this).val();
        if (districtSearch) {
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxSchoolSelect.php',
                data: {
                    districtSearch:districtSearch,
                    cityName:cityName
                },
                success: function(html){
                    $('#school').val("");
                    $('#districtlist').html(html);
                }
            });
        }
    });

    $('#district').on('change', function(){
        districtName = $(this).val();
        if (districtName) {
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxSchoolSelect.php',
                data: 'districtName='+districtName,
                success: function(html){
                    $('#district').blur();
                    $('#school').val("");
                    $('#schoollist').html(html);
                }
            });
        }
    });

    $('#school').on('keyup', function(){
		var schoolSearch = $(this).val();
		//var districtID = $('#districtID').val();
		if (schoolSearch) {
			$.ajax({
				type: 'POST',
				url: 'ajax/ajaxSchoolSelect.php',
				data: {schoolSearch:schoolSearch, districtName:districtName},
				success: function(html){
					$('#schoollist').html(html);
				}
			});
		} else {
			$('#school').html('<option value="">Prvně vyberte čtvrť</option>');
		}
    });
    
    $('#school').on('change', function(){
        $('#school').blur();
    });

    //--------------------- ADD NEW SCHOOL ----------------------

    $('#city_add').on('keyup', function(){
        var citySearch = $(this).val();
        if (citySearch) {
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxSchoolSelect.php',
                data: {
                    load:'city',
                    citySearch:citySearch
                },
                success: function(html){
                    $('#district_add').val("");
                    $('#city_addlist').html(html);
                }
            });
        }
    });

    $('#city_add').on('change', function(){
        cityName = $(this).val();
        if (cityName) {
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxSchoolSelect.php',
                data: 'cityName='+cityName,
                success: function(html){
                    $('#city_add').blur();
                    $('#district_add').val("");
                    $('#district_addlist').html(html);
                }
            });
        }
    });

    $('#district_add').on('keyup', function(){
        var districtSearch = $(this).val();
        if (districtSearch) {
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxSchoolSelect.php',
                data: {
                    districtSearch:districtSearch,
                    cityName:cityName
                },
                success: function(html){
                    $('#district_addlist').html(html);
                }
            });
        }
    });

    $('#district_add').on('change', function(){
        districtName = $(this).val();
        if (districtName) {
            $.ajax({
                type: 'POST',
                url: 'ajax/ajaxSchoolSelect.php',
                data: 'districtName='+districtName,
                success: function(html){
                    $('#district_add').blur();
                }
            });
        }
    });
});