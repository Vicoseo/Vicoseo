<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
      <h2 style="margin: 0">Siteler</h2>
      <div style="display: flex; align-items: center; gap: 12px">
        <el-tag v-if="analyticsLoaded" type="info" size="small">
          {{ analyticsData.length }} site analitik verisi
        </el-tag>
        <el-button type="primary" icon="el-icon-plus" @click="$router.push('/sites/create')">Yeni Site</el-button>
      </div>
    </div>

    <el-card>
      <div style="margin-bottom: 16px">
        <el-input
          v-model="search"
          placeholder="Alan adı veya isim ile ara..."
          prefix-icon="el-icon-search"
          clearable
          style="width: 300px"
          @input="handleSearch"
        />
      </div>

      <el-table :data="filteredSites" v-loading="loading" stripe style="width: 100%">
        <el-table-column label="Alan Adı" min-width="200" sortable sort-by="domain">
          <template slot-scope="{ row }">
            <div style="display: flex; align-items: center; gap: 8px">
              <img
                v-if="row.logo_url"
                :src="resolveUrl(row.logo_url)"
                style="width: 28px; height: 28px; object-fit: contain; border-radius: 4px; border: 1px solid #ebeef5"
                :alt="row.domain"
              />
              <span v-else style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; background: #f0f2f5; border-radius: 4px; color: #c0c4cc; font-size: 14px; border: 1px solid #ebeef5">
                <i class="el-icon-picture-outline"></i>
              </span>
              <span style="font-weight: 500">{{ row.domain }}</span>
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="name" label="İsim" min-width="120" sortable />

        <el-table-column label="7 Gün Trend" width="120" align="center">
          <template slot-scope="{ row }">
            <div v-if="getSiteAnalytics(row.id) && getSiteAnalytics(row.id).daily && getSiteAnalytics(row.id).daily.length > 1" style="display: flex; align-items: center; justify-content: center">
              <canvas :ref="'spark-' + row.id" width="90" height="30" style="display: block"></canvas>
            </div>
            <span v-else style="color: #c0c4cc; font-size: 12px">-</span>
          </template>
        </el-table-column>

        <el-table-column label="Ziyaretçi" width="100" align="right" sortable :sort-method="sortByVisitors">
          <template slot-scope="{ row }">
            <span v-if="getSiteAnalytics(row.id)" style="font-weight: 600; color: #409eff">
              {{ formatNumber(getSiteAnalytics(row.id).active_users) }}
            </span>
            <span v-else style="color: #c0c4cc">-</span>
          </template>
        </el-table-column>

        <el-table-column label="Tıklama" width="100" align="right" sortable :sort-method="sortByClicks">
          <template slot-scope="{ row }">
            <span v-if="getSiteAnalytics(row.id) && getSiteAnalytics(row.id).clicks > 0" style="font-weight: 600; color: #67c23a">
              {{ formatNumber(getSiteAnalytics(row.id).clicks) }}
            </span>
            <span v-else style="color: #c0c4cc">-</span>
          </template>
        </el-table-column>

        <el-table-column label="Durum" width="80" sortable sort-by="is_active" align="center">
          <template slot-scope="{ row }">
            <el-tag :type="row.is_active ? 'success' : 'danger'" size="mini">
              {{ row.is_active ? 'Aktif' : 'Pasif' }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column label="İşlemler" width="200" align="right">
          <template slot-scope="{ row }">
            <el-button type="primary" size="mini" @click="$router.push(`/sites/${row.id}`)">Yönet</el-button>
            <el-button size="mini" @click="$router.push(`/sites/${row.id}/edit`)">Düzenle</el-button>
            <el-button type="danger" size="mini" @click="handleDelete(row)">Sil</el-button>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        v-if="total > perPage"
        style="margin-top: 16px; text-align: right"
        layout="prev, pager, next, total"
        :total="total"
        :page-size="perPage"
        :current-page.sync="currentPage"
        @current-change="fetchSites"
      />
    </el-card>
  </div>
</template>

<script>
import { Chart, LineController, LineElement, PointElement, LinearScale, CategoryScale, Filler } from 'chart.js'
import { getSites, deleteSite } from '../../api/sites'
import { getAnalyticsSummary } from '../../api/analytics'

Chart.register(LineController, LineElement, PointElement, LinearScale, CategoryScale, Filler)

export default {
  name: 'SiteList',
  data() {
    return {
      sites: [],
      loading: false,
      search: '',
      currentPage: 1,
      perPage: 15,
      total: 0,
      analyticsData: [],
      analyticsLoaded: false,
      sparkCharts: {},
    }
  },
  computed: {
    filteredSites() {
      if (!this.search) return this.sites
      const q = this.search.toLowerCase()
      return this.sites.filter(
        (s) => s.domain.toLowerCase().includes(q) || s.name.toLowerCase().includes(q)
      )
    },
    analyticsMap() {
      const map = {}
      this.analyticsData.forEach((item) => {
        map[item.site_id] = item
      })
      return map
    },
  },
  created() {
    this.fetchSites()
    this.fetchAnalytics()
  },
  methods: {
    async fetchSites(page) {
      this.loading = true
      try {
        const { data } = await getSites({ page: page || this.currentPage, per_page: this.perPage })
        this.sites = data.data || []
        this.total = data.total || 0
        this.currentPage = data.current_page || 1
      } catch {
        this.$message.error('Siteler yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    async fetchAnalytics() {
      try {
        const { data } = await getAnalyticsSummary({ period: '7d' })
        this.analyticsData = data.data?.per_site || []
        this.analyticsLoaded = true
        this.$nextTick(() => {
          this.renderSparklines()
        })
      } catch {
        // GA not configured, skip silently
        this.analyticsLoaded = false
      }
    },
    getSiteAnalytics(siteId) {
      return this.analyticsMap[siteId] || null
    },
    renderSparklines() {
      // Destroy existing charts
      Object.values(this.sparkCharts).forEach((c) => c.destroy())
      this.sparkCharts = {}

      this.sites.forEach((site) => {
        const analytics = this.getSiteAnalytics(site.id)
        if (!analytics || !analytics.daily || analytics.daily.length < 2) return

        const canvas = this.$refs['spark-' + site.id]
        if (!canvas) return
        const el = Array.isArray(canvas) ? canvas[0] : canvas
        if (!el) return

        const values = analytics.daily.map((d) => d.users)
        const isUp = values[values.length - 1] >= values[0]

        this.sparkCharts[site.id] = new Chart(el, {
          type: 'line',
          data: {
            labels: analytics.daily.map((d) => d.date),
            datasets: [{
              data: values,
              borderColor: isUp ? '#67c23a' : '#f56c6c',
              backgroundColor: isUp ? 'rgba(103,194,58,0.1)' : 'rgba(245,108,108,0.1)',
              borderWidth: 1.5,
              fill: true,
              tension: 0.4,
              pointRadius: 0,
              pointHitRadius: 0,
            }],
          },
          options: {
            responsive: false,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: {
              x: { display: false },
              y: { display: false },
            },
            animation: false,
          },
        })
      })
    },
    handleSearch() {
      // Client-side filtering
    },
    async handleDelete(site) {
      try {
        await this.$confirm(`"${site.domain}" sitesini silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`, 'Uyarı', {
          type: 'warning',
        })
        await deleteSite(site.id)
        this.$message.success('Site silindi')
        this.fetchSites()
      } catch (err) {
        if (err !== 'cancel') {
          this.$message.error('Silme başarısız')
        }
      }
    },
    resolveUrl(url) {
      if (!url) return ''
      if (url.startsWith('http')) return url
      const base = process.env.VUE_APP_API_BASE_URL || ''
      return base + url
    },
    formatDate(d) {
      if (!d) return ''
      return new Date(d).toLocaleDateString('tr-TR', { year: 'numeric', month: 'short', day: 'numeric' })
    },
    formatNumber(n) {
      if (n == null) return '-'
      return Number(n).toLocaleString('tr-TR')
    },
    sortByVisitors(a, b) {
      const aVal = this.getSiteAnalytics(a.id)?.active_users || 0
      const bVal = this.getSiteAnalytics(b.id)?.active_users || 0
      return aVal - bVal
    },
    sortByClicks(a, b) {
      const aVal = this.getSiteAnalytics(a.id)?.clicks || 0
      const bVal = this.getSiteAnalytics(b.id)?.clicks || 0
      return aVal - bVal
    },
  },
  beforeDestroy() {
    Object.values(this.sparkCharts).forEach((c) => c.destroy())
  },
}
</script>
