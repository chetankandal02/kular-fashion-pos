<template>
    <div class="search-box mb-2">
        <div class="position-relative">
            <input type="number" v-model="query" class="form-control" placeholder="Scan barcode" autofocus
                @input="addToCart">
            <i class="bx bx-barcode search-icon"></i>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            query: '',
        };
    },
    methods: {
        async addToCart() {
            if (this.query.toString().length === 13) {
                const barcode = this.query;
                this.query = '';
                const response = await axios.get(`/validate-item/${barcode}`);
                const { product } = response.data;
                if (product) {
                    this.$emit('add-to-cart', product);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Product Barcode is invalid.',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    });
                }
            }
        },
    },
};
</script>