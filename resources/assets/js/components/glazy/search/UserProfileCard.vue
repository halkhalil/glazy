<template>
    <div v-if="searchUser" class="card-glazy-profile">
        <div v-if="'profile' in searchUser && searchUser.profile.avatar"
             class="card-avatar">
            <img v-bind:src="searchUser.profile.avatar"
                 v-bind:alt="searchUser.name"
                 class="rounded-circle">
        </div>
        <div class="card-glazy-profile-body">
            <h4 class="card-glazy-profile-title">{{ searchUser.name }}</h4>
            <h6 v-if="'profile' in searchUser && searchUser.profile.countryName" class="category text-gray">
                {{ searchUser.profile.countryName }}
            </h6>
            <div v-if="'profile' in searchUser">
                <a v-if="'url' in searchUser.profile && searchUser.profile.url"
                   v-bind:href="'http://' + searchUser.profile.url"
                   target="_blank"
                   class="btn btn-icon btn-sm btn-round btn-default">
                    <i class="fab fa-link"></i>
                </a>
                <a v-if="'facebook' in searchUser.profile && searchUser.profile.facebook"
                   v-bind:href="'https://www.facebook.com/' + searchUser.profile.facebook"
                   target="_blank"
                   class="btn btn-icon btn-sm btn-round btn-facebook">
                    <i class="fab fa-facebook"></i>
                </a>
                <a v-if="'instagram' in searchUser.profile && searchUser.profile.instagram"
                   v-bind:href="'https://www.instagram.com/' + searchUser.profile.instagram"
                   target="_blank"
                   class="btn btn-icon btn-sm btn-round btn-instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a v-if="'pinterest' in searchUser.profile && searchUser.profile.pinterest"
                   v-bind:href="'https://www.pinterest.com/' + searchUser.profile.pinterest"
                   target="_blank"
                   class="btn btn-icon btn-sm btn-round btn-pinterest">
                    <i class="fab fa-pinterest"></i>
                </a>
                <a v-if="'artaxis' in searchUser.profile && searchUser.profile.artaxis"
                   v-bind:href="'https://artaxis.org/' + searchUser.profile.artaxis"
                   target="_blank"
                   class="btn btn-icon btn-sm btn-round btn-artaxis">a</a>
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
    }

    .card-glazy-profile .card-avatar {
        max-width: 64px;
        max-height: 64px;
    }

    .card-glazy-profile .card-avatar img {
        float: left;
        margin: 0 10px 0 0;
    }

    .card-glazy-profile .card-glazy-profile-body {
        padding: 0 10px;
    }

    .card-glazy-profile .card-glazy-profile-body .card-glazy-profile-title {
        margin-top: 10px;
        margin-bottom: 10px;
        font-size: 1.25em;
    }

    .card-glazy-profile .card-glazy-profile-body .btn {
        margin: 0 1px 10px 1px;
    }

    .btn-artaxis {
        background-color: #9a9901;
        color: #ffffff;
        font-size: 16px !important;
        line-height: 28px !important;
    }
</style>