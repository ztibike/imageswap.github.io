window.imageswap = window.imageswap || {};
window.imageswap.deleteModule = (function ($) {
    $(document).ready(function() {
        let classEs = $('.wc-block-components-product-details__name');
        let removeItemButton = $('button.wc-block-cart-item__remove-link');
        

classEs.on('click', function(e){

            let parentRow=this.closest('tr');
            parentRow = $(parentRow);
            let imageFileName=parentRow.find('.wc-block-components-product-details__value').text();
            removeItemButton.prop('class',null);
            alert(imageFileName);
        });

        
    });
})(jQuery);
