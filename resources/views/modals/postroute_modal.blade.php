<div class="modal fade" id="postRoute" tabindex="-1" aria-labelledby="postRoute" aria-modal="false" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg post-route assign-route" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myExtraLargeModal">
                    Post Routes to Loadboard
                </h5>
                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="mb-2">Routes Selected</h6>
                <table id="loadersTable" class="table table-responsive text-nowrap custom-scrollbar mb-4">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Route ID</th>
                            <th>D/S Date</th>
                            <th>PDT</th>
                            <th>Stops</th>
                            <th>Miles</th>
                            <th>Forklift</th>
                            <th>Recomended Price</th>
                            <th>Loading By Loader/Driver</th>
                            <th>Total Price</th>
                            <!-- <th>Save</th> -->
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <p class="mt-3 mb-0 fs-12">
                The recommended Price have several price matrices such as forklift,
                    stops, miles as well as the updated fuel price through
                    the API integration.
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary post-route-close" type="button" data-bs-dismiss="modal">
                    Close
                </button>
                <button class="btn btn-success-outline save-postloadboard-button" type="button">
                    Confirm & Post to Loadboard
                </button>
            </div>
        </div>
    </div>
</div>