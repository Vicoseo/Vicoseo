<template>
  <el-menu
    :default-active="activeMenu"
    :collapse="collapsed"
    :router="true"
    background-color="#304156"
    text-color="#bfcbd9"
    active-text-color="#409EFF"
    class="sidebar-menu"
  >
    <div class="sidebar-logo" :class="{ 'is-collapsed': collapsed }">
      <span v-if="!collapsed">CMS Admin</span>
      <span v-else>C</span>
    </div>
    <el-menu-item index="/">
      <i class="el-icon-s-home"></i>
      <span slot="title">Panel</span>
    </el-menu-item>
    <el-menu-item index="/sites">
      <i class="el-icon-monitor"></i>
      <span slot="title">Siteler</span>
    </el-menu-item>
    <el-menu-item index="/domains">
      <i class="el-icon-discover"></i>
      <span slot="title">Domain Y&#246;netimi</span>
    </el-menu-item>
    <el-menu-item index="/global-offers">
      <i class="el-icon-star-off"></i>
      <span slot="title">Sponsorlar</span>
    </el-menu-item>
    <el-menu-item v-if="isMaster" index="/users">
      <i class="el-icon-user"></i>
      <span slot="title">Kullanicilar</span>
    </el-menu-item>
    <el-menu-item index="/logs">
      <i class="el-icon-document"></i>
      <span slot="title">Islem Loglari</span>
    </el-menu-item>
    <el-menu-item index="/logs/system">
      <i class="el-icon-warning-outline"></i>
      <span slot="title">Sistem Loglari</span>
    </el-menu-item>
    <el-menu-item index="/settings/2fa">
      <i class="el-icon-lock"></i>
      <span slot="title">2FA Ayarlari</span>
    </el-menu-item>
  </el-menu>
</template>

<script>
export default {
  name: 'Sidebar',
  props: {
    collapsed: { type: Boolean, default: false },
  },
  computed: {
    activeMenu() {
      const path = this.$route.path
      if (path.startsWith('/sites')) return '/sites'
      if (path.startsWith('/domains')) return '/domains'
      if (path.startsWith('/global-offers')) return '/global-offers'
      if (path.startsWith('/users')) return '/users'
      if (path === '/logs/system') return '/logs/system'
      if (path.startsWith('/logs')) return '/logs'
      if (path.startsWith('/settings/2fa')) return '/settings/2fa'
      return '/'
    },
    isMaster() {
      const user = this.$store.getters['auth/currentUser']
      return user && user.role === 'master'
    },
  },
}
</script>

<style scoped>
.sidebar-menu {
  height: 100%;
  border-right: none;
}
.sidebar-menu:not(.el-menu--collapse) {
  width: 210px;
}
.sidebar-logo {
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 20px;
  font-weight: 700;
  background-color: #263445;
  letter-spacing: 1px;
}
.sidebar-logo.is-collapsed {
  font-size: 24px;
}
</style>
