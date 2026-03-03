<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
      <h2 style="margin: 0">Siteler</h2>
      <el-button type="primary" icon="el-icon-plus" size="small" @click="$router.push('/sites/create')">Yeni Site</el-button>
    </div>

    <!-- Summary Stats -->
    <el-row :gutter="16" style="margin-bottom: 16px">
      <el-col :span="4">
        <el-card shadow="hover">
          <div class="summary-stat">
            <div class="summary-num">{{ sites.length }}</div>
            <div class="summary-label">Toplam Site</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="4">
        <el-card shadow="hover">
          <div class="summary-stat">
            <div class="summary-num" style="color: #67c23a">{{ activeSiteCount }}</div>
            <div class="summary-label">Aktif</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="4">
        <el-card shadow="hover">
          <div class="summary-stat">
            <div class="summary-num" style="color: #f56c6c">{{ inactiveSiteCount }}</div>
            <div class="summary-label">Pasif</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="4">
        <el-card shadow="hover" v-loading="analyticsLoading">
          <div class="summary-stat">
            <div class="summary-num" style="color: #e6a23c">{{ formatNumber(analyticsTotals.active_users) }}</div>
            <div class="summary-label">Ziyaretçi</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="4">
        <el-card shadow="hover" v-loading="analyticsLoading">
          <div class="summary-stat">
            <div class="summary-num" style="color: #909399">{{ formatNumber(analyticsTotals.page_views) }}</div>
            <div class="summary-label">Sayfa Gör.</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="4">
        <el-card shadow="hover" v-loading="analyticsLoading">
          <div class="summary-stat">
            <div class="summary-num" style="color: #67c23a">{{ formatNumber(analyticsTotals.clicks) }}</div>
            <div class="summary-label">Tıklama</div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px">
      <el-input
        v-model="search"
        placeholder="Ara..."
        prefix-icon="el-icon-search"
        clearable
        size="small"
        style="width: 240px"
        @input="handleSearch"
      />
      <el-radio-group v-model="period" size="small" @change="onPeriodChange">
        <el-radio-button label="7d">7 Gün</el-radio-button>
        <el-radio-button label="30d">30 Gün</el-radio-button>
        <el-radio-button label="90d">90 Gün</el-radio-button>
      </el-radio-group>
    </div>

    <div v-loading="loading" class="site-grid">
      <div
        v-for="row in filteredSites"
        :key="row.id"
        class="site-card"
        @click="$router.push(`/sites/${row.id}`)"
      >
        <!-- Header: Logo + Domain + Status -->
        <div class="site-card-header">
          <div class="site-identity">
            <img
              v-if="row.logo_url"
              :src="resolveUrl(row.logo_url)"
              class="site-logo"
              :alt="row.domain"
            />
            <span v-else class="site-logo-placeholder">
              <i class="el-icon-monitor"></i>
            </span>
            <div>
              <div class="site-domain">{{ row.domain }}</div>
              <div class="site-name">{{ row.name }}</div>
            </div>
          </div>
          <span :class="['status-dot', row.is_active ? 'active' : 'inactive']"></span>
        </div>

        <!-- Sparkline -->
        <div class="site-card-chart">
          <canvas
            v-if="getSiteAnalytics(row.id) && getSiteAnalytics(row.id).daily && getSiteAnalytics(row.id).daily.length > 1"
            :ref="'spark-' + row.id"
            width="260"
            height="40"
          ></canvas>
          <div v-else class="chart-empty"></div>
        </div>

        <!-- Stats -->
        <div class="site-card-stats">
          <div class="stat-item">
            <span class="stat-value visitors">{{ getSiteAnalytics(row.id) ? formatNumber(getSiteAnalytics(row.id).active_users) : '-' }}</span>
            <span class="stat-label">Ziyaretçi</span>
          </div>
          <div class="stat-item">
            <span class="stat-value pageviews">{{ getSiteAnalytics(row.id) ? formatNumber(getSiteAnalytics(row.id).page_views) : '-' }}</span>
            <span class="stat-label">Sayfa</span>
          </div>
          <div class="stat-item">
            <span class="stat-value clicks">{{ getSiteAnalytics(row.id) && getSiteAnalytics(row.id).clicks > 0 ? formatNumber(getSiteAnalytics(row.id).clicks) : '-' }}</span>
            <span class="stat-label">Tıklama</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="site-card-actions" @click.stop>
          <el-button type="text" size="mini" @click="$router.push(`/sites/${row.id}/edit`)">
            <i class="el-icon-edit"></i> Düzenle
          </el-button>
          <el-button type="text" size="mini" style="color: #f56c6c" @click="handleDelete(row)">
            <i class="el-icon-delete"></i> Sil
          </el-button>
        </div>
      </div>
    </div>

    <el-pagination
      v-if="total > perPage"
      style="margin-top: 20px; text-align: center"
      layout="prev, pager, next"
      :total="total"
      :page-size="perPage"
      :current-page.sync="currentPage"
      @current-change="fetchSites"
    />
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
      perPage: 18,
      total: 0,
      period: '7d',
      analyticsData: [],
      analyticsTotals: { active_users: 0, page_views: 0, sessions: 0, clicks: 0, impressions: 0 },
      analyticsLoading: false,
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
    activeSiteCount() {
      return this.sites.filter((s) => s.is_active).length
    },
    inactiveSiteCount() {
      return this.sites.filter((s) => !s.is_active).length
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
      this.analyticsLoading = true
      try {
        const { data } = await getAnalyticsSummary({ period: this.period })
        this.analyticsData = data.data?.per_site || []
        this.analyticsTotals = data.data?.totals || { active_users: 0, page_views: 0, sessions: 0, clicks: 0, impressions: 0 }
        this.analyticsLoaded = true
        this.$nextTick(() => {
          this.renderSparklines()
        })
      } catch {
        this.analyticsLoaded = false
      } finally {
        this.analyticsLoading = false
      }
    },
    onPeriodChange() {
      this.fetchAnalytics()
    },
    getSiteAnalytics(siteId) {
      return this.analyticsMap[siteId] || null
    },
    renderSparklines() {
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
        const color = isUp ? '#67c23a' : '#f56c6c'
        const bg = isUp ? 'rgba(103,194,58,0.08)' : 'rgba(245,108,108,0.08)'

        this.sparkCharts[site.id] = new Chart(el, {
          type: 'line',
          data: {
            labels: analytics.daily.map((d) => d.date),
            datasets: [{
              data: values,
              borderColor: color,
              backgroundColor: bg,
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
              y: { display: false, beginAtZero: true },
            },
            animation: false,
          },
        })
      })
    },
    handleSearch() {},
    async handleDelete(site) {
      try {
        await this.$confirm(`"${site.domain}" sitesini silmek istediğinize emin misiniz?`, 'Uyarı', { type: 'warning' })
        await deleteSite(site.id)
        this.$message.success('Site silindi')
        this.fetchSites()
      } catch (err) {
        if (err !== 'cancel') this.$message.error('Silme başarısız')
      }
    },
    resolveUrl(url) {
      if (!url) return ''
      if (url.startsWith('http')) return url
      const base = process.env.VUE_APP_API_BASE_URL || ''
      return base + url
    },
    formatNumber(n) {
      if (n == null) return '-'
      return Number(n).toLocaleString('tr-TR')
    },
  },
  beforeDestroy() {
    Object.values(this.sparkCharts).forEach((c) => c.destroy())
  },
}
</script>

<style scoped>
.summary-stat { text-align: center; padding: 8px 0; }
.summary-num { font-size: 24px; font-weight: 700; color: #409eff; }
.summary-label { font-size: 11px; color: #909399; margin-top: 2px; }

.site-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 16px;
}

.site-card {
  background: #fff;
  border: 1px solid #ebeef5;
  border-radius: 10px;
  padding: 16px;
  cursor: pointer;
  transition: box-shadow 0.2s, border-color 0.2s;
}
.site-card:hover {
  border-color: #d0d7de;
  box-shadow: 0 4px 16px rgba(0,0,0,0.06);
}

.site-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}
.site-identity {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}
.site-logo {
  width: 32px;
  height: 32px;
  object-fit: contain;
  border-radius: 6px;
  border: 1px solid #f0f0f0;
  flex-shrink: 0;
}
.site-logo-placeholder {
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #f5f7fa;
  border-radius: 6px;
  color: #c0c4cc;
  font-size: 16px;
  flex-shrink: 0;
}
.site-domain {
  font-size: 13px;
  font-weight: 600;
  color: #303133;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 180px;
}
.site-name {
  font-size: 11px;
  color: #909399;
  margin-top: 1px;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 4px;
}
.status-dot.active { background: #67c23a; }
.status-dot.inactive { background: #f56c6c; }

.site-card-chart {
  margin: 0 -4px 12px;
  height: 40px;
}
.site-card-chart canvas {
  display: block;
  width: 100% !important;
}
.chart-empty {
  height: 40px;
  background: linear-gradient(to right, #fafafa, #f5f7fa, #fafafa);
  border-radius: 4px;
}

.site-card-stats {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-top: 1px solid #f5f5f5;
  border-bottom: 1px solid #f5f5f5;
}
.stat-item {
  text-align: center;
  flex: 1;
}
.stat-value {
  display: block;
  font-size: 15px;
  font-weight: 600;
  line-height: 1.2;
}
.stat-value.visitors { color: #e6a23c; }
.stat-value.pageviews { color: #909399; }
.stat-value.clicks { color: #67c23a; }
.stat-label {
  display: block;
  font-size: 10px;
  color: #b0b4bb;
  margin-top: 2px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.site-card-actions {
  display: flex;
  justify-content: flex-end;
  gap: 4px;
  margin-top: 10px;
}
.site-card-actions .el-button--mini {
  font-size: 12px;
  padding: 4px 8px;
}
</style>
