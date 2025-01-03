<template>
  <div class="modal fade" id="finishSaleModal" tabindex="-1" aria-labelledby="finishSaleModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="finishSaleModalLabel">Finish Sale</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Have you verified the items?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" @click="confirmFinishSale">Finish Sale</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    confirmFinishSale() {
      this.$emit('finish-sale-confirmed');
      const myModalEl = document.getElementById('finishSaleModal');
      const modal = bootstrap.Modal.getInstance(myModalEl);
      modal.hide();
    }
  }
};
</script>