<template>
    <div class="search-box mb-2">
        <div class="position-relative">
            <input type="number" v-model="query" class="form-control" placeholder="Enter barcode" @input="addToCart">
            <i class="bx bx-barcode search-icon"></i>
        </div>
    </div>
</template>

<script>
export default {
    props: ['availableProducts'],
    data() {
        return {
            query: '',
        };
    },
    methods: {
        addToCart() {
            if(this.query.toString().length === 13){
                this.query = '';
            }
        },
    },
};
</script>

<style scoped>
/* Hide number input arrows */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield; /* Firefox */
}
</style>
