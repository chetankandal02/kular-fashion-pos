<template>
    <div class="modal fade" id="transferInventoryModal" tabindex="-1"
        aria-labelledby="transferInventoryModalLabel" aria-hidden="true" @shown.bs.modal="onModalShow">
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
                                <th>#</th>
                                <th>Article Code</th>
                                <th>Description</th>
                                <th>Color</th>
                                <th>Size</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items" :key="item.code">
                                <td>{{ items.length - index }}</td>
                                <td>{{ item.code }}</td>
                                <td>{{ item.description }}</td>
                                <td>{{ item.color }}</td>
                                <td>{{ item.size }}</td>
                                <td>{{ item.brand }}</td>
                                <td>{{ item.price }}</td>
                                <td>{{ item.quantity }}</td>
                                <td>
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
    <AddManufactureBarcodeModal :item="itemToBeAdd" @item-scanned="itemScanned" />
</template>

<script>
import BarCodeBox from '../components/inventory-transfer/BarCodeBox.vue';
import AddManufactureBarcodeModal from '../components/inventory-transfer/AddManufactureBarcodeModal.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    name: 'TransferInventoryModal',
    components: {
        BarCodeBox,
        AddManufactureBarcodeModal
    },
    data() {
        const storedItems = JSON.parse(localStorage.getItem('transferItems') || '[]');
        const sortedItems = storedItems.sort((a, b) => b.sno - a.sno);
        return {
            branches: [],
            fromStore: window.config.currentUserStore,
            toStore: '',
            itemToBeAdd: {},
            items: sortedItems
        };
    },
    created() {
        this.fetchBranches();
    },
    watch: {
        fromStore(newVal) {
            if (newVal === this.toStore) this.selectAnotherStore('toStore');
        },
        toStore(newVal) {
            if (newVal === this.fromStore) this.selectAnotherStore('fromStore');
        }
    },
    methods: {
        fetchBranches() {
            axios.get('/api/branches')
                .then(response => this.branches = response.data)
                .catch(err => console.error('Error fetching branches:', err));
        },
        deleteItem(index) {
            this.items.splice(index, 1);
            localStorage.setItem('transferItems', JSON.stringify(this.items));
        },
        itemScanned(barcode) {
            this.itemToBeAdd.manufacture_barcode = barcode;
            this.transferItem(this.itemToBeAdd);
            this.itemToBeAdd = {};
        },
        transferItem(item, forceAdd = false) {
            if (!item.manufacture_barcode) {
                this.itemToBeAdd = item;
                const modal = new bootstrap.Modal(document.getElementById('addManufactureBarcodeModal'));
                modal.show();
                return;
            }

            let products = JSON.parse(localStorage.getItem('transferItems') || '[]');
            const totalQty = products.filter(p => p.barcode === item.barcode).reduce((a, b) => a + b.quantity, 0);

            if (totalQty + 1 > item.available_quantity && !forceAdd) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Out of stock. Add anyway?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then(res => {
                    if (res.isConfirmed) this.transferItem(item, true);
                });
                return;
            }

            item.quantity = 1;
            item.scanned_barcode = item.manufacture_barcode;
            item.sno = products.length > 0 ? Math.max(...products.map(p => p.sno)) + 1 : 1;
            products.push(item);
            products.sort((a, b) => b.sno - a.sno);
            localStorage.setItem('transferItems', JSON.stringify(products));
            this.items = products;
        },
        async transferInventory() {
            try {
                const res = await axios.post('/transfer-inventory', {
                    items: this.items,
                    fromStore: this.fromStore,
                    toStore: this.toStore
                });

                if (res.data.success) {
                    this.items = [];
                    localStorage.removeItem('transferItems');
                    $('#transferInventoryModal').modal('hide');
                    $('.modal-backdrop').remove();

                    Swal.fire('Success!', res.data.message, 'success');
                }
            } catch {
                Swal.fire('Error', 'Transfer failed', 'error');
            }
        },
        onModalShow() {
            this.toStore = '';
        },
        selectAnotherStore(target) {
            const filtered = this.branches.filter(b => b.id !== this[target]);
            if (filtered.length) this[target] = filtered[0].id;
        }
    }
};
</script>
