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
    <el-menu-item index="/global-offers">
      <i class="el-icon-star-off"></i>
      <span slot="title">Sponsorlar</span>
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
      if (path.startsWith('/global-offers')) return '/global-offers'
      return '/'
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
