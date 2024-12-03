<template>
    <div class="modal fade" id="holdSaleModal" tabindex="-1" aria-labelledby="holdSaleModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="holdSaleModalLabel">Hold Sale</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to hold this sale?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger" @click="confirmHoldSale">Hold Sale</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    methods: {
        confirmHoldSale() {
            this.$emit('hold-sale-confirmed');
            const myModalEl = document.getElementById('holdSaleModal');
            const modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide(); 
        }
    }
  };
  </script>
  