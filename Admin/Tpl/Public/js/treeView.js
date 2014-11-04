$(document).ready(function(){
    $('.level1').menuTree({
        animation: true,
        handler: 'slideToggle',
        anchor: 'span.toggle',
        trace: false
    });
});