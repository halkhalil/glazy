const state = {
  isProcessing: false,
  serverError: null,
  apiError: null
}

const getters = {
  isProcessing(state) {
    return state.isProcessing
  }

}

const mutations = {
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
  copy (context, payload) {
    // Don't know if this is worthwhile.. maybe just do in component
    context.commit('isProcessing')
    context.dispatch('resetError')

    if (!payload.id) {
      return
    }
    console.log('VUEX MATERIAL COPY: ' + payload.id)

    return new Promise((resolve, reject) => {
      // Do something here... lets say, a http call using vue-resource
      Vue.axios.get(Vue.axios.defaults.baseURL + '/recipes/' + payload.id + '/copy')
        .then((response) => {
        if (response.data.error) {
          context.commit('setApiError', response.data.error)
          context.commit('isNotProcessing')
          reject(response.data.error)
        } else {
          context.commit('isNotProcessing')
          resolve(response)
          // var materialCopy = response.data.data;
          // this.$router.push({ name: 'recipes', params: { id: materialCopy.id }})
        }
      })
      .catch(response => {
        if (response.response && response.response.status) {
          if (response.response.status === 401) {
            this.$auth.refresh() // attempt refresh
          } else {
            context.commit('setServerError', response.response.message)
            reject(response.response.message)
          }
        }
        context.commit('isNotProcessing')
      })
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
