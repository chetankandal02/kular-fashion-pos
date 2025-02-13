<template>
    <div class="search-box mt-3">
        <VirtualNumberKeyboard ref="virtualKeyboard" variant="barcode" :inputValue="query" @on-change="validateItem" />
    </div>
</template>

<script>
import axios from 'axios';
import VirtualNumberKeyboard from '../VirtualNumberKeyboard.vue';

export default {
    data() {
        return {
            query: '',
        };
    },
    components: {
        VirtualNumberKeyboard
    },
    mounted() {
        this.$refs.virtualKeyboard.focusInput();
    },
    methods: {
        async validateItem(input) {
            if (input.length === 13) {
                const barcode = input;

                const response = await axios.get(`/validate-item/${barcode}`);
                const { product } = response.data;
                if (product) {
                    if(product.available_quantity > 0){
                        this.$emit('add-to-cart', product);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Product is out of stock. Do you still want to add this project?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Add Project',
                            cancelButtonText: 'No, Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.$emit('add-to-cart', product);
                            }
                        });
                    }
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Product Barcode is invalid.',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    });
                }
            } else {
                this.query = input;
            }
        },
    },
};
</script>