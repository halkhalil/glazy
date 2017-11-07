<template>

<nav aria-label="page navigation" v-if="isLoaded">
    <ul class="pagination">

        <li class="page-item disabled"
            v-if="page_links.current_page == page_links.start_page">
            <span class="page-link">
                <i class="fa fa-chevron-left"></i>
                <span class="sr-only">Previous</span>
            </span>
        </li>
        <li class="page-item"
            v-else>
            <a class="page-link" @click.prevent="pageRequest(page_links.current_page - 1)">
                <i class="fa fa-chevron-left"></i>
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
                <i class="fa fa-chevron-right"></i>
                <span class="sr-only">Next</span>
            </span>
        </li>
        <li class="page-item"
            v-else>
            <a class="page-link" @click.prevent="pageRequest(page_links.current_page + 1)">
                <i class="fa fa-chevron-right"></i>
                <span class="sr-only">Next</span>
            </a>
        </li>
        <li class="page-item totals">
            {{ pagination.from }}
            to {{ pagination.to }}
            of {{ pagination.total }}
            {{ item_type_name }}
        </li>
    </ul>

</nav>



</template>


<script>

export default {

  name: 'Paginator',

  props: [
        'pagination',
        'num_page_links',
        'item_type_name'
    ],

    data() {
        return {
        }
    },


    computed: {

        isLoaded: function() {
            if (this.pagination && this.num_page_links) {
                return true;
            }
            return false;
        },

        page_links: function() {
            var page_links = {};

            if (this.isLoaded) {
                page_links.current_page = this.pagination.current_page;

                page_links.page_link_count = Number(this.num_page_links);

                if (page_links.page_link_count % 2) {
                    var num_side_pages = (page_links.page_link_count - 1) / 2;
                    page_links.start_page = page_links.current_page - num_side_pages;
                    page_links.end_page = page_links.current_page + num_side_pages;
                }
                else {
                    var num_side_pages = page_links.page_link_count / 2;
                    page_links.start_page = page_links.current_page - num_side_pages;
                    page_links.end_page = page_links.current_page + num_side_pages - 1;
                }

                if (page_links.start_page <= 0)
                {
                    page_links.start_page = 1;
                    page_links.end_page = page_links.page_link_count;
                }
                if (page_links.end_page > this.pagination.total_pages)
                {
                    page_links.end_page = this.pagination.total_pages;
                    page_links.start_page = page_links.end_page - page_links.page_link_count + 1;
                    if (page_links.start_page <= 0) {
                        page_links.start_page = 1;
                    }
                }

                page_links.page_link_count = page_links.end_page - page_links.start_page + 1;
            }
            return page_links;
        }

    },
    /*
     $paginatedJson['total'] = $paginatedItems->total();
     $paginatedJson['per_page'] = $paginatedItems->perPage();
     $paginatedJson['current_page'] = $paginatedItems->currentPage();
     $paginatedJson['last_page'] = $paginatedItems->lastPage();
     $paginatedJson['from'] = $paginatedItems->firstItem();
     $paginatedJson['to'] = $paginatedItems->lastItem();
     */

    methods : {
        pageRequest: function(page_num) {
            console.log('emit page request: ' + page_num);
            this.$emit('pagerequest', page_num);
        },

    }

}

</script>
