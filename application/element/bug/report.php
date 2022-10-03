<!-- translation form Modal -->
<div class="modal fade" id="bug_report" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php Bob::say("Report a problem"); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="/application/submitBugReport" method="post">
        <div class="modal-body">
          <div id="showmsg_bug_report"></div>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label><?php Bob::say("Describe the problem"); ?></label>
              <textarea class="width100p" name="problem" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn w-50 text-center justify-content-center btn-outline-primary" data-dismiss="modal"><?php Bob::say("Cancel"); ?></button>
          <button name="jsonsubmit" type="submit" class="btn w-50 bold text-center justify-content-center btn-primary"><?php Bob::say("Submit"); ?></button>
        </div>
      </form>
      
    </div>
  </div>
</div>