<template>
  <el-container style="height: 100vh">
    <el-aside :width="sidebarCollapsed ? '64px' : '210px'" style="transition: width 0.3s">
      <Sidebar :collapsed="sidebarCollapsed" />
    </el-aside>
    <el-container>
      <el-header style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e6e6e6; background: #fff">
        <el-button
          type="text"
          :icon="sidebarCollapsed ? 'el-icon-s-unfold' : 'el-icon-s-fold'"
          @click="sidebarCollapsed = !sidebarCollapsed"
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
    return { sidebarCollapsed: false }
  },
  computed: {
    userName() {
      const user = this.$store.getters['auth/currentUser']
      return user ? user.name : 'Admin'
    },
  },
  methods: {
    async handleCommand(cmd) {
      if (cmd === 'logout') {
        await this.$store.dispatch('auth/logout')
        this.$router.push('/login')
      }
    },
  },
}
</script>
