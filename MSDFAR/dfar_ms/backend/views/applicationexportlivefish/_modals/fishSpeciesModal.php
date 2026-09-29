<div id="fishSpeciesModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Fish Species Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="fishSpeciesFields">
                    <!-- Fish Species Fields -->
                    <div class="form-group"><input type="number" class="form-control" placeholder="Locally Collected Quantity (kg)" name="Applicationexportlivefish[locally_collected_fish_quantity_value][]"></div>
                    <div class="form-group"><input type="number" class="form-control" placeholder="Locally Bred Quantity (kg)" name="Applicationexportlivefish[locally_bred_fish_quantity_value][]"></div>
                    <div class="form-group"><input type="number" class="form-control" placeholder="Re-exported Quantity (kg)" name="Applicationexportlivefish[re_exported_fish_quantity_value][]"></div>
                    <div class="form-group"><input type="number" class="form-control" placeholder="Weight (kg)" name="Applicationexportlivefish[weight][]"></div>
                    <div class="form-group"><input type="text" class="form-control" placeholder="Species/Type" name="Applicationexportlivefish[species_type][]"></div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveStorePlaceFields()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>