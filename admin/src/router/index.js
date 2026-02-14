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
      { path: 'global-offers', name: 'GlobalOffers', component: GlobalOfferList },
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
  next()
})

export default router
