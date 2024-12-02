<template>
    <div class="modal fade" id="cancelSaleModal" tabindex="-1" aria-labelledby="cancelSaleModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="cancelSaleModalLabel">Cancel Sale</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to cancel this sale?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger" @click="confirmCancelSale">Cancel Sale</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    methods: {
        confirmCancelSale() {
            this.$emit('cancel-sale-confirmed');
            const myModalEl = document.getElementById('cancelSaleModal');
            const modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide(); 
        }
    }
  };
  </script>
  