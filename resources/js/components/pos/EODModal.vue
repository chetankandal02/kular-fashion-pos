<template>
  <div class="modal fade" id="eodModal" tabindex="-1" aria-labelledby="eodModalLabel">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="eodModalLabel">EOD</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="text-center">
            <h6>{{ currentStoreName }}</h6>
            <h6 v-html="nl2br(orderReceiptHeader)"></h6>
            <h6>Phone Number: 445454</h6>
            <h6>Email: asdas@sds.dd</h6>
            <h6>{{ currentDate }}</h6>
            <div class="border-divider mb-2"></div>
            <h5>SALE OF GOODS</h5>
          </div>
          <div class="text-left">
            <div class="row">
              <div class="col-9 ps-4">Sale Items</div>
              <div class="col-3">{{ salesData.saleItems }}</div>
            </div>
            <div class="row">
              <div class="col-9 ps-4">Sale Return Items</div>
              <div class="col-3">{{ salesData.saleReturns }}</div>
            </div>
            <div class="row">
              <div class="col-9 ps-4">Misc Sales</div>
              <div class="col-3">£{{ salesData.miscSales }}</div>
            </div>
            <div class="row">
              <div class="col-9 ps-4">Misc Returns</div>
              <div class="col-3">£{{ salesData.miscReturns }}</div>
            </div>
          </div>
          <div class="text-center">
            <div class="border-divider mb-2 mt-1"></div>
            <h5>Vouchers Exchange</h5>
          </div>
          <div class="text-left">
            <div class="row">
              <div class="col-9 ps-4">Gift Vouchers Sold</div>
              <div class="col-3">{{ salesData.giftVouchersSold }}</div>
            </div>
            <div class="row">
              <div class="col-9 ps-4">Gift Vouchers Redeemed</div>
              <div class="col-3">£{{ salesData.giftVouchersRedeemed }}</div>
            </div>
            <div class="row">
              <div class="col-9 ps-4">Credit Notes Issued</div>
              <div class="col-3">{{ salesData.creditNotesIssued }}</div>
            </div>
            <div class="row">
              <div class="col-9 ps-4">Credit Notes Redeemed</div>
              <div class="col-3">£{{ salesData.creditNotesRedeemed }}</div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success"><i class="mdi mdi-printer font-size-14 me-1" @click="handlePrint"></i>Print</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      currentStoreName: window.config.currentStoreName,
      orderReceiptHeader: window.config.orderReceiptHeader,
      currentDate: this.formatDate(new Date()),
      salesData: {
        saleItems: 0,
        saleReturns: 0,
        miscSales: 0,
        miscReturns: 0,
        giftVouchersSold: 0,
        giftVouchersRedeemed: 0,
        creditNotesIssued: 0,
        creditNotesRedeemed: 0
      },
      salesPersonId: window.config.userId || 0,
    };
  },
  methods: {
    formatDate(date) {
      return `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')} ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${date.getSeconds().toString().padStart(2, '0')}`;
    },
    async fetchSalesData() {
        const response = await axios.post('api/sales/today', { salesPersonId: this.salesPersonId });
        this.salesData = response.data.totals;
    },
    nl2br(text) {
      return text.replace(/\n/g, '<br>');
    },
    handlePrint() {
      this.printEod();
    },
    async printEod() {
      try {
        const response = await axios.post('/print-eod', { salesPersonId: this.salesPersonId });
        console.log('EOD printed successfully', response.data);
      } catch (error) {
        console.error('Error printing EOD', error);
      }
    },
  },
  created() {
    this.fetchSalesData();
  }
};
</script>