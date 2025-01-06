<template>
  <div class="modal fade" id="laywayModal" tabindex="-1" aria-labelledby="laywayModalLabel">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="laywayModalLabel">Layway</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-4">
                <label for="customer_name">Customer Name</label>
                <input type="text" class="form-control">
            </div>
            <div class="col-sm-4">
                <label for="customer_name">Customer Email</label>
                <input type="text" class="form-control">
            </div>
            <div class="col-sm-4">
                <label for="customer_name">Contact Number</label>
                <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success">Proceed</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    
  }
};
</script>