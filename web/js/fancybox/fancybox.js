$(function(){
  $("a.fancybox").fancybox({
    'titleShow'     : false,
    'transitionIn'	: 'elastic',
    'transitionOut'	: 'elastic'
  });

  $("a.iframefancybox").fancybox({
    'titleShow'     : false,
    'transitionIn'	: 'elastic',
    'transitionOut'	: 'elastic',
    'type'          : 'iframe'
  });
  
  $("a.cleanfancybox").fancybox({
    'titleShow'     : false,
    'transitionIn'	: 'elastic',
    'transitionOut'	: 'elastic',
    'onCleanup'     : function(){ location.reload(); }
  });

  $("a.cleaniframefancybox").fancybox({
    'titleShow'     : false,
    'transitionIn'	: 'elastic',
    'transitionOut'	: 'elastic',
    'type'          : 'iframe',
    'onCleanup'     : function(){ location.reload(); }
  });
});
