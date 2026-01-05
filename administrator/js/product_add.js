
//live search
$(document).ready(function(){  
    $('#search').keyup(function(){  
        var query = $(this).val();  
        if(query != '')  
        {  
            $.ajax({  
                url:"search_data.php",  
                method:"POST",  
                data:{query:query},  
                success:function(data)  
                {  
                    $('#productList').fadeIn();  
                    $('#productList').html(data);  
                }  
            });  
        }  
    });  
    $(document).on('click', 'li', function(){  
        $('#search').val($(this).text());  
        $('#productList').fadeOut();  
    });  
});

// $(document).ready(function() {
    
//     var max_fields      = 10; //maximum input boxes allowed
//     var wrapper   		= $(".addColorImg"); //Fields wrapper
//     var add_button      = $(".addColor"); //Add button ID
//     var color_html = $('#product_color').html();

//     var x = 1; //initlal text box count
//     $(add_button).click(function(e){ //on add input button click
//     e.preventDefault();
//         if(x < max_fields){ //max input box allowed
//             x++; //text box increment
//             $(wrapper).append('<div><div class="row mb-3"><label for="product_color" class="col-sm-3 col-form-label text-md-end">Màu sắc</label><div class="col-sm-5"><select class="form-select" id="product_color" name="product_color">'+color_html+'</select></div><div class="col-sm-3"><button class="btn btn-danger remove_btn">Xóa</button></div></div><div class="row mb-3"><label for="product_color-img" class="col-sm-3 col-form-label text-md-end">Ảnh theo màu</label><div class="col-sm-5"><input type="file" class="form-control" id="product_color-img" name="product_color-img"></div></div></div>'); //add input box
//         }
//     });
//     $(wrapper).on("click",".remove_btn", function(e){ //user click on remove text
// 		e.preventDefault(); $(this).parent('div').parent('div').parent('div').remove(); x--;
// 	})

//     var wrapper_config   		= $(".addConfig"); //Fields wrapper
//     var add_config_button      = $(".addConfigbtn"); //Add button ID
//     var y=1;
//     $(add_config_button).click(function(e){ //on add input button click
//         e.preventDefault();
//             if(y < max_fields){ //max input box allowed
//                 y++; //text box increment
//                 $(wrapper_config).append('<div><div class="row mb-3"> <label for="product_config_name" class="col-sm-3 col-form-label text-md-end">Tên cấu hình</label> <div class="col-sm-5"> <input type="text" class="form-control" id="product_config_name" name="product_config_name"> </div> <div class="col-sm-3"><button class="btn btn-danger remove_config_btn">Xóa</button></div></div><div class="row mb-3"> <label for="product_config_value" class="col-sm-3 col-form-label text-md-end">Thuộc tính</label> <div class="col-sm-5"> <input type="text" class="form-control" id="product_config_value" name="product_config_value"> </div> </div></div>'); //add input box
//             }
//         });
//     $(wrapper_config).on("click",".remove_config_btn", function(e){ //user click on remove text
// 		e.preventDefault(); $(this).parent('div').parent('div').parent('div').remove(); y--;
// 	})
//     });

// display img before upload
function preview_image(event) 
{
    var reader = new FileReader();
    reader.onload = function()
    {
    var output = document.getElementById('displayImg');
    output.src = reader.result;
    output.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}

