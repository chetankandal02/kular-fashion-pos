<template>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Order Items</h4>
            <div class="table-responsive">
                <table class="table align-middle table-nowrap mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="align-middle">Item Code</th>
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
                                <button @click="removeItem(item)" type="button"
                                    class="btn btn-primary btn-danger waves-effect waves-light">
                                    <i class="bx bx-trash-alt fs-5"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            orderItems: [
                { code: '#SK2540', description: 'Neal Matthews', color: 'Grey', size: 'S', brand: 'Puma', price: 45 },
                { code: '#SK2541', description: 'Jamal Burnett', color: 'Black', size: 'L', brand: 'Nike', price: 470 },
                // Add more order items here
            ],
        };
    },
    methods: {
        removeItem(item) {
            this.orderItems = this.orderItems.filter(i => i !== item);
        },
    },
};
</script>