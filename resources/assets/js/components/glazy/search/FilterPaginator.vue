<template>

    <div class="row pagination-row" v-if="isLoaded">

        <div class="col-sm-7 col-md-8">
            <paginator
                    :pagination="pagination"
                    :num_page_links="5"
                    :item_type_name="item_type_name"
                    v-on:pagerequest="pageRequest"
            ></paginator>
        </div>
        <div class="col-sm-5 col-md-4 text-right">

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
      item_type_name: {
        type: String,
        default: null
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
