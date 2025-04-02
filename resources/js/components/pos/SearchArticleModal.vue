<template>
  <div class="modal fade" id="searchArticalModal" tabindex="-1" aria-labelledby="searchArticalModalLabel">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="searchArticalModalLabel">Search Article</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-1">
          <div class="row">
            <div class="col-md-3 mb-1">
              <label for="search_by_brand" class="mb-0">Brand</label>
              <select id="search_by_brand" class="form-control">
                <option value="">Select brand</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                  {{ brand.name }}
                </option>
              </select>
            </div>
            <div class="col-md-3 mb-1">
              <label for="search_by_product_type" class="mb-0">Product Type</label>
              <select id="search_by_product_type" class="form-control">
                <option value="">Select Product Type</option>
                <option v-for="productType in productTypes" :key="productType.id" :value="productType.id">
                  {{ productType.name }}
                </option>
              </select>
            </div>
            <div class="col-md-3 mb-1">
              <label for="search_product" class="mb-0">Search</label>
              <input type="text" id="search_product" class="form-control">
            </div>
          </div>

          <table class="table table-striped table-bordered w-100 table-sm" id="search-article-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Article Code</th>
                <th>Manufacture Code</th>
                <th>Brand</th>
                <th>Product Type</th>
                <th>Department</th>
                <th>Short Description</th>
                <th>Price</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="sizeSelectionModal" tabindex="-1" aria-labelledby="sizeSelectionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="sizeSelectionModalLabel">Select Size</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div v-if="selectedProduct && selectedProduct.sizes">
            <div class="row">
              <div v-for="size in selectedProduct.sizes" :key="size.id" class="col-md-2 text-center">
                <div class="size-box mb-3" :class="{ selected: size.id === selectedSize }"
                  @click="selectedSize = size.id">{{
                    size.size_detail.size }}</div>
              </div>
            </div>
          </div>
          <button class="btn btn-primary" @click="confirmSizeSelection" :disabled="!selectedSize">Confirm
            Selection</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="colorSelectionModal" tabindex="-1" aria-labelledby="colorSelectionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="colorSelectionModalLabel">Select Color</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div v-if="selectedProduct && selectedProduct.colors">
            <div class="row">
              <div v-for="color in selectedProduct.colors" :key="color.id" class="col-md-3 text-center">
                <div class="color-box d-block m-auto" :class="{ selected: color.id === selectedColor }"
                  @click="selectedColor = color.id"
                  v-bind:style="{ backgroundColor: color.color_detail.ui_color_code }"></div>
                <label class="form-check-label" :for="color">{{ color.color_detail.name }}</label>
              </div>
            </div>
          </div>
          <button class="btn btn-primary mt-3" @click="confirmColorSelection" :disabled="!selectedColor">Confirm
            Selection</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="stocksDetailModal" tabindex="-1" aria-labelledby="stocksDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="stocksDetailModalLabel">Stocks Details</h5>
          <div>
            <button type="button" class="btn btn-primary btn-sm inventory-change"
              @click="storeWiseInventory = !storeWiseInventory">{{ !storeWiseInventory ? 'Store' : 'Color' }} wise
              Inventory</button>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
        </div>
        <div class="modal-body">
          <div v-if="stocksDetail && stocksDetail.product">
            <div v-if="storeWiseInventory">
              <div class="mb-2" v-for="(branch, index) in stocksDetail.branches" :key="index">
                <div>
                  <div class="text-center">
                    <h5 class="mb-0"><strong>{{ branch.name }}</strong></h5>
                  </div>

                  <div class="table-responsive">
                    <table class="table mb-0 table-bordered">
                      <tbody>
                        <tr>
                          <th scope="row" class="p-1">Size</th>
                          <th class="p-1" v-for="(size, index) in stocksDetail.product.sizes" :key="index">{{
                            size.size_detail.size }}</th>
                        </tr>

                        <tr v-for="(color, index) in stocksDetail.product.colors" :key="index">
                          <th class="d-flex p-1">
                            <div class="me-1 d-color-code" :style="{ background: color.color_detail.ui_color_code }">
                            </div>
                            <h6 class="m-0">{{ color.color_detail.name }} ({{ color.color_detail.code }})</h6>
                          </th>
                          <td class="p-1" v-for="(size, index) in stocksDetail.product.sizes" :key="index">
                            <div v-if="branch.id === 1">
                              {{stocksDetail.product.quantities.find(item => item.product_size_id === size.id &&
                                item.product_color_id === color.id)?.quantity || 0}}
                            </div>
                            <div v-else>
                              {{branch.inventory.find(item => item.product_size_id === size.id &&
                                item.product_color_id === color.id)?.quantity || 0}}
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="table-responsive mb-4">
                <div class="text-center">
                  <h5 class="text-success mb-0"><strong>Goods In</strong></h5>
                </div>

                <table class="table mb-0 table-bordered">
                  <tbody>
                    <tr>
                      <th scope="row" class="p-1">Size</th>
                      <th class="p-1" v-for="(size, index) in stocksDetail.product.sizes" :key="index">{{
                        size.size_detail.size }}</th>
                    </tr>

                    <tr v-for="(color, index) in stocksDetail.product.colors" :key="index">
                      <th class="d-flex p-1">
                        <div class="me-1 d-color-code" :style="{ background: color.color_detail.ui_color_code }">
                        </div>
                        <h6 class="m-0">{{ color.color_detail.name }} ({{ color.color_detail.code }})</h6>
                      </th>
                      <td class="p-1" v-for="(size, index) in stocksDetail.product.sizes" :key="index">
                        {{stocksDetail.product.quantities.find(item => item.product_size_id === size.id &&
                          item.product_color_id === color.id)?.total_quantity || 0}}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div v-else>
              <div class="mb-2" v-for="(color, index) in stocksDetail.product.colors" :key="index">
                <div>
                  <div class="d-flex justify-content-center align-items-center mt-2">
                    <div class="me-1 d-color-code" :style="{ background: color.color_detail.ui_color_code }"></div>
                    <h5 class="m-0">{{ color.color_detail.name }} ({{ color.color_detail.code }})</h5>
                  </div>

                  <div class="table-responsive">
                    <table class="table mb-0 table-bordered">
                      <tbody>
                        <tr>
                          <th scope="row" class="p-1">Size</th>
                          <th class="p-1" v-for="(size, index) in stocksDetail.product.sizes" :key="index">{{
                            size.size_detail.size }}</th>
                        </tr>

                        <tr v-for="(branch, index) in stocksDetail.branches" :key="index">
                          <th class="d-flex p-1">
                            <h6 class="m-0">{{ branch.name }}</h6>
                          </th>
                          <td class="p-1" v-for="(size, index) in stocksDetail.product.sizes" :key="index">
                            <div v-if="branch.id === 1">
                              {{stocksDetail.product.quantities.find(item => item.product_size_id === size.id &&
                                item.product_color_id === color.id)?.quantity || 0}}
                            </div>
                            <div v-else>
                              {{branch.inventory.find(item => item.product_size_id === size.id &&
                                item.product_color_id === color.id)?.quantity || 0}}
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <th class="d-flex p-1">
                            <h6 class="m-0 text-success">Goods In</h6>
                          </th>
                          <td class="p-1" v-for="(size, index) in stocksDetail.product.sizes" :key="index">
                            {{stocksDetail.product.quantities.find(item => item.product_size_id === size.id &&
                              item.product_color_id === color.id)?.total_quantity || 0}}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { EventBus } from '@/eventBus';

export default {
  props: {
    brands: {
      type: Array,
      required: true
    },
    productTypes: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      table: null,
      selectedProduct: null,
      selectedSize: null,
      selectedColor: null,
      stocksDetail: null,
      storeWiseInventory: true
    };
  },
  methods: {
    async confirmColorSelection() {
      $('#colorSelectionModal').modal('hide');

      const response = await axios.post('/api/quick-validate-item', {
        id: this.selectedProduct.id,
        sizeId: this.selectedSize,
        colorId: this.selectedColor,
        userId: window.config.userId
      });

      if (response.data.success) {
        const article = response.data.product;

        if (article.available_quantity === 0) {
          Swal.fire({
            title: 'Warning!',
            text: 'Product is out of stock. Do you still want to add this product?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Add Product',
            cancelButtonText: 'No, Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              EventBus.quickAddArticle = {
                article: article,
                timestamp: Date.now(),
              };
            }
          });
        } else {
          EventBus.quickAddArticle = {
            article: article,
            timestamp: Date.now(),
          };
        }
      } else {
        Swal.fire({
          title: 'Error!',
          text: response.data.message,
          icon: 'error',
          confirmButtonText: 'Okay'
        });
      }
    },
    confirmSizeSelection() {
      $('#sizeSelectionModal').modal('hide');
      $('#colorSelectionModal').modal('show');
    },
    initializeDataTable() {
      $('#search_by_product_type').chosen({
        width: "100%"
      });

      const vm = this;

      this.table = $('#search-article-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 20,
        lengthChange: false,
        searching: false,
        ajax: {
          url: '/get-products',
          data: (d) => {
            d.brand_id = $('#search_by_brand').val();
            d.product_type_id = $('#search_by_product_type').val();
            d.search['value'] = $('#search_product').val();
            d.page = Math.floor(d.start / d.length) + 1;
            // d.order = d.order;
          },
          error: function (xhr, error, thrown) {
            console.error('Error fetching data:', error);
            console.error('Response:', xhr.responseText);
          }
        },
        columns: [
          {
            title: '#',
            data: 'id',
          },
          {
            title: 'Article Code',
            data: 'article_code',
          },
          {
            title: 'Manufacture Code',
            data: 'manufacture_code',
          },
          {
            title: 'Brand',
            data: 'brand.name',
          },
          {
            title: 'Product Type',
            data: 'product_type.name',
          },
          {
            title: 'Department',
            data: 'department.name',
          },
          {
            title: 'Short Description',
            data: 'short_description',
          },
          {
            title: 'Price',
            data: 'price',
          },
          {
            title: 'Action',
            data: null,
            render: function (data, type, row) {
              let tempArticle = {
                id: row.id,
                colors: row.colors,
                sizes: row.sizes,
              };

              return `
                <button class="btn btn-primary btn-sm py-0 px-1 pick-product-for-sale" data-product='${JSON.stringify(tempArticle)}'>
                  <i class="fas fa-hand-holding-usd"></i>
                </button>
                <button class="btn btn-primary btn-sm py-0 px-1 view-stock" data-product-id="${row.id}">
                  <i class="fas fa-eye"></i>
                </button>
              `;
            },
          },
        ],
        order: [[2, 'asc']],
        drawCallback: function () {
          $('.pick-product-for-sale').on('click', (event) => {
            const product = JSON.parse($(event.currentTarget).attr('data-product'));
            vm.pickProduct(product);
          });

          $('.view-stock').on('click', (event) => {
            const productId = $(event.currentTarget).attr('data-product-id');
            vm.viewStocks(productId);
          });
        },
      });
    },
    pickProduct(product) {
      this.selectedSize = null;
      this.selectedColor = null;
      this.selectedProduct = product;
      $('#searchArticalModal').modal('hide');
      $('#sizeSelectionModal').modal('show');
    },
    async viewStocks(productId) {
      const reorderBranches = (branches) => {
        let defaultBranchId = window.config.currentUserStore || 1;

        const defaultBranch = branches.find(branch => branch.id === defaultBranchId);
        const otherBranches = branches.filter(branch => branch.id !== defaultBranchId);
        return [defaultBranch, ...otherBranches];
      };

      const stocks = await axios.get(`/api/product-stocks/${productId}`);

      this.stocksDetail = {
        branches: reorderBranches(stocks.data.branches),
        product: stocks.data.product
      }

      $('#stocksDetailModal').modal('show');
    },
    reloadDataTable() {
      if (this.table) {
        this.table.ajax.reload();
      }
    },
  },
  mounted() {
    $('#search_by_brand').chosen({
      width: "100%"
    });

    $('#search_by_product_type').chosen({
      width: "100%"
    });

    $('#search_by_brand').on('change', () => {
      this.reloadDataTable();
    });

    $('#search_product').on('keyup', () => {
      this.reloadDataTable();
    });

    $('#search_by_product_type').on('change', () => {
      this.reloadDataTable();
    });

    this.initializeDataTable();
  }
};
</script>