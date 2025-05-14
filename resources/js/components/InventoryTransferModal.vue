<template>
    <div class="modal fade" id="transferInventoryModal" tabindex="-1" aria-labelledby="transferInventoryModalLabel"
        aria-hidden="true" @shown.bs.modal="onModalShow">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferInventoryModalLabel">Transfer Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-2">
                            <label>From</label>
                            <select class="form-control" v-model="fromStore" disabled>
                                <option disabled value="">Select a store</option>
                                <option v-for="(branch, index) in branches" :key="index" :value="branch.id">
                                    {{ branch.name }}
                                </option>
                            </select>
                        </div>

                        <div class="col-2">
                            <label>To</label>
                            <select class="form-control" v-model="toStore" :disabled="!fromStore">
                                <option disabled value="">Select a store</option>
                                <option v-for="(branch, index) in branches" :key="index" :value="branch.id"
                                    :disabled="branch.id === fromStore">
                                    {{ branch.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-2">
                            <BarCodeBox :item-to-be-add="itemToBeAdd" @transfer-item="transferItem" />
                        </div>
                        <div class="col-4">
                            <button class="btn btn-primary mt-4" :disabled="!toStore || !fromStore || !items.length"
                                @click="transferInventory">Transfer Items</button>
                        </div>
                    </div>

                    <table class="table table-striped table-bordered mt-2">
                        <thead>
                            <tr>
                                <th class="align-middle p-1">#</th>
                                <th class="align-middle p-1">Article Code</th>
                                <th class="align-middle p-1">Description</th>
                                <th class="align-middle p-1">Color</th>
                                <th class="align-middle p-1">Size</th>
                                <th class="align-middle p-1">Brand</th>
                                <th class="align-middle p-1">Price</th>
                                <th class="align-middle p-1">Quantity</th>
                                <th class="align-middle p-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items" :key="item.code">
                                <td class="p-1">{{ items.length - index }}</td>
                                <td class="p-1">{{ item.code }}</td>
                                <td class="p-1">{{ item.description }}</td>
                                <td class="p-1">{{ item.color }}</td>
                                <td class="p-1">{{ item.size }}</td>
                                <td class="p-1">{{ item.brand }}</td>
                                <td class="p-1">{{ item.price }}</td>
                                <td class="p-1">{{ item.quantity }}</td>
                                <td class="p-1">
                                    <button class="btn btn-danger btn-sm py-0 px-1" @click="deleteItem(index)">
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

    <div class="modal fade" id="transferInventoryHistoryModal" tabindex="-1" aria-labelledby="transferInventoryHistoryModalLabel"
        aria-hidden="true" @shown.bs.modal="onHistoryModalShow">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferInventoryHistoryModalLabel">Transfer Inventory History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered" id="tranfer-history-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Sent By</th>
                                <th>No. of Items</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(transfer, index) in inventoryHistory" :key="transfer.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ transfer.sent_from.name }}</td>
                                <td>{{ transfer.sent_to.name }}</td>
                                <td>{{ transfer.sent_by.name }}</td>
                                <td>{{ inventoryHistory.length }}</td>
                                <td>{{ formatDate(transfer.created_at) }}</td>
                                <td>
                                    <a :href="`/inventory-transfer-details/${transfer.id}`" target="_blank" class="btn btn-secondary btn-sm py-0 px-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <AddManufactureBarcodeModal :item="itemToBeAdd" @item-scanned="itemScanned" />

</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import BarCodeBox from '../components/inventory-transfer/BarCodeBox.vue';
import AddManufactureBarcodeModal from '../components/inventory-transfer/AddManufactureBarcodeModal.vue';

export default {
    components: {
        BarCodeBox,
        AddManufactureBarcodeModal
    },
    name: 'InventoryTransferModal',
    data() {
        const storedItems = localStorage.getItem('transferItems') ? JSON.parse(localStorage.getItem('transferItems')) : [];
        const sortedItems = storedItems.sort((a, b) => b.sno - a.sno);
        return {
            table : null,
            branches: [],
            fromStore: window.config.currentUserStore,
            toStore: '',
            itemToBeAdd: {},
            items: sortedItems,
            inventoryHistory: [],
        };
    },
    created() {
        this.fetchBranches();
    },

    watch: {
        fromStore(newVal) {
            if (newVal === this.toStore) {
                this.selectAnotherStore('toStore');
            }
        },
        toStore(newVal) {
            if (newVal === this.fromStore) {
                this.selectAnotherStore('fromStore');
            }
        }
    },
    mounted() {
        const historyModal = document.getElementById('transferInventoryHistoryModal');
        if (historyModal) {
            historyModal.addEventListener('shown.bs.modal', this.onHistoryModalShow);
            historyModal.addEventListener('hidden.bs.modal', () => {
                // Clean up DataTable when modal is closed
                if ($.fn.DataTable.isDataTable('#tranfer-history-table')) {
                    this.table.destroy();
                }
            });
        }
    },
    beforeUnmount() {
        const historyModal = document.getElementById('transferInventoryHistoryModal');
        if (historyModal) {
            historyModal.removeEventListener('shown.bs.modal', this.onHistoryModalShow);
        }
    },
    methods: {
        fetchBranches() {
            axios.get('/api/branches')
                .then(response => {
                    this.branches = response.data;
                })
                .catch(error => {
                    console.error('There was an error fetching branches:', error);
                });
        },

        deleteItem(index) {
            this.items.splice(index, 1);
            localStorage.setItem('transferItems', JSON.stringify(this.items));
        },

        selectAnotherStore(target) {
            const availableBranches = this.branches.filter(
                branch => branch.id !== this[target]
            );

            if (availableBranches.length > 0) {
                this[target] = availableBranches[0].id;
            }
        },
        // updateItems(updatedItems) {
        //     localStorage.setItem('transferItems', JSON.stringify(updatedItems));
        //     this.items = updatedItems;
        // },
        itemScanned(scanned_barcode) {
            this.itemToBeAdd.manufacture_barcode = scanned_barcode;
            this.transferItem(this.itemToBeAdd);
            this.itemToBeAdd = {};
        },
        transferItem(item, forceAdd = false) {
            if (!item.manufacture_barcode) {
                this.itemToBeAdd = item;

                const addManufactureBarcodeModal = document.getElementById('addManufactureBarcodeModal');
                new bootstrap.Modal(addManufactureBarcodeModal).show();
                return;
            }

            let products = [];
            if (localStorage.getItem('transferItems')) {
                products = JSON.parse(localStorage.getItem('transferItems'));
            }

            const totalQuantity = products
                .filter(product => product.barcode === item.barcode)
                .reduce((sum, product) => sum + product.quantity, 0);

            if (totalQuantity + 1 > item.available_quantity && !forceAdd) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Product is out of stock. Do you still want to add this product?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Add Product',
                    cancelButtonText: 'No, Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.transferItem(item, true);
                    }
                });

                if(!this.forceAdd){
                    return;
                }
            }

            let highestSno = products.length > 0 ? Math.max(...products.map(product => product.sno)) : 0;
            item.quantity = 1; 
            item.scanned_barcode = item.scanned_barcode || item.manufacture_barcode; 
            item.sno = highestSno + 1;
            products.push(item);
            products.sort((a, b) => b.sno - a.sno);

            localStorage.setItem('transferItems', JSON.stringify(products));

            this.items = products;
        },
        onModalShow() {
            this.toStore = ''
        },
        initializeDataTable() {
           // this.table = $('#tranfer-history-table').DataTable();
        },
        async transferInventory() {
            const response = await axios.post('/transfer-inventory', {
                items: this.items,
                fromStore: this.fromStore,
                toStore: this.toStore
            });

            if (response.data.success) {
                this.items = [];
                localStorage.removeItem('transferItems');
                $('#transferInventoryModal').modal('hide');
                $('.modal-backdrop').remove();

                Swal.fire({
                    title: 'Success!',
                    text: response.data.message,
                    icon: 'success',
                    confirmButtonText: 'Great!'
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Something wnt wrong',
                    icon: 'error',
                    confirmButtonText: 'Okay'
                });
            }
        },
        async onHistoryModalShow() {
            try {
                // Destroy existing DataTable if it exists
                if ($.fn.DataTable.isDataTable('#tranfer-history-table')) {
                    this.table.destroy();
                }
                
                const response = await axios.get('/inventory-transfer-history');
                this.inventoryHistory = response.data;
                
                // Initialize DataTable after Vue has updated the DOM
                this.$nextTick(() => {
                    this.table = $('#tranfer-history-table').DataTable({
                        responsive: true,
                        destroy: true // Allows reinitialization
                    });
                });
            } catch (error) {
                console.error('Failed to fetch inventory history:', error);
            }
        },
        formatDate(dateStr) {
            const date = new Date(dateStr);
            
            // Get day, month, year components
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = String(date.getFullYear()).slice(-2); // Last 2 digits of year
            
            // Format time in 12-hour format with AM/PM
            let hours = date.getHours();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // Convert 0 to 12
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            
            return `${day}-${month}-${year} ${hours}:${minutes}:${seconds} ${ampm}`;
        }
    }
};
</script>
