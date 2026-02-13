<template>
  <div>
    <h2 style="margin-top: 0">Panel</h2>
    <el-row :gutter="20" style="margin-bottom: 20px">
      <el-col :span="8">
        <el-card shadow="hover">
          <div class="stat-card">
            <div class="stat-number">{{ totalSites }}</div>
            <div class="stat-label">Toplam Site</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card shadow="hover">
          <div class="stat-card">
            <div class="stat-number" style="color: #67c23a">{{ activeSites }}</div>
            <div class="stat-label">Aktif Siteler</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card shadow="hover">
          <div class="stat-card">
            <div class="stat-number" style="color: #f56c6c">{{ inactiveSites }}</div>
            <div class="stat-label">Pasif Siteler</div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <el-card>
      <div slot="header" style="display: flex; justify-content: space-between; align-items: center">
        <span>Son Eklenen Siteler</span>
        <el-button type="primary" size="small" @click="$router.push('/sites')">Tümünü Gör</el-button>
      </div>
      <el-table :data="recentSites" v-loading="loading" stripe>
        <el-table-column prop="domain" label="Alan Adı" />
        <el-table-column prop="name" label="İsim" />
        <el-table-column label="Durum" width="100">
          <template slot-scope="{ row }">
            <el-tag :type="row.is_active ? 'success' : 'danger'" size="small">
              {{ row.is_active ? 'Aktif' : 'Pasif' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="created_at" label="Oluşturulma" width="180">
          <template slot-scope="{ row }">
            {{ formatDate(row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column label="İşlemler" width="120">
          <template slot-scope="{ row }">
            <el-button type="text" @click="$router.push(`/sites/${row.id}`)">Yönet</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
import { getSites } from '../api/sites'

export default {
  name: 'Dashboard',
  data() {
    return {
      sites: [],
      loading: false,
    }
  },
  computed: {
    totalSites() {
      return this.sites.length
    },
    activeSites() {
      return this.sites.filter((s) => s.is_active).length
    },
    inactiveSites() {
      return this.sites.filter((s) => !s.is_active).length
    },
    recentSites() {
      return this.sites.slice(0, 5)
    },
  },
  created() {
    this.fetchSites()
  },
  methods: {
    async fetchSites() {
      this.loading = true
      try {
        const { data } = await getSites({ per_page: 50 })
        this.sites = data.data || []
      } catch {
        this.$message.error('Siteler yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    formatDate(d) {
      if (!d) return ''
      return new Date(d).toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      })
    },
  },
}
</script>

<style scoped>
.stat-card {
  text-align: center;
  padding: 10px 0;
}
.stat-number {
  font-size: 36px;
  font-weight: 700;
  color: #409eff;
}
.stat-label {
  font-size: 14px;
  color: #909399;
  margin-top: 4px;
}
</style>
