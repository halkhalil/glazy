<template>
    <div v-if="searchUser" class="card card-glazy-profile">
        <div v-if="'profile' in searchUser && searchUser.profile.avatar"
             class="card-avatar">
            <img v-bind:src="searchUser.profile.avatar"
                 v-bind:alt="searchUser.name"
                 class="rounded-circle">
        </div>
        <div class="card-body">
            <h4 class="card-title">{{ searchUser.name }}</h4>
            <h6 v-if="'profile' in searchUser && searchUser.profile.countryName" class="category text-gray">
                {{ searchUser.profile.countryName }}
            </h6>
            <span >
              <router-link v-if="$auth.check() && $auth.user().id === searchUser.id"
                           :to="{ name: 'user-edit' }">
                <i class="fa fa-edit"></i> Edit
              </router-link>
            </span>
            <div v-if="'profile' in searchUser">
                <a v-if="'url' in searchUser.profile && searchUser.profile.url"
                   v-bind:href="'http://' + searchUser.profile.url"
                   target="_blank"
                   class="btn btn-icon btn-sm btn-round btn-default">
                    <i class="fa fa-link"></i>
                </a>
                <a v-if="'facebook' in searchUser.profile && searchUser.profile.facebook"
                   v-bind:href="'https://www.facebook.com/' + searchUser.profile.facebook"
                   target="_blank"
                   class="btn btn-icon btn-sm btn-round btn-facebook">
                    <i class="fa fa-facebook"></i>
                </a>
                <a v-if="'instagram' in searchUser.profile && searchUser.profile.instagram"
                   v-bind:href="'https://www.instagram.com/' + searchUser.profile.instagram"
                   target="_blank"
                   class="btn btn-icon btn-sm btn-round btn-instagram">
                    <i class="fa fa-instagram"></i>
                </a>
                <a v-if="'pinterest' in searchUser.profile && searchUser.profile.pinterest"
                   v-bind:href="'https://www.pinterest.com/' + searchUser.profile.pinterest"
                   target="_blank"
                   class="btn btn-icon btn-sm btn-round btn-pinterest">
                    <i class="fa fa-pinterest"></i>
                </a>
            </div>
        </div>
    </div>
</template>
<script>
  export default {
    name: 'UserProfileCard',
    props: {
      searchUser: {
        type: Object,
        default: null
      }
    },

    data() {
      return {
      }
    },

    computed: {}
  }
</script>
<style>
    .card-glazy-profile {
        margin-top: 0;
        margin-bottom: 10px;
    }

    .card-glazy-profile .card-avatar {
        max-width: 64px;
        max-height: 64px;
    }

    .card-glazy-profile .card-avatar img {
        float: left;
        margin: 10px;
    }

    .card-glazy-profile .card-body {
        padding: 0 10px;
    }

    .card-glazy-profile .card-body .card-title {
        margin-top: 10px;
        font-size: 1.25em;
    }

    .card-glazy-profile .card-body .btn {
        margin: 0 1px 10px 1px;
    }
</style>