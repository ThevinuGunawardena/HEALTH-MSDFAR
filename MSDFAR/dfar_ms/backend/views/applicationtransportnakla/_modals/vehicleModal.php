<!-- Vehicle Modal -->
<div id="vehicleModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Vehicle Numbers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="vehicleFields">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Enter vehicle number">
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addVehicleField()">Add More</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveVehicleFields()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
