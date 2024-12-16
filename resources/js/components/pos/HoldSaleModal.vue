<template>
  <div class="modal fade" id="holdSaleModal" tabindex="-1" aria-labelledby="holdSaleModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="holdSaleModalLabel">Hold Sale</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="holdSaleCustomerName">Customer name</label>
            <input type="text" id="holdSaleCustomerName" class="form-control" v-model="customerName" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-danger" :disabled="!customerName" @click="confirmHoldSale">Hold Sale</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data(){
    return {
      customerName: ''
    }
  },
  methods: {
    confirmHoldSale() {
      this.$emit('hold-sale-confirmed', this.customerName);
      this.customerName = '';
      bootstrap.Modal.getInstance($('#holdSaleModal')).hide();
    }
  }
};
</script>