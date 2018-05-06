<template>

    <div class="row pagination-row" v-if="isLoaded">
        <div class="col-md-8 col-sm-7 col-12">
            <paginator
                    :pagination="pagination"
                    :numPageLinks="5"
                    :itemTypeName="itemTypeName"
                    :isCompact="isCompact"
                    :showTotals="showTotals"
                    v-on:pagerequest="pageRequest"
            ></paginator>
        </div>
        <div v-if="!isCompact" class="col-md-4 col-sm-5 col-12 text-right">
            <b-button-group class="search-buttons">
                <b-dropdown size="sm" left text="Sort">
                    <b-dropdown-item @click="orderRequest('newest')">Newest</b-dropdown-item>
                    <b-dropdown-item @click="orderRequest('oldest')">Oldest</b-dropdown-item>
                    <b-dropdown-item @click="orderRequest('best')">Best</b-dropdown-item>
                    <b-dropdown-item @click="orderRequest('worst')">Worst</b-dropdown-item>
                </b-dropdown>
                <b-button
                        size="sm"
                        @click="viewRequest('cards')"
                        v-bind:class="{ 'disabled' : view === 'cards' }">
                    <i class="fa fa-th"></i></b-button>
                <b-button
                        size="sm"
                        @click.prevent="viewRequest('details')"
                        v-bind:class="{ 'disabled' : view === 'details' }">
                    <i class="fa fa-list-ol"></i>
                </b-button>
                <!--
                <b-button
                        size="sm"
                        @click.prevent="viewRequest('rows')"
                        v-bind:class="{ 'disabled' : view === 'rows' }">
                    <i class="fa fa-table"></i>
                </b-button>
                -->
            </b-button-group>
        </div>
    </div>

</template>


<script>

    import Paginator from './Paginator.vue'

  export default {

    name: 'FilterPaginator',
    components: {
      Paginator
    },
    props: {
      pagination: {
        type: Object,
        default: null
      },
      view: {
        type: String,
        default: null
      },
      order: {
        type: String,
        default: null
      },
      itemTypeName: {
        type: String,
        default: null
      },
      isCompact: {
        type: Boolean,
        default: false
      },
      showTotals: {
        type: Boolean,
        default: false
      }
    },

    computed: {

      isLoaded () {
        if (this.pagination) {
          return true;
        }
        return false;
      }

    },

    methods: {
      pageRequest (page_num) {
        console.log('emit page request: ' + page_num);
        this.$emit('pagerequest', page_num);
      },

      orderRequest (order_type) {
        console.log('emit order request: ' + order_type);
        this.$emit('orderrequest', order_type);
      },

      viewRequest (view_type) {
        console.log('emit view request: ' + view_type);
        this.$emit('viewrequest', view_type);
      },

    }

  }

</script>
