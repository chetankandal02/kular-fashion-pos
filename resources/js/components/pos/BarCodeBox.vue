<template>
    <div class="search-box mb-2">
        <div class="position-relative">
            <input type="number" v-model="query" class="form-control" placeholder="Enter barcode" @input="addToCart">
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
            if(this.query.toString().length === 13){
                const barcode = this.query;
                this.query = '';
                const response = await axios.get(`/product-validate/${barcode}`);
                const {product} = response.data;
                if (product) {
                    console.log(this.$parent.orderItems);
                    const existingProduct = this.$parent.orderItems.find(
                        item => item.code === product.code && item.size === product.size && item.color === product.color
                    );
                    if (existingProduct) {
                        console.log(existingProduct);
                        Swal.fire({
                            title: 'Product Already Exists!',
                            text: `The product ${product.code} is already in sale.`,
                            icon: 'info',
                            confirmButtonText: 'Okay'
                        });
                    } else {
                        this.$emit('add-to-cart', product);
                    }
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