<template>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Order Items</h4>
            <div class="table-responsive">
                <table class="table align-middle table-nowrap mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="align-middle">Article Code</th>
                            <th class="align-middle">Description</th>
                            <th class="align-middle">Color</th>
                            <th class="align-middle">Size</th>
                            <th class="align-middle">Brand</th>
                            <th class="align-middle">Price</th>
                            <th class="align-middle">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in orderItems" :key="item.code + item.size">
                            <td><a href="javascript: void(0);" class="text-body fw-bold">{{ item.code }}</a></td>
                            <td>{{ item.description }}</td>
                            <td>{{ item.color }}</td>
                            <td>{{ item.size }}</td>
                            <td>{{ item.brand }}</td>
                            <td>{{ formatPrice(item.changedPrice ? item.changedPrice.amount : item.price) }}</td>
                            <td>
                                <button
                                    @click="editItemPriceModal(item, index)"
                                    type="button"
                                    class="btn btn-sm btn-primary waves-effect waves-light me-2"
                                >
                                    <i class="bx bx-edit fs-5"></i>
                                </button>
                                <button
                                    @click="removeItem(index)"
                                    type="button"
                                    class="btn btn-sm btn-danger waves-effect waves-light"
                                >
                                    <i class="bx bx-trash-alt fs-5"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for editing item price -->
    <EditItemPriceModal :selected-item="selectedItem" @save-price-change="updateItemPrice" />
</template>

<script>
import EditItemPriceModal from './EditItemPriceModal.vue';

export default {
    components: {
        EditItemPriceModal
    },
    props: {
        orderItems: {
            type: Array
        }
    },
    emits: ['removeFromCart'],
    data(){
        return {
            selectedItem: null,
            selectedItemIndex: -1
        }
    },
    methods: {
        formatPrice(price) {
            if (!price) return '£0.00';
            return `£${parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
        },
        editItemPriceModal(item, index) {
            this.selectedItemIndex = index;
            this.selectedItem = { ...item };

            const modalElement = document.getElementById('editPriceModal');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
            modalInstance.show();
        },
        updateItemPrice(updatedItem) {
            if (this.selectedItemIndex > -1) {
                this.orderItems[this.selectedItemIndex] = updatedItem;
            }

            localStorage.setItem('orderItems', JSON.stringify(this.orderItems));
        },
        removeItem(item) {
            this.$emit('removeFromCart', item);
        }
    }
};
</script>
