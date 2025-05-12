<template>
  <div class="modal fade" id="eodModal" tabindex="-1" aria-labelledby="eodModalLabel">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="eodModalLabel">EOD</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Show if no data -->
          <div v-if="isDataEmpty" class="text-center text-muted py-4">
            <p>No sales data found for today.</p>
          </div>

          <!-- Show actual content -->
          <div v-else>
            <div class="text-center">
              <h6>{{ currentStoreName }}</h6>
              <h6 v-html="nl2br(orderReceiptHeader)"></h6>
              <h6 v-if="storeDetail.contact">Phone Number: {{ storeDetail.contact }}</h6>
              <h6 v-if="storeDetail.email">Email: {{ storeDetail.email }}</h6>
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

              <div class="text-center">
                <div class="border-divider mb-2"></div>
                <h5>Payments History</h5>
              </div>

              <div v-if="totalsByMethod.length > 0">
                <div class="row" v-for="(method, index) in totalsByMethod" :key="index">
                  <div class="col-9 ps-4">{{ method.method }}</div>
                  <div class="col-3">£{{ method.total_amount }}</div>
                </div>
              </div>

             
            </div>

            <div class="text-center">
              <div class="border-divider mb-2 mt-3"></div>
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
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" @click="handlePrint">
            <i class="mdi mdi-printer font-size-14 me-1"></i>
            Print
          </button>
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
      storeDetail: window.config.storeDetail,
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
      totalsByMethod: [],
      salesPersonId: window.config.userId || 0,

      // New Payment
      newPayment: {
        method: '',
        amount: null
      }
    };
  },
  methods: {
    formatDate(date) {
      return `${date.getDate().toString().padStart(2, '0')}-${(date.getMonth() + 1)
        .toString()
        .padStart(2, '0')}-${date.getFullYear()} ${date
        .getHours()
        .toString()
        .padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${date
        .getSeconds()
        .toString()
        .padStart(2, '0')}`;
    },
    async fetchSalesData() {
      const response = await axios.post('api/sales/today', {
        salesPersonId: Number(this.salesPersonId)
      });
      this.salesData = response.data.totals;
      this.totalsByMethod = response.data.totalsByMethod;
    },
    nl2br(text) {
      return text?.replace(/\n/g, '<br>');
    },
    handlePrint() {
      this.printEod();
    },
    async printEod() {
      try {
        const response = await axios.post('/print-eod', {
          salesPersonId: this.salesPersonId
        });
        console.log('EOD printed successfully', response.data);
      } catch (error) {
        console.error('Error printing EOD', error);
      }
    },
    async addPayment() {
      if (!this.newPayment.method || !this.newPayment.amount) {
        alert('Please enter both method and amount');
        return;
      }

      try {
        await axios.post('/api/payment/add', {
          method: this.newPayment.method,
          amount: this.newPayment.amount,
          salesPersonId: this.salesPersonId
        });

        // Clear input
        this.newPayment.method = '';
        this.newPayment.amount = null;

        // Refresh data
        await this.fetchSalesData();
      } catch (error) {
        console.error('Error adding payment:', error);
      }
    }
  },
  computed: {
    isDataEmpty() {
      const d = this.salesData;
      return (
        d.saleItems === 0 &&
        d.saleReturns === 0 &&
        d.miscSales === 0 &&
        d.miscReturns === 0 &&
        d.giftVouchersSold === 0 &&
        d.giftVouchersRedeemed === 0 &&
        d.creditNotesIssued === 0 &&
        d.creditNotesRedeemed === 0 &&
        this.totalsByMethod.length === 0
      );
    }
  },
  created() {
    this.fetchSalesData();
  },
  mounted() {
    // Refresh data every time the modal is opened
    const eodModal = document.getElementById('eodModal');
    if (eodModal) {
      eodModal.addEventListener('shown.bs.modal', () => {
        this.fetchSalesData();
      });
    }
  }
};
</script>
