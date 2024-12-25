<template>
    <div class="modal fade" id="transferInventoryModal" tabindex="-1" aria-labelledby="transferInventoryModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferInventoryModalLabel">Transfer Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Sale Items</th>
                                <th>Return Items</th>
                                <th>Grand Total</th>
                                <th>Date & Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'InventoryTransferModal',
};
</script>
