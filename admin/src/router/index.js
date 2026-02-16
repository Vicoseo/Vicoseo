import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '../store'
import AdminLayout from '../layout/AdminLayout.vue'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import SiteList from '../views/sites/SiteList.vue'
import SiteForm from '../views/sites/SiteForm.vue'
import SiteDetail from '../views/sites/SiteDetail.vue'
import PageForm from '../views/pages/PageForm.vue'
import PostForm from '../views/posts/PostForm.vue'
import GlobalOfferList from '../views/top-offers/GlobalOfferList.vue'
import DomainManager from '../views/domains/DomainManager.vue'
import UserList from '../views/users/UserList.vue'
import UserForm from '../views/users/UserForm.vue'
import TwoFactorSetup from '../views/settings/TwoFactorSetup.vue'
import AdminLogList from '../views/logs/AdminLogList.vue'
import SystemLogList from '../views/logs/SystemLogList.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true },
  },
  {
    path: '/',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'Dashboard', component: Dashboard },
      { path: 'sites', name: 'SiteList', component: SiteList },
      { path: 'sites/create', name: 'SiteCreate', component: SiteForm },
      { path: 'sites/:id/edit', name: 'SiteEdit', component: SiteForm, props: true },
      { path: 'sites/:id', name: 'SiteDetail', component: SiteDetail, props: true },
      { path: 'sites/:siteId/pages/create', name: 'PageCreate', component: PageForm, props: true },
      { path: 'sites/:siteId/pages/:pageId', name: 'PageEdit', component: PageForm, props: true },
      { path: 'sites/:siteId/posts/create', name: 'PostCreate', component: PostForm, props: true },
      { path: 'sites/:siteId/posts/:postId', name: 'PostEdit', component: PostForm, props: true },
      { path: 'domains', name: 'DomainManager', component: DomainManager },
      { path: 'global-offers', name: 'GlobalOffers', component: GlobalOfferList },
      { path: 'users', name: 'UserList', component: UserList, meta: { masterOnly: true } },
      { path: 'users/create', name: 'UserCreate', component: UserForm, meta: { masterOnly: true } },
      { path: 'users/:id/edit', name: 'UserEdit', component: UserForm, props: true, meta: { masterOnly: true } },
      { path: 'settings/2fa', name: 'TwoFactorSetup', component: TwoFactorSetup },
      { path: 'logs', name: 'AdminLogs', component: AdminLogList },
      { path: 'logs/system', name: 'SystemLogs', component: SystemLogList },
    ],
  },
]

const router = new VueRouter({
  mode: 'history',
  base: '/admin/',
  routes,
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters['auth/isAuthenticated']

  if (to.matched.some((r) => r.meta.requiresAuth) && !isAuthenticated) {
    return next('/login')
  }
  if (to.matched.some((r) => r.meta.guest) && isAuthenticated) {
    return next('/')
  }

  // Master-only route check
  if (to.matched.some((r) => r.meta.masterOnly)) {
    const user = store.getters['auth/currentUser']
    if (user && user.role !== 'master') {
      return next('/')
    }
  }

  next()
})

export default router
