<style type="text/css">

#log_picker {
  background: none repeat scroll 0 0 #FFFFFF;
  border-bottom: 1px solid #D8D8D8;
  border-right: 1px solid #D8D8D8;
  /*border-top: 1px solid #D8D8D8;*/
  left: -250px;
  padding: 10px;
  position: fixed;
  top: 0px;
  z-index: 9999;
  width: 225px;
}

#log_picker h3 {
  font-size: 16px !important;
  margin: 0 0 5px;
}

.log_picker_toggle_wrapper {
  background: none repeat scroll 0 0 #FFFFFF;
  border-bottom: 1px solid #D8D8D8;
  border-right: 1px solid #D8D8D8;
  border-top: 1px solid #D8D8D8;
  display: block;
  height: 45px;
  position: absolute;
  right: -46px;
  top: -1px;
  width: 45px;
}

.log_picker_toggle {
  background: url("/web/img/log.png") no-repeat scroll left top #FFFFFF;
  height: 27px;
  position: absolute;
  right: 9px;
  top: 9px;
  width: 27px;
}

.log_picker_toggle_open {
  background: url("/web/img/log.png") repeat scroll left bottom #FFFFFF;
}

</style>

<script type="text/javascript">
  $(function(){
    
    var helperEvent = function(){
      if($(this).hasClass('log_picker_toggle_open')) {
        $("div#log_picker").animate({"left": "-=225px"}, "slow");
        $(this).removeClass('log_picker_toggle_open');
        $.cookie("log_show_helper", "on", {path: '/'});
        return false;
      } else {
        $("div#log_picker").animate({"left": "+=225px"}, "slow");
        $(this).addClass('log_picker_toggle_open');
        $.cookie("log_show_helper", "off", {path: '/'});
        return false;
      }
    };
    
    // layout
    $('a.log_picker_toggle').click(helperEvent);
    
    // default
    if($.cookie("log_show_helper") === "on") {
      
    } else {
      $('a.log_picker_toggle').trigger('click');
    }
    
  });
</script>

<div id="log_picker">
    
  <?php
  $index = 0;
  foreach($log as $l) {
    $index++;
    $class = "odd";
    if($index % 2 == 0) {
      $class = "even";
    }
    echo("<div class='$class border mt5'>$l</div>");
  }
  ?>
  
  <div class="log_picker_toggle_wrapper">
    <a class="log_picker_toggle" href=""></a>
  </div>
  
</div>
