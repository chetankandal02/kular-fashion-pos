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
                        <tr v-for="item in orderItems" :key="item.code">
                            <td><a href="javascript: void(0);" class="text-body fw-bold">{{ item.code }}</a></td>
                            <td>{{ item.description }}</td>
                            <td>{{ item.color }}</td>
                            <td>{{ item.size }}</td>
                            <td>{{ item.brand }}</td>
                            <td>{{ item.price }}</td>
                            <td>
                                <button @click="editItemPriceModal(item)" type="button"
                                    class="btn btn-sm btn-primary waves-effect waves-light me-2">
                                    <i class="bx bx-edit fs-5"></i>
                                </button>
                                <button @click="removeItem(item)" type="button"
                                    class="btn btn-sm btn-danger waves-effect waves-light">
                                    <i class="bx bx-trash-alt fs-5"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <EditItemPriceModal :selectedItem="selectedItem" @save-price-change="savePriceChange" />
</template>

<script>
import EditItemPriceModal from './EditItemPriceModal.vue';

export default {
    props: {
        orderItems: {
            type: Array,
            required: true
        }
    },
    emits: ['remove-from-cart'],
    components: {
        EditItemPriceModal
    },
    data() {
        return {
            selectedItem: null
        };
    },
    methods: {
        editItemPriceModal(item) {
            this.selectedItem = { ...item };
            const modal = new bootstrap.Modal($('#editPriceModal'));
            modal.show();
        },
        savePriceChange(updatedItem) {
            const index = this.orderItems.findIndex(item => item.code === updatedItem.code);
            if (index !== -1) {
                this.orderItems[index].price = updatedItem.price;
                this.orderItems[index].reason = updatedItem.reason;
            }
        },
        removeItem(item) {
            this.$emit('remove-from-cart', item);
        },
    },
};
</script>