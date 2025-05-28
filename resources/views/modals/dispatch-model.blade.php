<div class="modal fade" id="dispatchModal">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 pt-2">
                <button type="button" class="close" id="closeDispatchIcon" data-dismiss="modal" aria-label="Close"
                    title="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-0">
                    <h3 class="mb-4 head-sm medium text-center" id="dispatchTitle"></h3>
                    <form id="updateDispatchForm" class="w-75 mx-auto">
                        <input type="hidden" id="bookingIdInput" name="booking_id">
                        <div class="form-group row mb-0">
                            <label for="driverNotified" class="col-sm-6 col-form-label">Driver Notified</label>
                            <div class="col-sm-6 text-end">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="driverNotified"
                                        name="is_driver_notified">
                                    <label for="driverNotified" class="custom-control-label"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label for="driverAcknowledge" class="col-sm-6 col-form-label">Driver Acknowledge</label>
                            <div class="col-sm-6 text-end">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="driverAcknowledge"
                                        name="is_driver_acknowledge">
                                    <label for="driverAcknowledge" class="custom-control-label"></label>
                                </div>
                            </div>
                        </div>
                    </form>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="submit" id="saveDispatchButton" class="float-right btn btn-outline-primary mx-2"
                    title="Save">
                    Save
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
