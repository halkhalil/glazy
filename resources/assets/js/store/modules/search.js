const state = {
  query: null,
  searchItems: [],
  searchPagination: null,
  searchUser: null,
  isProcessing: false,
  serverError: null,
  apiError: null
}

const getters = {
  query(state) {
    return state.query
  },
  searchItems(state) {
    return state.searchItems
  },
  searchPagination(state) {
    return state.searchPagination
  },
  searchUser(state) {
    return state.searchUser
  },
  isProcessing(state) {
    return state.isProcessing
  }

}

const mutations = {
  setQuery(state, payload) {
    state.query = payload
  },
  setSearchItems(state, payload) {
    state.searchItems = payload
  },
  setSearchPagination(state, payload) {
    state.searchPagination = payload
  },
  setSearchUser(state, payload) {
    state.searchUser = payload
  },
  setServerError(state, payload) {
    state.serverError = payload
  },
  setApiError(state, payload) {
    state.apiError = payload
  },
  isProcessing(state) {
    state.isProcessing = true
  },
  isNotProcessing(state) {
    state.isProcessing = false
  }

}

const actions = {
  resetError (context) {
    context.commit('setServerError', null)
    context.commit('setApiError', null)
  },
  search (context, payload) {
    context.commit('setQuery', payload.query)
    context.dispatch('refresh')
  },
  refresh (context) {
    // Let components know we are processing a request
    context.commit('isProcessing')

    // Clear out all old errors and search user
    context.commit('setSearchUser', null)
    context.dispatch('resetError')

    var query = context.getters.query
    var myQuery = query.getMinimalQuery()
    //var myQuery = payload.query.getMinimalQuery()

    var querystring = query.toQuerystring(myQuery)

    console.log('VUEX SEARCH: ' + querystring)

    // Make sure itemlist is always defined, and an array
    context.commit('setSearchItems', [])
    Vue.axios.get(Vue.axios.defaults.baseURL + '/search?' + querystring)
      .then((response) => {
      if (response.data.error) {
        context.commit('setApiError', response.data.error)
        context.commit('isNotProcessing')
      } else {
        if (response.data.data) {
          context.commit('setSearchItems', response.data.data)
        }
        context.commit('setSearchPagination', response.data.meta.pagination)
        if ('user' in response.data.meta && 'data' in response.data.meta.user) {
          context.commit('setSearchUser', response.data.meta.user.data)
        }
        context.commit('isNotProcessing')
      }
    })
    .catch(response => {
      if (response.response && response.response.status) {
        if (response.response.status === 401) {
          this.$auth.refresh() // attempt refresh
        } else {
          context.commit('setServerError', response.response.message)
        }
      }
      context.commit('isNotProcessing')
    })
  }
}

export default {
  state,
  mutations,
  actions,
  getters,
  namespaced: true
}
