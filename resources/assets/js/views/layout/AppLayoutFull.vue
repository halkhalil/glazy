<template>
    <div class="container-fluid app-container">
        <AppHeader/>
        <router-view></router-view>
    </div>
</template>

<script>
    import AppHeader from './AppHeader.vue'
    import AppSidebar from './AppSidebar.vue'

  export default {
    name: 'AppLayoutFull',
    components: {
      AppHeader,
      AppSidebar
    },
    mounted() {
/*
      // Hide submenus
      $('#body-row .collapse').collapse('hide');

    // Collapse/Expand icon
    $('#collapse-icon').addClass('fa-angle-double-left');

    // Collapse click
    $('[data-toggle=sidebar-colapse]').click(function() {
      SidebarCollapse();
    });

    function SidebarCollapse () {
      $('.menu-collapsed').toggleClass('d-none');
      $('.sidebar-submenu').toggleClass('d-none');
      $('.submenu-icon').toggleClass('d-none');
      $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

      // Treating d-flex/d-none on separators with title
      var SeparatorTitle = $('.sidebar-separator-title');
      if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
      } else {
        SeparatorTitle.addClass('d-flex');
      }

      // Collapse/Expand icon
      $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
    }
    */
    }
  }
</script>

<style>
    .app-container {
    }
</style>