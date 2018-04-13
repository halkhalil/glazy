<template>
    <div id="collections-panel">
        <div class="row" v-if="collectionList.length">
            <div class="col-lg-3 col-md-4 col-sm-6 col-6" v-for="collection in collectionList">
                <div class="card material-collection-card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <router-link :to="{ name: 'user', params: { id: getUserSearchParam(collection.createdByUser) }, query: { collection: collection.id }}">
                                {{ collection.name }}
                                <span class="badge badge-info">
                                    {{ collection.materialCount }}
                                </span>
                            </router-link>
                        </h5>
                        <router-link :to="{ name: 'user', params: { id: getUserSearchParam(collection.createdByUser) }}">
                            <div class="author">
                                <img v-if="'profile' in collection.createdByUser && 'avatar' in collection.createdByUser.profile"
                                     v-bind:src="collection.createdByUser.profile.avatar"
                                     class="avatar"/>
                                <span>{{ collection.createdByUser.name }}</span>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
        <div class="collections-table" v-else>
            <h5>Not found in any collections</h5>
        </div>
    </div>
</template>
<script>
  export default {
    name: 'MaterialCollectionsPanel',
    props: {
      material: {
        type: Object,
        default: null
      },
    },
    computed: {
      isLoaded: function () {
        if (this.material) {
          return true;
        }
        return false;
      },

      collectionList: function () {
        if (this.isLoaded) {
          if (this.material.hasOwnProperty('collections')) {
            return this.material.collections;
          }
        }
        return [];
      },

    },

    methods: {

      getUserProfileUrl: function (user) {
        if (user.hasOwnProperty('username')
          && user.username) {
          return '/u/' + user.username;
        }
        return '/u/' + user.id;
      },

      getUserAvatar: function (user) {
        if (user.hasOwnProperty('avatar') && user.avatar) {
          return user.avatar;
        }
        else if (user.hasOwnProperty('gravatar') && user.gravatar) {
          return user.gravatar;
        }
        else {
          return 'http://www.gravatar.com/avatar/?d=mm';
        }
      },

      getDisplayName: function (user) {
        if (user.hasOwnProperty('username') && user.username) {
          return user.username;
        }
        return user.name;
      },

      getUserSearchParam: function (user) {
        if (!user) { return }
        if ('profile' in user && 'username' in user.profile && user.profile.username) {
          return user.profile.username
        }
        return user.id
      }

    }
  }

</script>

<style>

    .material-collection-card .card-title {
        margin-top: 0px;
    }

</style>