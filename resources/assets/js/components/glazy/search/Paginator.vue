<template>

<nav aria-label="page navigation" v-if="isLoaded">
    <ul class="pagination">

        <li class="page-item disabled d-none d-sm-block"
            v-if="page_links.current_page == 1 && !isCompact">
            <span class="page-link">
                <i class="fa fa-angle-double-left fa-fw"></i>
                <span class="sr-only">First</span>
            </span>
        </li>
        <li class="page-item d-none d-sm-block"
            v-else-if="!isCompact">
            <a class="page-link" @click.prevent="pageRequest(1)">
                <i class="fa fa-angle-double-left fa-fw"></i>
                <span class="sr-only">First</span>
            </a>
        </li>

        <li class="page-item disabled"
            v-if="page_links.current_page == 1">
            <span class="page-link">
                <i class="fa fa-angle-left"></i>
                <span class="sr-only">Previous</span>
            </span>
        </li>
        <li class="page-item"
            v-else>
            <a class="page-link" @click.prevent="pageRequest(page_links.current_page - 1)">
                <i class="fa fa-angle-left"></i>
                <span class="sr-only">Previous</span>
            </a>
        </li>

        <li class="page-item"
            v-for="n in page_links.page_link_count"
            v-bind:class="{ 'active' : (n - 1 + page_links.start_page) == page_links.current_page }">
            <a class="page-link" @click.prevent="pageRequest((n - 1 + page_links.start_page))"
               v-if="(n - 1 + page_links.start_page) != page_links.current_page">
                {{ (n - 1 + page_links.start_page) }}
            </a>
            <span class="page-link"
                v-else>
                {{ (n - 1 + page_links.start_page) }}
                <span class="sr-only">(current)</span>
            </span>
        </li>

        <li class="page-item disabled"
            v-if="page_links.current_page == page_links.end_page">
            <span class="page-link">
                <i class="fa fa-angle-right"></i>
                <span class="sr-only">Next</span>
            </span>
        </li>
        <li class="page-item"
            v-else>
            <a class="page-link" @click.prevent="pageRequest(page_links.current_page + 1)">
                <i class="fa fa-angle-right"></i>
                <span class="sr-only">Next</span>
            </a>
        </li>

        <li class="page-item disabled d-none d-sm-block"
            v-if="page_links.current_page == pagination.total_pages && !isCompact">
            <span class="page-link">
                <i class="fa fa-angle-double-right"></i>
                <span class="sr-only">Last</span>
            </span>
        </li>
        <li class="page-item d-none d-sm-block"
            v-else-if="!isCompact">
            <a class="page-link" @click.prevent="pageRequest(pagination.total_pages)">
                <i class="fa fa-angle-double-right"></i>
                <span class="sr-only">Last</span>
            </a>
        </li>
    </ul>

    <div v-if="showTotals && !isCompact" class="page-item totals">
        {{ paginationFrom }}
        to {{ paginationTo }}
        of {{ pagination.total }}
        {{ itemTypeName }}
    </div>

</nav>



</template>


<script>

export default {

  name: 'Paginator',

  props: {
    pagination: {
      type: Object,
      default: null
    },
    numPageLinks: {
      type: Number,
      default: 5
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

    isLoaded: function () {
      if (this.pagination && this.numPageLinks) {
        return true;
      }
      return false;
    },

    paginationFrom: function () {
      return (this.pagination.per_page * this.pagination.current_page) - this.pagination.per_page + 1
    },

    paginationTo: function () {
      var myto = this.pagination.per_page * this.pagination.current_page
      if (myto > this.pagination.total) return this.pagination.total
      return myto
    },

    page_links: function () {
      var page_links = {}

      if (this.isLoaded) {
        page_links.current_page = this.pagination.current_page

        if (this.isCompact) {
          page_links.page_link_count = 3
        }
        else {
          page_links.page_link_count = Number(this.numPageLinks)
        }

        if (page_links.page_link_count % 2) {
          var num_side_pages = (page_links.page_link_count - 1) / 2
          page_links.start_page = page_links.current_page - num_side_pages
          page_links.end_page = page_links.current_page + num_side_pages
        }
        else {
          var num_side_pages = page_links.page_link_count / 2
          page_links.start_page = page_links.current_page - num_side_pages
          page_links.end_page = page_links.current_page + num_side_pages - 1
        }

        if (page_links.start_page <= 0) {
          page_links.start_page = 1
          page_links.end_page = page_links.page_link_count
        }
        if (page_links.end_page > this.pagination.total_pages) {
          page_links.end_page = this.pagination.total_pages
          page_links.start_page = page_links.end_page - page_links.page_link_count + 1
          if (page_links.start_page <= 0) {
            page_links.start_page = 1
          }
        }

        page_links.page_link_count = page_links.end_page - page_links.start_page + 1
      }
      return page_links
    }

  },

  methods: {
    pageRequest: function (page_num) {
      this.$emit('pagerequest', page_num)
    }
  }

}

</script>
