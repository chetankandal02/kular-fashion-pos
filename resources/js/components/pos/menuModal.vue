<template>
    <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalLabel">Menu</h5>
                    <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#eodModal">
                                <i class="mdi mdi-cash-multiple font-size-14 me-1"></i>
                                Till Now
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#eodModal">
                                <i class="mdi mdi-printer font-size-14 me-1"></i>
                                EOD
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <EODModal />
</template>

<script>
import EODModal from './EODModal.vue';

export default {
    components: {
        EODModal
    },
    data() {
        return {

        };
    },
    methods: {
        closeModal() {
            const modal = bootstrap.Modal.getInstance($('#menuModal'));
            modal.hide();
        },
    }
};
</script>