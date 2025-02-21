<template>
    <div class="modal fade" id="transferInventoryModal" tabindex="-1" aria-labelledby="transferInventoryModalLabel"
        aria-hidden="true" @shown.bs.modal="onModalShow">
        <div class="modal-dialog modal-lg">
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
                        <div class="col-4">
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
                    text: 'Product is out of stock. Do you still want to add this project?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Add Project',
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
        }
    }
};
</script>
