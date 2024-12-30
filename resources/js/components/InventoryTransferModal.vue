<template>
    <div class="modal fade" id="transferInventoryModal" tabindex="-1" aria-labelledby="transferInventoryModalLabel" aria-hidden="true" @shown.bs.modal="onModalShow">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferInventoryModalLabel">Transfer Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-3">
                            <label>From</label>
                            <select class="form-control" v-model="fromStore" disabled>
                                <option disabled value="">Select a store</option>
                                <option v-for="(branch, index) in branches" :key="index" :value="branch.id">
                                    {{ branch.name }}
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-3">
                            <label>To</label>
                            <select class="form-control" v-model="toStore" :disabled="!fromStore">
                                <option disabled value="">Select a store</option>
                                <option v-for="(branch, index) in branches" :key="index" :value="branch.id" :disabled="branch.id === fromStore">
                                    {{ branch.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <BarCodeBox :item-to-be-add="itemToBeAdd" @transfer-item="transferItem" />
                        </div>
                    </div>
                        <AddManufactureBarcodeModal :item="itemToBeAdd" @item-scanned="itemScanned" />

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="align-middle">Article Code</th>
                                <th class="align-middle">Description</th>
                                <th class="align-middle">Color</th>
                                <th class="align-middle">Size</th>
                                <th class="align-middle">Brand</th>
                                <th class="align-middle">Price</th>
                                <th class="align-middle">Quantity</th>
                                <th class="align-middle">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items" :key="item.code">
                                <td>{{ item.code }}</td>
                                <td>{{ item.description }}</td>
                                <td>{{ item.color }}</td>
                                <td>{{ item.size }}</td>
                                <td>{{ item.brand }}</td>
                                <td>{{ item.price }}</td>
                                <td>{{ item.quantity }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm" @click="deleteItem(index)">
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
        return {
            branches: [],
            fromStore: window.config.currentUserStore,
            toStore: '',
            itemToBeAdd: {},
            items: localStorage.getItem('transferItems') ? JSON.parse(localStorage.getItem('transferItems')) : []
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
        itemScanned(scanned_barcode){
            this.itemToBeAdd.manufacture_barcode = scanned_barcode;
            this.transferItem(this.itemToBeAdd);
            this.itemToBeAdd = {};
        },
        transferItem(item) {
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

            const existingProductIndex = products.findIndex(product => product.barcode === item.barcode);

            if (existingProductIndex !== -1) {                
                if (products[existingProductIndex].quantity < 1) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Item maximum quantity exceed',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    });
                    return;
                }
                products[existingProductIndex].quantity += 1;

            } else {
                if(item.available_quantity < 1){
                    Swal.fire({
                        title: 'Error!',
                        text: 'Item maximum quantity exceed',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    });
                    return;
                }

                item.quantity = 1;
                products.push(item);
            }

            localStorage.setItem('transferItems', JSON.stringify(products));

            this.items = products;
        },

        onModalShow() {
            this.toStore = ''; // Reset 'To' store when the modal is shown
        }
    }
};
</script>
