<template>
  <div>
    <h2 style="margin-top: 0">Sistem Loglari</h2>

    <el-tabs v-model="activeTab">
      <el-tab-pane label="Backend Loglari" name="backend">
        <div style="margin-bottom: 12px; display: flex; gap: 8px; align-items: center">
          <el-select v-model="level" placeholder="Log Seviyesi" size="small" clearable style="width: 150px" @change="fetchBackendLogs">
            <el-option label="Error" value="error" />
            <el-option label="Warning" value="warning" />
            <el-option label="Info" value="info" />
            <el-option label="Debug" value="debug" />
            <el-option label="Critical" value="critical" />
          </el-select>
          <el-button size="small" @click="fetchBackendLogs">Yenile</el-button>
          <span style="color: #909399; font-size: 12px" v-if="backendTotal">Toplam: {{ backendTotal }} kayit</span>
        </div>

        <el-table :data="backendLogs" v-loading="backendLoading" size="small" border max-height="600">
          <el-table-column label="Tarih" width="180" prop="timestamp" />
          <el-table-column label="Seviye" width="100">
            <template slot-scope="{ row }">
              <el-tag :type="levelTag(row.level)" size="mini">{{ row.level }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="Mesaj">
            <template slot-scope="{ row }">
              <div style="max-height: 100px; overflow: auto; font-size: 12px; font-family: monospace; white-space: pre-wrap">{{ row.message }}</div>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>

      <el-tab-pane label="Admin Islem Loglari" name="admin">
        <AdminLogList />
      </el-tab-pane>
    </el-tabs>

    <el-row :gutter="16" style="margin-top: 20px" v-if="summary">
      <el-col :span="4" v-for="(count, lvl) in summary" :key="lvl">
        <el-card shadow="never">
          <div style="text-align: center">
            <div style="font-size: 20px; font-weight: 700" :style="{ color: summaryColor(lvl) }">{{ count }}</div>
            <div style="font-size: 12px; color: #909399">{{ lvl }}</div>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import client from '../../api/client'
import AdminLogList from './AdminLogList.vue'

export default {
  name: 'SystemLogList',
  components: { AdminLogList },
  data() {
    return {
      activeTab: 'backend',
      backendLogs: [],
      backendLoading: false,
      backendTotal: 0,
      level: '',
      summary: null,
    }
  },
  created() {
    this.fetchBackendLogs()
    this.fetchSummary()
  },
  methods: {
    async fetchBackendLogs() {
      this.backendLoading = true
      try {
        const params = { per_page: 100 }
        if (this.level) params.level = this.level
        const { data } = await client.get('/admin/logs/backend', { params })
        this.backendLogs = data.data
        this.backendTotal = data.total
      } catch {
        this.$message.error('Loglar yuklenemedi')
      } finally {
        this.backendLoading = false
      }
    },
    async fetchSummary() {
      try {
        const { data } = await client.get('/admin/logs/summary')
        this.summary = data.data
      } catch {
        // silent
      }
    },
    levelTag(level) {
      const map = { error: 'danger', critical: 'danger', warning: 'warning', info: '', debug: 'info' }
      return map[level] || 'info'
    },
    summaryColor(level) {
      const map = { error: '#f56c6c', critical: '#f56c6c', warning: '#e6a23c', info: '#409eff', debug: '#909399' }
      return map[level] || '#909399'
    },
  },
}
</script>
