import Vue from 'vue'
import ElementUI from 'element-ui'
import locale from 'element-ui/lib/locale/lang/tr-TR'
import 'element-ui/lib/theme-chalk/index.css'
import './responsive.css'
import App from './App.vue'
import router from './router'
import store from './store'

Vue.use(ElementUI, { size: 'medium', locale })
Vue.config.productionTip = false

// Restore user session on app load
store.dispatch('auth/fetchUser')

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount('#app')
