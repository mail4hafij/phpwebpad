/** -------------------------------------------------------------------------------------*
* Version: 4.0                                                                           *
* framework: https://github.com/mail4hafij/phpwebpad                                     *
* License: Free to use                                                                   *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

$(function(){
  /** 
  * START: Handling ajax call
  */
  $(document).ajaxSend(function (e, xhr, opt) {
    // We can add more url that should't start the loader here...
    var showLoader = (opt.url != "/Controller/Action");
    if (showLoader == true) {
      $.loader({
        className: "blue-with-image-alt",
        content: ''
      });
    }
  });
  
  $(document).ajaxError(function () {
    $.loader('close');
  });
  
  $(document).ajaxComplete(function () {
    $.loader('close');
  });
  /**
  * END
  */


  /**
  * START: Making any form to ajax form.
  */
  // error = null | "any error msg from controller"
  // showmsg = "any div id" | null (then it will render the output into #showmsg div)
  // url = "current" | "any url" | null
  // html = "any html content" | null
  var showJsonResponse = function (obj) {
    if (obj.error == null) {
      
      // check if success message has been sent from the controller
      if (obj.success != null) {
        if (obj.showmsg != null) {
          $("#" + obj.showmsg).html(obj.success).addClass("success").removeClass("notice").removeClass("dsn").removeClass("error");
        } else {
          $("#showmsg").html(obj.success).addClass("success").removeClass("notice").removeClass("dsn").removeClass("error");
        }
      }

      // check if html has been sent from the controller
      if (obj.html != null) {
        if (obj.showmsg != null) {
          $("#" + obj.showmsg).html(obj.html).removeClass("error");
        } else {
          $("#showmsg").html(obj.html).removeClass("error");
        }
      }

      // check if something needs to show
      if (obj.show != null) {
        if (Array.isArray(obj.show)) {
          obj.show.map(function (e) {
            $("#" + e).show();
          });
        } else {
          $("#" + obj.show).show();
        }
      }

      // check if something needs to hide
      if (obj.hide != null) {
        if (Array.isArray(obj.hide)) {
          obj.hide.map(function (e) {
            $("#" + e).hide();
          });
        } else {
          $("#" + obj.hide).hide();
        }
      }

      // check if something needs to render
      if (obj.render != null) {
        if (Array.isArray(obj.render)) {
          obj.render.map(function (e) {
            $("#" + e)
              .load($("#" + e).attr("src"))
              .show();
          });
        } else {
          $("#" + obj.render)
            .load($("#" + obj.render).attr("src"))
            .show();
        }
      }

      // check if something needs to remove
      if (obj.remove != null) {
        if (Array.isArray(obj.remove)) {
          obj.remove.map(function (e) {
            $("#" + e).empty();
          });
        } else {
          $("#" + obj.remove).empty();
        }
      }
      
      // check if url is set or not
      if (obj.url == null) {
        // do nothing...
      } else if (obj.url == "current") {
        location.reload();
      } else {
        // check if container is set or not
        if(obj.container != null) {
          $("#" + obj.container).load(obj.url);
        } else {
          window.location = obj.url;
        }
      }
      
    } else {
      // error message has been sent from the controller
      // obj.error = obj.error + "<br />" + "<a href='#' class='bug' data-toggle='modal' data-target='#bug_report'>Rapportera ett problem</a>";
      if (obj.showmsg != null) {
        if(obj.showmsg == "alert") {
          alert(obj.error);
        } else {
          $("#" + obj.showmsg).html(obj.error).addClass("error").removeClass("notice").removeClass("dsn");
          $("html, body").animate({
            scrollTop: $("#" + obj.showmsg).offset().top - 30
          }, 500);
        }
      } else {
        $("#showmsg").html(obj.error).addClass("error").removeClass("notice").removeClass("dsn");
        $("html, body").animate({
            scrollTop: $("#showmsg").offset().top - 30
          }, 500);
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
  
  // Binding the form submit button with name jsonsubmit
  $(document).on('click', "button[name='jsonsubmit']", function () {
    /*  
    if ($(this).hasClass("confirm")) {
      var conf = confirm("Please confirm!");
      if (!conf) {
        return false;
      }
    }
    */
    if ($(this).hasClass("confirm")) {
      var mainForm = $(this.form);
      $.post("/Translation/getJsConfirm", {phrase: 'Please confirm!'}, function(json) {
        $.confirm({
          'message'	: json.text,
          'buttons'	: {
              yes : {
                'name' : json.yes,
                'class'	: 'blue',
                'action': function(){
                  
                  $(mainForm).ajaxSubmit(options);
                  return false;
                  
                }
              },
              no : {
                'name' : json.no,
                'class'	: 'gray',
                'action': function(){
                  // Do nothing
                  return false;
                }	
              }
            }
        });
      });
    } else {
      $(this.form).ajaxSubmit(options);
    }
    
    return false;
  });
  
  // Get a value from get variables given a key
  function getFromQueryString(url, key) {
    if(url.indexOf('?') != -1) {
      var variables = url.split('?')[1];
      if(variables.indexOf('&') != -1) {
        // multiple parameters
        var parameters = variables.split('&');
        for(var i = 0; i < parameters.length; i++) {
          var pair = parameters[i].split('=');
          if (pair[0] == key) {
            return pair[1];
          }
        }
      } else {
        // single parameter
        var pair = variables.split('=');
        if (pair[0] == key) {
          return pair[1];
        }
      }
    }
    return null;
  }
  
  // Handle get requests
  $(document).on('click', "a.get", function() {
    var url = $(this).attr("href");
    var addClass = getFromQueryString(url, 'addClass');
    if(addClass != null) {
      $(this).addClass(addClass);
    }
    
    var container = getFromQueryString(url, 'container');
    if ($(this).hasClass("confirm")) {
      $.post("/Translation/getJsConfirm", {phrase: 'Please confirm!'}, function(json) {
        $.confirm({
          'message'	: json.text,
          'buttons'	: {
              yes : {
                'name' : json.yes,
                'class'	: 'blue',
                'action': function(){
                  // Yes has been clicked.
                  if(container == null) {
                    $.get(url, showJsonResponse);
                  } else {
                    $("#" + container).load(url);
                  }
                }
              },
              no : {
                'name' : json.no,
                'class'	: 'gray',
                'action': function(){
                  // Do nothing
                  return false;
                }	
              }
            }
        });
      });
      /*
      var conf = confirm("Are you sure?");
      if (conf) {
        // check if container is set or not
        if (container == null) {
          $.get(url, showJsonResponse);
        } else {
          $("#" + container).load(url);
        }
      }
      */
    } else {
      // check if container is set or not
      if(container == null) {
        $.get(url, showJsonResponse);
      } else {
        $("#" + container).load(url);
      }
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
  * END
  */
});