import { login, logout, getMe } from '../../api/auth'

export default {
  namespaced: true,
  state: {
    token: localStorage.getItem('token') || null,
    user: null,
  },
  getters: {
    isAuthenticated: (state) => !!state.token,
    currentUser: (state) => state.user,
  },
  mutations: {
    SET_TOKEN(state, token) {
      state.token = token
      if (token) {
        localStorage.setItem('token', token)
      } else {
        localStorage.removeItem('token')
      }
    },
    SET_USER(state, user) {
      state.user = user
    },
  },
  actions: {
    async login({ commit }, credentials) {
      const { data } = await login(credentials)
      commit('SET_TOKEN', data.data.token)
      commit('SET_USER', data.data.user)
      return data
    },
    async logout({ commit }) {
      try {
        await logout()
      } finally {
        commit('SET_TOKEN', null)
        commit('SET_USER', null)
      }
    },
    async fetchUser({ commit, state }) {
      if (!state.token) return
      try {
        const { data } = await getMe()
        commit('SET_USER', data.data)
      } catch {
        commit('SET_TOKEN', null)
        commit('SET_USER', null)
      }
    },
  },
}
