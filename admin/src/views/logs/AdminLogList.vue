<template>
  <div>
    <h2 style="margin: 0 0 20px">Admin Islem Loglari</h2>

    <!-- Filters -->
    <el-card shadow="never" style="margin-bottom: 16px">
      <el-form :inline="true" size="small">
        <el-form-item label="Admin">
          <el-select v-model="filters.user_id" clearable placeholder="Tum adminler" style="width: 180px">
            <el-option v-for="u in admins" :key="u.id" :label="u.name" :value="u.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="Aksiyon">
          <el-input v-model="filters.action" placeholder="Arama..." clearable style="width: 150px" />
        </el-form-item>
        <el-form-item label="Tarih">
          <el-date-picker v-model="dateRange" type="daterange" range-separator="-" start-placeholder="Baslangic" end-placeholder="Bitis" value-format="yyyy-MM-dd" style="width: 240px" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="fetchLogs">Filtrele</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- Log Table -->
    <el-card shadow="never">
      <el-table :data="logs" v-loading="loading" stripe style="width: 100%">
        <el-table-column label="Tarih" width="160">
          <template slot-scope="{ row }">
            <span style="font-size: 12px">{{ row.created_at }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Admin" width="140">
          <template slot-scope="{ row }">
            {{ row.user ? row.user.name : 'Silinmis' }}
          </template>
        </el-table-column>
        <el-table-column prop="action" label="Aksiyon" min-width="200" show-overflow-tooltip />
        <el-table-column prop="resource_type" label="Kaynak" width="100" />
        <el-table-column prop="ip_address" label="IP" width="130" />
        <el-table-column label="Detay" width="100" align="center">
          <template slot-scope="{ row }">
            <el-popover trigger="click" width="300" v-if="row.details">
              <pre style="font-size: 11px; max-height: 200px; overflow: auto">{{ JSON.stringify(row.details, null, 2) }}</pre>
              <el-button slot="reference" type="text" size="small">Gor</el-button>
            </el-popover>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        v-if="total > perPage"
        style="margin-top: 16px; text-align: center"
        layout="prev, pager, next, total"
        :total="total"
        :page-size="perPage"
        :current-page.sync="currentPage"
        @current-change="fetchLogs"
      />
    </el-card>
  </div>
</template>

<script>
import { getAdminLogs } from '../../api/logs'
import { getUsers } from '../../api/users'

export default {
  name: 'AdminLogList',
  data() {
    return {
      logs: [],
      admins: [],
      loading: false,
      currentPage: 1,
      perPage: 50,
      total: 0,
      filters: {
        user_id: '',
        action: '',
      },
      dateRange: null,
    }
  },
  created() {
    this.fetchLogs()
    this.fetchAdmins()
  },
  methods: {
    async fetchLogs(page) {
      this.loading = true
      try {
        const params = {
          page: page || this.currentPage,
          per_page: this.perPage,
        }
        if (this.filters.user_id) params.user_id = this.filters.user_id
        if (this.filters.action) params.action = this.filters.action
        if (this.dateRange && this.dateRange[0]) params.from = this.dateRange[0]
        if (this.dateRange && this.dateRange[1]) params.to = this.dateRange[1]

        const { data } = await getAdminLogs(params)
        this.logs = data.data || []
        this.total = data.total || 0
      } catch {
        this.$message.error('Loglar yuklenemedi')
      } finally {
        this.loading = false
      }
    },
    async fetchAdmins() {
      try {
        const { data } = await getUsers({ per_page: 200 })
        this.admins = data.data || []
      } catch {}
    },
  },
}
</script>
