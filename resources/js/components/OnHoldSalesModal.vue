<template>
    <div class="modal fade" id="onHoldSalesModal" tabindex="-1" aria-labelledby="onHoldSalesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="onHoldSalesModalLabel">On Hold Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sale ID</th>
                                <th>Total Items</th>
                                <th>Grand Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(sale, index) in sales" :key="sale.saleId">
                                <td>{{ index + 1 }}</td>
                                <td>{{ sale.saleId }}</td>
                                <td>{{ sale.totalItems }}</td>
                                <td>{{ sale.grandTotal }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm me-2">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'OnHoldSalesModal',
    data() {
        return {
            sales: [
                { saleId: 'S001', totalItems: 2, grandTotal: '£249', date: '2024-11-10' },
                { saleId: 'S002', totalItems: 5, grandTotal: '£499', date: '2024-11-12' },
                { saleId: 'S003', totalItems: 6, grandTotal: '£549', date: '2024-11-14' }
            ]
        };
    },
};
</script>
