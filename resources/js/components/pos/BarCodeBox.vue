<template>
    <div class="search-box mb-2">
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
                    this.$emit('add-to-cart', product);
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