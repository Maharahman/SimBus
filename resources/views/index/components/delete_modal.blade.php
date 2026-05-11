<div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bg-dark border-danger">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title text-white mx-auto">Confirm Delete</h5>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center text-white pt-0">
                    <i class="bi bi-exclamation-triangle text-danger mb-3" style="font-size: 3rem;"></i>
                    <p>Are you sure you want to delete <br> <strong id="delete_name" class="text-warning"></strong>?</p>
                </div>
                <div class="modal-footer border-top-0 flex-column">
                    <button type="submit" class="btn btn-danger w-100 fw-bold">YES, DELETE</button>
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>