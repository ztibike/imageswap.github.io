window.imageswap = window.imageswap || {};
window.imageswap.uploadModule = (function ($) {
let uploadedFile = $('#uploaded_file');
let form=$('form.cart');
let cartButton = $('button.single_add_to_cart_button');
let productId=cartButton.val();
let htmlForm = $('#file_upload_form');
let url="https://z-connect.hu/wp-json/imageswap-plugin-main/v1/upload-image";
let imageFileName='';


function init()
    {
        cartButton.prop('name', null);
        
        form.append('<input type="submit" name="add-to-cart" value="'+productId+'" style="display:none;">');
        
        cartButton.on('click', function (e){
            e.preventDefault();
            let inputFile = uploadedFile[0];
            
         if (inputFile.files.length>0)
             {
                
                let formData = new FormData(htmlForm[0]);
                uploadAjax(url, formData);
                
                setTimeout(function(){
                form.append('<input type="hidden" name="image_file_name" id="image_file_name" value="'+imageFileName+'">');
                $('[name=add-to-cart]').trigger('click');
                },1000);
                
            }

            else {alert('There is no file selected!');}
        
        });
        
}


function uploadAjax(url, formData)
{
    $.ajax({
     url: url,
     type: 'POST',
     data:formData,
     processData: false,
     contentType:false,
     success: function(response){
        if (response.status ==='success')
        {
            imageFileName=response.fileName;
            alert('Uploaded!'+imageFileName);
            
        } 
        else 
        {
            alert('Something went wrong: ' + (response.error || 'Unknown error'));
        }
    },
     error: function(jqXHR, textStatus, errorThrown) {
        
        console.log('Something went wrong!');
        console.log('Status:', textStatus); 
        console.log('Error message:', errorThrown); 
        console.log('Detailed error:', jqXHR); 
    },

    });

}

init();





})(jQuery);

