<div class="modal fade" id="blcackOutModal">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="closeBlcackOutInfoIcon" data-dismiss="modal" aria-label="Close"
                    title="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h3 class="mt-2 head-sm medium text-center mb-3">Blackout Period Confirmation</h3>
                <p> Dear <strong>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong>,the booking request is made within the blackout period.
                    The booking status will
                    be pending and a member from the ops team will be contacting you to confirm this booking shortly.</p>
            </div>
            <div class="modal-footer justify-content-right">
                <a class="btn btn-outline-danger px-4" id="closeBlcackOutInfoButton" data-dismiss="modal"
                    title="Close">Close</a>
                <a class="btn btn-primary px-4" id="yesSubmitForm" title="Yes">Yes</a>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
