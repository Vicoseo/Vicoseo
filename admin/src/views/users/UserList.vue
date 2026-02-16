<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
      <h2 style="margin: 0">Admin Kullanicilari</h2>
      <el-button type="primary" icon="el-icon-plus" @click="$router.push('/users/create')">Yeni Admin</el-button>
    </div>

    <el-card shadow="never">
      <el-table :data="users" v-loading="loading" stripe style="width: 100%">
        <el-table-column prop="name" label="Ad" min-width="120" />
        <el-table-column prop="email" label="E-posta" min-width="180" />
        <el-table-column label="Rol" width="100" align="center">
          <template slot-scope="{ row }">
            <el-tag :type="row.role === 'master' ? 'danger' : 'info'" size="small">
              {{ row.role === 'master' ? 'Master' : 'Admin' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="2FA" width="70" align="center">
          <template slot-scope="{ row }">
            <i :class="row.two_factor_enabled ? 'el-icon-success' : 'el-icon-warning'"
               :style="{ color: row.two_factor_enabled ? '#67c23a' : '#e6a23c' }" />
          </template>
        </el-table-column>
        <el-table-column label="Durum" width="80" align="center">
          <template slot-scope="{ row }">
            <el-tag :type="row.is_active ? 'success' : 'danger'" size="small">
              {{ row.is_active ? 'Aktif' : 'Pasif' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Siteler" width="120" align="center">
          <template slot-scope="{ row }">
            <span v-if="row.role === 'master'">Tumu</span>
            <el-tag v-else size="small" type="info">{{ (row.sites || []).length }} site</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Son Giris" width="150">
          <template slot-scope="{ row }">
            <span v-if="row.last_login_at" style="font-size: 12px">{{ row.last_login_at }}</span>
            <span v-else style="color: #c0c4cc">--</span>
          </template>
        </el-table-column>
        <el-table-column label="Islemler" width="150" align="right">
          <template slot-scope="{ row }">
            <el-button type="text" icon="el-icon-edit" @click="$router.push(`/users/${row.id}/edit`)">Duzenle</el-button>
            <el-button type="text" icon="el-icon-delete" style="color: #f56c6c" @click="handleDelete(row)">Sil</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
import { getUsers, deleteUser } from '../../api/users'

export default {
  name: 'UserList',
  data() {
    return {
      users: [],
      loading: false,
    }
  },
  created() {
    this.fetchUsers()
  },
  methods: {
    async fetchUsers() {
      this.loading = true
      try {
        const { data } = await getUsers()
        this.users = data.data || []
      } catch {
        this.$message.error('Kullanicilar yuklenemedi')
      } finally {
        this.loading = false
      }
    },
    async handleDelete(row) {
      try {
        await this.$confirm(`"${row.name}" kullanicisini silmek istediginize emin misiniz?`, 'Uyari', {
          confirmButtonText: 'Sil',
          cancelButtonText: 'Iptal',
          type: 'warning',
        })
        await deleteUser(row.id)
        this.$message.success('Kullanici silindi')
        this.fetchUsers()
      } catch (err) {
        if (err !== 'cancel') {
          this.$message.error(err?.response?.data?.message || 'Silme basarisiz')
        }
      }
    },
  },
}
</script>
