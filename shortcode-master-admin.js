jQuery(document).ready(function ($) {
    // Handle tab navigation
   // $('#tab2').hide();
    $('.nav-tab-wrapper a').on('click', function (e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.tab-content').hide();
        $($(this).attr('href')).show();
        $('.main-cls').removeAttr('hidden')
    });
});