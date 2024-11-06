window.imageswap = window.imageswap || {};
window.imageswap.uploadModule = (function ($) {
    $(window).on('load',function(){
let deleteButton=$('button.wc-block-cart-item__remove-link');

function init(){
    
    
    deleteButton.on('click',function(e){
        let targetDiv = $(this).parent().parent();
        let imageFileName= targetDiv.find('wc-block-components-product-details__value').text();
    e.preventDefault();
    alert(imageFileName);
    });
}
init();
});
})(jQuery);