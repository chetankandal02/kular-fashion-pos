<template>
    <div class="position-relative" ref="keyboardWrapper">
        <input type="text" v-model="query" class="form-control" :placeholder="placeholderText" @focus="showKeyboard"
            @input="validateInput" ref="input" @keydown.enter="emitAmount" :class="{'is-invalid' : invalidMessage}" />

        <i class="bx search-icon" :class="iconClass"></i>

        <span class="invalid-feedback">{{ invalidMessage }}</span>

        <div v-if="isVisible" class="virtual-keyboard" :class="{'invalid-input' : invalidMessage}">
            <div class="row mb-1">
                <div v-for="key in row1" class="col-4 px-2" :key="key">
                    <button class="btn btn-primary w-100" @click="addDigit(key)">{{ key }}</button>
                </div>
            </div>
            <div class="row mb-1">
                <div v-for="key in row2" class="col-4 px-2" :key="key">
                    <button class="btn btn-primary w-100" @click="addDigit(key)">{{ key }}</button>
                </div>
            </div>
            <div class="row mb-1">
                <div v-for="key in row3" class="col-4 px-2" :key="key">
                    <button class="btn btn-primary w-100" @click="addDigit(key)">{{ key }}</button>
                </div>
            </div>
            <div class="row mb-1">
                <div v-for="key in row4" class="col-4 px-2" :key="key">
                    <button class="btn btn-primary w-100" @click="addDigit(key)" v-if="key !== '.' && key !== '<-'">{{ key }}</button>
                    <button v-if="key === '.' && !String(query).includes('.') && variant !== 'barcode'"
                        class="btn btn-primary w-100 key" @click="addDigit(key)">{{ key }}</button>

                    <button v-if="key === '<-'" class="btn btn-primary w-100 key" @click="addDigit(key)">
                        <i class="mdi mdi-keyboard-backspace"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-6 px-2">
                    <button class="btn btn-danger w-100" @click="clearInput">Clear</button>
                </div>
                <div class="col-6 px-2">
                    <button class="btn btn-danger w-100" @click="closeKeyboard">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        inputValue: {
            type: String,
            default: ""
        },
        invalidMessage: {
            type: String,
            default: ""
        },
        variant: {
            type: String,
            default: "amount"
        }
    },
    data() {
        return {
            query: this.inputValue,
            isVisible: false,
            row1: ['1', '2', '3'],
            row2: ['4', '5', '6'],
            row3: ['7', '8', '9'],
            row4: ['0', '.', '<-'],
        };
    },
    watch: {
        query(newValue) {
            this.$emit('onChange', String(newValue));
            if(newValue.length===13 && this.variant=='barcode'){
                this.query = '';
            }
        },
        inputValue(newValue) {
            this.query = newValue;
        }
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeDestroy() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    computed: {
        placeholderText() {
            return this.variant === "amount" ? "Enter amount" : "Scan barcode";
        },
        iconClass() {
            return this.variant === "amount" ? "mdi mdi-currency-eur" : "bx bx-barcode";
        }
    },
    methods: {
        showKeyboard() {
            if(this.variant !== 'barcode'){
                this.isVisible = true;
            }
        },
        emitAmount(){
            this.$emit('onSubmit')
        },
        validateInput(event) {
            let inputValue = event.target.value;

            if (this.variant !== 'barcode') {
                inputValue = inputValue.replace(/[^0-9.]/g, ""); // Allow only digits and one dot

                const decimalCount = inputValue.split(".").length - 1;
                if (decimalCount > 1) {
                    inputValue = inputValue.replace(/\.(?=.*\.)/g, '');;
                }
            } else {
                inputValue = inputValue.replace(/[^0-9]/g, ""); // Allow only digits
            }

            this.query = inputValue;
        },
        closeKeyboard() {
            this.isVisible = false;
        },
        addDigit(key) {
            if (key === '<-') {
                this.query = this.query.slice(0, -1);
            } else {
                this.query += key;
            }
        },
        clearInput() {
            this.query = '';
        },
        handleClickOutside(event) {
            const keyboardWrapper = this.$refs.keyboardWrapper;
            if (keyboardWrapper && !keyboardWrapper.contains(event.target) && !$(event.target).hasClass('key')) {
                this.closeKeyboard();
            }
        },
        focusInput() {
            this.$refs.input.focus();
        }
    }
};
</script>

<style scoped>
.virtual-keyboard {
    padding: 10px;
    background: #f1f1f1;
    border: 1px solid #ccc;
    border-radius: 8px;
    width: 274px;
    position: absolute;
    top: 40px;
    left: 20px;
    z-index: 9999;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.virtual-keyboard.invalid-input{
    top: 58px;
}
</style>
