$(function(){

  /**
  * start
  * Making any form to ajax form.
  */

  // error = null | "any error msg from controller"
  // showmsg = "any div id" | null then it will render the output into that div
  // url = "current" | "any url" | null
  // html = "any html" | null
  var showJsonResponse = function (obj) {
    if (obj.error == null) {
      // check if html has been sent from the controller
      if (obj.html != null) {
        if (obj.showmsg != null) {
          $("#" + obj.showmsg).html(obj.html).removeClass("error");
        } else {
          $("#showmsg").html(obj.html).removeClass("error");
        }
      }

      // check if success message has been sent from the controller
      if (obj.success != null) {
        if (obj.showmsg != null) {
          $("#" + obj.showmsg).html(obj.success).addClass("success").removeClass("notice").removeClass("dsn").removeClass("error");
        } else {
          $("#showmsg").html(obj.success).addClass("success").removeClass("notice").removeClass("dsn").removeClass("error");
        }
      }

      // check if url is set or not
      if (obj.url == null) {
        // do nothing...
      } else if (obj.url == "current") {
        location.reload();
      } else {
        // Check if the container is set or not
        if(obj.container != null) {
          $("#" + obj.container).load(obj.url);
        } else {
          window.location = obj.url;
        }
      }

    } else {
      if (obj.showmsg != null) {
        if(obj.showmsg == "alert") {
          alert(obj.error);
        } else {
          $("#" + obj.showmsg).html(obj.error).addClass("error").removeClass("notice").removeClass("dsn");
        }
      } else {
        $("#showmsg").html(obj.error).addClass("error").removeClass("notice").removeClass("dsn");
      }
    }
  };

  var options = {
    success: showJsonResponse,      // post-submit callback
    type: 'post',                   // 'get' or 'post', override for form's 'method' attribute
    dataType: 'json',               // 'xml', 'script', or 'json' (expected server response type)
    resetForm: false                // reset the form after successful

  // clearForm: true              // clear all form fields after successful submit
  // target: '#showmsg',          // target element(s) to be updated with server response
  // beforeSubmit:  showRequest,  // pre-submit callback
  // url:       url               // override for form's 'action' attribute
  // $.ajax options can be used here too, for example:
  // timeout:   3000
  };

  $("button[name='jsonsubmit']").on('click', function () {
    if ($(this).hasClass("confirm")) {
      var conf = confirm("Are you sure?");
      if (!conf) {
        return false;
      }
    }

    $(this.form).ajaxSubmit(options);
    return false;
  });
    
  function getURLParameter(url, index) {
    if(url.indexOf('?') != -1) {
      var variables = url.split('?')[1];
      if(variables.indexOf('&') != -1) {
        // multiple parameters
        var parameters = variables.split('&');
        for(var i = 0; i < parameters.length; i++) {
          var pair = parameters[i].split('=');
          if (pair[0] == index) {
            return pair[1];
          }
        }
      } else {
        // single parameter
        var pair = variables.split('=');
        if (pair[0] == index) {
          return pair[1];
        }
      }
    }
    return null;
  }
  
  $("a.get").on('click', function() {
    if ($(this).hasClass("confirm")) {
      var conf = confirm("Are you sure?");
      if (!conf) {
        return false;
      }
    }
    
    var url = $(this).attr("href");
    var addClass = getURLParameter(url, 'addClass');
    if(addClass != null) {
      $(this).addClass(addClass);
    }
    var container = getURLParameter(url, 'container');
    if(container == null) {
      $.get(url, showJsonResponse);
    } else {
      $("#" + container).load(url);
    }
    return false;
  });
  
  // if we have widgeEditor we need to submit the form by calling the following
  // method in onsubmit event of the form. In that case we can get the html before
  // we make an ajaxsubmit. And we dont need to name the button jsonsubmit since
  // we are using onsubmit event in the form.
  eventonsubmit = function (formid) {
    $(document.forms[formid]).ajaxSubmit(options);
    return false;
  };

  /**
  * end
  */
});