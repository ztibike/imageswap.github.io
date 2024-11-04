window.imageswap = window.imageswap || {};
window.imageswap.uploadModule = (function ($) {
    //Initialize the necessary variables
    let protocol = window.location.protocol;
    let host = window.location.host;
    let fullUrl = protocol + '//' + host;
    let uploadedFile = $('#uploaded_file');
    let form = $('form.cart');
    let cartButton = $('button.single_add_to_cart_button');
    let productId = cartButton.val();
    let htmlForm = $('#file_upload_form');
    let url = fullUrl + '/wp-json/imageswap-plugin-main/v1/upload-image';
    let imageFileName = '';

    function init() {
        //Remove the original add-to-cart button name
        cartButton.prop('name', null);
        form.append('<input type="submit" name="add-to-cart" value="' + productId + '" style="display:none;">');
        cartButton.on('click', function (e) {
            e.preventDefault();
            let inputFile = uploadedFile[0];

            if (inputFile.files.length > 0) {
                let formData = new FormData(htmlForm[0]);
                //send the AJAX request
                uploadAjax(url, formData)
                    .then(() => {
                        form.append('<input type="hidden" name="image_file_name" id="image_file_name" value="' + imageFileName + '">');
                        $('[name=add-to-cart]').trigger('click');
                    })
                    .catch(error => {
                        alert('Error: ' + error);
                    });
            } else {
                alert('There is no file selected!');
            }
        });
    }
//This is the function for the request
    function uploadAjax(url, formData) {
        return fetch(url, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                imageFileName = data.fileName;
                //alert('Successfully Uploaded! ' + imageFileName);
            } else {
                throw new Error(data.error || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Something went wrong!', error);
            throw error; 
        });
    }

    init();
})(jQuery);
