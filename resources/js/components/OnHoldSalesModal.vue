<template>
    <div class="modal fade" id="onHoldSalesModal" tabindex="-1" aria-labelledby="onHoldSalesModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="onHoldSalesModalLabel">On Hold Sales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-striped table-bordered" v-if="holdSales.length > 0">
                        <thead>
                            <tr>
                                <th class="p-1">#</th>
                                <th class="p-1">Customer Name</th>
                                <th class="p-1">Sale Items</th>
                                <th class="p-1">Return Items</th>
                                <th class="p-1">Grand Total</th>
                                <th class="p-1">Date & Time</th>
                                <th class="p-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(sale, index) in holdSales" :key="sale.index">
                                <td class="p-1">{{ index + 1 }}</td>
                                <td class="p-1">{{ sale.basicInfo.customerName || 'Unknown' }}</td>
                                <td class="p-1">{{ sale.orderItems?.length || 0 }}</td>
                                <td class="p-1">{{ sale.returnItems?.length || 0 }}</td>
                                <td class="p-1">£{{ sale.basicInfo.grandTotal.toFixed(2) }}</td>
                                <td class="p-1">{{ this.formatDateTime(sale.basicInfo.timestamp) }}</td>
                                <td class="p-1">
                                    <button class="btn btn-primary btn-sm me-2 py-0 px-1" @click="handlePickHoldSale(index)">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm py-0 px-1" @click="handleDeleteHoldSale(index)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="text-center py-5 text-black" v-else>
                        <h1 class="mdi mdi-cart-outline"></h1>
                        <h4 class="py-2">No holded sale</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { EventBus } from '../eventBus';

export default {
    name: 'OnHoldSalesModal',
    data() {
        return {
            holdSales: []
        };
    },
    mounted() {
        const modal = document.getElementById('onHoldSalesModal');
        modal.addEventListener('show.bs.modal', this.getHoldSales);
    },
    beforeDestroy() {
        const modal = document.getElementById('onHoldSalesModal');
        modal.removeEventListener('show.bs.modal', this.getHoldSales);
    },
    methods: {
        getHoldSales() {
            if (localStorage.getItem('holdSales')) {
                this.holdSales = JSON.parse(localStorage.getItem('holdSales'));
            }
        },
        handleDeleteHoldSale(index) {
            this.holdSales.splice(index, 1);
            localStorage.setItem('holdSales', JSON.stringify(this.holdSales));
        },
        handlePickHoldSale(index) {
            EventBus.pickHoldSale = {
                index: index,
                timestamp: Date.now(),
            };
            this.holdSales.splice(index, 1);

            const modal = bootstrap.Modal.getInstance($('#onHoldSalesModal'));
            modal.hide();
        },
        formatDateTime(timestamp) {
            let now = new Date(timestamp);
            let day = now.getDate();
            let month = now.getMonth() + 1;
            let year = now.getFullYear();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();

            let period = hours >= 12 ? 'PM' : 'AM';

            hours = hours % 12;
            hours = hours ? hours : 12;

            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            let formattedDate = (day < 10 ? '0' + day : day) + '-' + month + '-' + year;

            let formattedTime = hours + ':' + minutes + ':' + seconds + ' ' + period;

            return formattedDate + ' ' + formattedTime;
        }
    }
};
</script>
