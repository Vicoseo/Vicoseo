<template>
  <div>
    <h2 style="margin: 0 0 20px">Admin İşlem Logları</h2>

    <!-- Filters -->
    <el-card shadow="never" style="margin-bottom: 16px">
      <el-form :inline="true" size="small" @submit.native.prevent="fetchLogs(1)">
        <el-form-item label="Admin">
          <el-select v-model="filters.user_id" clearable placeholder="Tüm adminler" style="width: 180px">
            <el-option v-for="u in admins" :key="u.id" :label="u.name" :value="u.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="Kategori">
          <el-select v-model="filters.resource_type" clearable placeholder="Tümü" style="width: 140px">
            <el-option label="Giriş / Auth" value="user" />
            <el-option label="Site" value="site" />
            <el-option label="Domain" value="domain" />
            <el-option label="Sayfa" value="page" />
            <el-option label="Yazı" value="post" />
            <el-option label="Sponsor" value="sponsor" />
            <el-option label="Redirect" value="redirect" />
          </el-select>
        </el-form-item>
        <el-form-item label="Aksiyon">
          <el-input v-model="filters.action" placeholder="ör: auth.login" clearable style="width: 150px" />
        </el-form-item>
        <el-form-item label="Tarih">
          <el-date-picker v-model="dateRange" type="daterange" range-separator="-" start-placeholder="Başlangıç" end-placeholder="Bitiş" value-format="yyyy-MM-dd" style="width: 240px" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="fetchLogs(1)">Filtrele</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <!-- Log Table -->
    <el-card shadow="never">
      <el-table :data="logs" v-loading="loading" stripe style="width: 100%" :row-class-name="rowClass">
        <el-table-column label="Tarih" width="155">
          <template slot-scope="{ row }">
            <span style="font-size: 12px; font-family: monospace">{{ formatDate(row.created_at) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Admin" width="130">
          <template slot-scope="{ row }">
            {{ row.user ? row.user.name : '—' }}
          </template>
        </el-table-column>
        <el-table-column label="İşlem" min-width="220">
          <template slot-scope="{ row }">
            <el-tag :type="actionTagType(row.action)" size="mini" effect="dark" style="margin-right: 6px">
              {{ actionIcon(row.action) }}
            </el-tag>
            <span style="font-weight: 500">{{ actionLabel(row.action) }}</span>
            <span v-if="actionExtra(row)" style="color: #909399; margin-left: 6px; font-size: 12px">
              {{ actionExtra(row) }}
            </span>
          </template>
        </el-table-column>
        <el-table-column label="IP" width="130">
          <template slot-scope="{ row }">
            <code style="font-size: 12px">{{ row.ip_address }}</code>
          </template>
        </el-table-column>
        <el-table-column label="Detay" width="80" align="center">
          <template slot-scope="{ row }">
            <el-popover trigger="click" width="350" v-if="row.details">
              <pre style="font-size: 11px; max-height: 250px; overflow: auto; margin: 0">{{ JSON.stringify(row.details, null, 2) }}</pre>
              <el-button slot="reference" type="text" size="small" icon="el-icon-document">JSON</el-button>
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

const ACTION_MAP = {
  'auth.login': { label: 'Giriş Yaptı', icon: 'GIRIS', type: 'success' },
  'auth.logout': { label: 'Çıkış Yaptı', icon: 'CIKIS', type: 'info' },
  'site.create': { label: 'Site Oluşturdu', icon: 'SITE', type: 'success' },
  'site.update': { label: 'Site Güncelledi', icon: 'SITE', type: '' },
  'site.delete': { label: 'Site Sildi', icon: 'SITE', type: 'danger' },
  'site.redirect_update': { label: '301 Redirect Değiştirdi', icon: '301', type: 'warning' },
  'user.create': { label: 'Yeni Admin Oluşturdu', icon: 'ADMIN', type: 'success' },
  'user.update': { label: 'Admin Güncelledi', icon: 'ADMIN', type: '' },
  'user.delete': { label: 'Admin Sildi', icon: 'ADMIN', type: 'danger' },
  'user.password_change': { label: 'Şifre Değiştirdi', icon: 'SIFRE', type: 'warning' },
  'user.2fa_reset': { label: '2FA Sıfırladı', icon: '2FA', type: 'warning' },
  'domain.setup': { label: 'Domain Kurulumu', icon: 'DOMAIN', type: 'success' },
  'domain.purchase': { label: 'Domain Satın Aldı', icon: 'DOMAIN', type: 'success' },
  'domain.fix_pending': { label: 'Domain Aktifleştirme', icon: 'DOMAIN', type: 'warning' },
  'dns.add': { label: 'DNS Kaydı Ekledi', icon: 'DNS', type: '' },
  'page.create': { label: 'Sayfa Oluşturdu', icon: 'SAYFA', type: 'success' },
  'page.update': { label: 'Sayfa Güncelledi', icon: 'SAYFA', type: '' },
  'page.delete': { label: 'Sayfa Sildi', icon: 'SAYFA', type: 'danger' },
  'post.create': { label: 'Yazı Oluşturdu', icon: 'YAZI', type: 'success' },
  'post.update': { label: 'Yazı Güncelledi', icon: 'YAZI', type: '' },
  'post.delete': { label: 'Yazı Sildi', icon: 'YAZI', type: 'danger' },
  'content.bulk_generate': { label: 'Toplu İçerik Üretimi', icon: 'AI', type: '' },
  'content.ai_generate': { label: 'AI İçerik Üretimi', icon: 'AI', type: '' },
}

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
        resource_type: '',
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
      if (typeof page === 'number') this.currentPage = page
      try {
        const params = { page: this.currentPage, per_page: this.perPage }
        if (this.filters.user_id) params.user_id = this.filters.user_id
        if (this.filters.action) params.action = this.filters.action
        if (this.filters.resource_type) params.resource_type = this.filters.resource_type
        if (this.dateRange && this.dateRange[0]) params.from = this.dateRange[0]
        if (this.dateRange && this.dateRange[1]) params.to = this.dateRange[1]

        const { data } = await getAdminLogs(params)
        this.logs = data.data || []
        this.total = data.total || 0
      } catch {
        this.$message.error('Loglar yüklenemedi')
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
    formatDate(d) {
      if (!d) return ''
      return d.replace('T', ' ').substring(0, 19)
    },
    actionLabel(action) {
      return ACTION_MAP[action]?.label || action
    },
    actionIcon(action) {
      return ACTION_MAP[action]?.icon || 'LOG'
    },
    actionTagType(action) {
      return ACTION_MAP[action]?.type || 'info'
    },
    actionExtra(row) {
      const d = row.details
      if (!d) return ''
      if (d.fallback_domain) return `→ ${d.fallback_domain}`
      if (d.domain) return d.domain
      if (d.user_data?.email) return d.user_data.email
      if (d.dns) return `${d.dns.type} ${d.dns.name}`
      if (d.email) return d.email
      return ''
    },
    rowClass({ row }) {
      if (row.action === 'auth.login') return 'row-login'
      if (row.action?.includes('redirect') || row.action?.includes('password') || row.action?.includes('delete')) return 'row-important'
      return ''
    },
  },
}
</script>

<style scoped>
.row-login td { background: #f0f9eb !important; }
.row-important td { background: #fef0f0 !important; }
</style>
