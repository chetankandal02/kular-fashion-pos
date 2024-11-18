<template>
    <div class="search-box mb-2">
        <div class="position-relative">
            <input type="text" v-model="query" class="form-control" placeholder="Enter barcode" @input="addToCart">
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
            if(this.query.length === 13){
                this.query = '';
            }
        },
    },
};
</script>

<style scoped></style>
