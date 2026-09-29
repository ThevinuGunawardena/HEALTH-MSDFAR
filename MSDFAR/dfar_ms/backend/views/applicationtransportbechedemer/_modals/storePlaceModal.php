<!-- Store Place Modal -->
<div id="storePlaceModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Store Place Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="storePlaceFields">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Species / Type">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Weight per purchasing district (Kg)">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Total Weight (Kg)">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Purchasing District">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Intermediate Destination / Store places">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Final Store Place / District">
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addStorePlaceField()">Add More</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveStorePlaceFields()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
