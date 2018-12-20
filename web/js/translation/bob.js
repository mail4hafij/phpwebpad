$(function() {
  
  $(".translate").bind("contextmenu",function(e) {
    e.preventDefault();
    
    // create and show 
    var dicId = $(this).attr('id').split("-")[1];
    $("a#hidden_spy").attr("href", "/Translation/translationForm/" + dicId).trigger('click');
    
  });
  
});