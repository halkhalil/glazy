<template>
    <b-nav-item-dropdown v-if="unreadNotifications"
                         text='<i class="fa fa-bell fa-fw"></i>'>
        <b-dropdown-item v-for="(notification, index) in unreadNotifications"
                         v-bind:key="notification.id"
                         :to="notification.data.link">
            <i v-bind:class="'fa-'+notification.data.type" class="fa fa-fw"></i> {{ notification.data.title }}
        </b-dropdown-item>
        <b-dropdown-item @click.prevent="markAsRead()">
            <i class="fa fa-bell-off fa-fw"></i> Mark All as Read
        </b-dropdown-item>
    </b-nav-item-dropdown>
</template>
<script>
  import VueTimeago from 'vue-timeago'

  export default {
    name: 'NotificationsDropdown',
    components: {
      VueTimeago
    },
    props: {},
    data() {
      return {
        isProcessing: false
      }
    },
    computed: {
      unreadNotifications: function () {
        if (this.$auth.check() &&
          this.$auth.user() &&
          this.$auth.user().unread_notifications &&
          this.$auth.user().unread_notifications.length > 0) {
          return this.$auth.user().unread_notifications
        }
        return null
      }
    },
    methods: {
      markAsRead: function () {
        if (!this.$auth.check()) {
          return
        }
        this.isProcessing = true
        Vue.axios.get(Vue.axios.defaults.baseURL + '/notifications/markAsRead')
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
            this.isProcessing = false
          } else {
            this.isProcessing = false
            this.$emit('notificationsUpdated');
          }
        })
        .catch(response => {
          this.serverError = response;
          this.isProcessing = false
        })
      }
    }
  }
</script>
