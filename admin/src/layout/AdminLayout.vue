<template>
  <el-container style="height: 100vh">
    <!-- Mobile overlay -->
    <div v-if="mobileMenuOpen" class="mobile-overlay" @click="mobileMenuOpen = false"></div>
    <el-aside
      :width="sidebarWidth"
      :class="{ 'mobile-open': mobileMenuOpen }"
      style="transition: width 0.3s, left 0.3s"
    >
      <Sidebar :collapsed="sidebarCollapsed && !isMobile" @navigate="mobileMenuOpen = false" />
    </el-aside>
    <el-container>
      <el-header style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e6e6e6; background: #fff">
        <el-button
          type="text"
          :icon="isMobile ? 'el-icon-s-operation' : (sidebarCollapsed ? 'el-icon-s-unfold' : 'el-icon-s-fold')"
          @click="toggleSidebar"
          style="font-size: 20px"
        />
        <el-dropdown @command="handleCommand">
          <span class="el-dropdown-link" style="cursor: pointer">
            {{ userName }}<i class="el-icon-arrow-down el-icon--right"></i>
          </span>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item command="logout">Çıkış Yap</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </el-header>
      <el-main style="background: #f0f2f5; overflow-y: auto">
        <router-view />
      </el-main>
    </el-container>
  </el-container>
</template>

<script>
import Sidebar from './Sidebar.vue'

export default {
  name: 'AdminLayout',
  components: { Sidebar },
  data() {
    return {
      sidebarCollapsed: false,
      mobileMenuOpen: false,
      isMobile: false,
    }
  },
  computed: {
    userName() {
      const user = this.$store.getters['auth/currentUser']
      return user ? user.name : 'Admin'
    },
    sidebarWidth() {
      if (this.isMobile) return '210px'
      return this.sidebarCollapsed ? '64px' : '210px'
    },
  },
  methods: {
    toggleSidebar() {
      if (this.isMobile) {
        this.mobileMenuOpen = !this.mobileMenuOpen
      } else {
        this.sidebarCollapsed = !this.sidebarCollapsed
      }
    },
    checkMobile() {
      this.isMobile = window.innerWidth <= 768
      if (this.isMobile) {
        this.mobileMenuOpen = false
      }
    },
    async handleCommand(cmd) {
      if (cmd === 'logout') {
        await this.$store.dispatch('auth/logout')
        this.$router.push('/login')
      }
    },
  },
  created() {
    this.checkMobile()
    window.addEventListener('resize', this.checkMobile)
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.checkMobile)
  },
  watch: {
    '$route'() {
      if (this.isMobile) {
        this.mobileMenuOpen = false
      }
    },
  },
}
</script>
