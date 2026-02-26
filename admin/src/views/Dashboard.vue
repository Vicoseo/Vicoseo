<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
      <h2 style="margin: 0">Panel</h2>
      <el-radio-group v-model="period" size="small" @change="onPeriodChange">
        <el-radio-button label="7d">7 Gün</el-radio-button>
        <el-radio-button label="30d">30 Gün</el-radio-button>
        <el-radio-button label="90d">90 Gün</el-radio-button>
      </el-radio-group>
    </div>

    <!-- Site Count Stats -->
    <el-row :gutter="16" style="margin-bottom: 16px">
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

    <!-- Analytics Stats -->
    <el-row :gutter="16" style="margin-bottom: 20px">
      <el-col :span="6">
        <el-card shadow="hover" v-loading="analyticsLoading">
          <div class="stat-card">
            <div class="stat-number stat-visitors">{{ formatNumber(analyticsTotals.active_users) }}</div>
            <div class="stat-label">Ziyaretçi</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="hover" v-loading="analyticsLoading">
          <div class="stat-card">
            <div class="stat-number stat-pageviews">{{ formatNumber(analyticsTotals.page_views) }}</div>
            <div class="stat-label">Sayfa Görüntüleme</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="hover" v-loading="analyticsLoading">
          <div class="stat-card">
            <div class="stat-number stat-clicks">{{ formatNumber(analyticsTotals.clicks) }}</div>
            <div class="stat-label">Google Tıklama</div>
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card shadow="hover" v-loading="analyticsLoading">
          <div class="stat-card">
            <div class="stat-number stat-impressions">{{ formatNumber(analyticsTotals.impressions) }}</div>
            <div class="stat-label">Gösterim</div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- Site Comparison Bar Chart -->
    <el-card style="margin-bottom: 20px" v-loading="analyticsLoading">
      <div slot="header">
        <span>Site Ziyaretçi Karşılaştırması</span>
      </div>
      <div v-if="perSiteData.length > 0" style="position: relative; height: 350px">
        <canvas ref="barChart"></canvas>
      </div>
      <div v-else style="text-align: center; padding: 40px 0; color: #909399">
        <i class="el-icon-data-analysis" style="font-size: 40px; margin-bottom: 8px; display: block"></i>
        Analitik verisi bulunamadı. Google Analytics yapılandırmasını kontrol edin.
      </div>
    </el-card>

    <!-- Bulk Operations -->
    <el-card style="margin-bottom: 20px">
      <div slot="header">
        <span>Toplu İşlemler</span>
      </div>
      <el-button type="success" icon="el-icon-magic-stick" :loading="bulkLoading" @click="showBulkDialog = true">
        Tüm Siteler İçin İçerik Oluştur
      </el-button>
    </el-card>

    <!-- Bulk Content Dialog -->
    <el-dialog title="Toplu İçerik Üretimi" :visible.sync="showBulkDialog" width="500px" :close-on-click-modal="false">
      <el-form label-width="130px" v-if="!bulkBatchId">
        <el-form-item label="AI Sağlayıcı">
          <el-radio-group v-model="bulkForm.provider">
            <el-radio label="openai">ChatGPT</el-radio>
            <el-radio label="anthropic">Claude</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="İçerik Türü">
          <el-radio-group v-model="bulkForm.content_type">
            <el-radio label="all">Hepsi</el-radio>
            <el-radio label="pages">Sayfalar</el-radio>
            <el-radio label="posts">Yazılar</el-radio>
            <el-radio label="daily">Günlük</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="Mevcut İçerik">
          <el-switch v-model="bulkForm.overwrite" active-text="Üzerine Yaz" inactive-text="Atla" />
        </el-form-item>
        <el-form-item label="Günlük Sayısı" v-if="bulkForm.content_type === 'daily' || bulkForm.content_type === 'all'">
          <el-input-number v-model="bulkForm.daily_count" :min="1" :max="10" />
        </el-form-item>
      </el-form>

      <!-- Progress View -->
      <div v-if="bulkBatchId">
        <el-progress :percentage="bulkProgress.overall_progress || 0" :status="bulkProgressStatus" style="margin-bottom: 16px" />
        <div style="margin-bottom: 8px; font-size: 14px; color: #606266">
          Toplam: {{ bulkProgress.total || 0 }} | Tamamlanan: {{ bulkProgress.completed || 0 }} | Hatalı: {{ bulkProgress.failed || 0 }}
        </div>
        <el-table :data="bulkProgress.tasks || []" size="mini" max-height="300">
          <el-table-column prop="domain" label="Site" />
          <el-table-column label="Durum" width="100">
            <template slot-scope="{ row }">
              <el-tag :type="bulkStatusTag(row.status)" size="mini">{{ bulkStatusLabel(row.status) }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="progress" label="%" width="60" />
          <el-table-column label="Sonuç" width="140">
            <template slot-scope="{ row }">
              <span v-if="row.result">{{ row.result.created || 0 }} oluşturuldu</span>
              <span v-if="row.error" style="color: #f56c6c">{{ row.error.substring(0, 40) }}</span>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <span slot="footer">
        <el-button @click="closeBulkDialog">{{ bulkBatchId ? 'Kapat' : 'İptal' }}</el-button>
        <el-button v-if="!bulkBatchId" type="primary" :loading="bulkLoading" @click="startBulkContent">Başlat</el-button>
      </span>
    </el-dialog>

    <!-- Recent Sites -->
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
import { Chart, BarController, BarElement, LinearScale, CategoryScale, Tooltip, Legend } from 'chart.js'
import { getSites, startBulkContent, getBulkContentProgress } from '../api/sites'
import { getAnalyticsSummary } from '../api/analytics'

Chart.register(BarController, BarElement, LinearScale, CategoryScale, Tooltip, Legend)

export default {
  name: 'Dashboard',
  data() {
    return {
      sites: [],
      period: '7d',
      analyticsTotals: { active_users: 0, page_views: 0, sessions: 0, clicks: 0, impressions: 0 },
      perSiteData: [],
      analyticsLoading: false,
      loading: false,
      showBulkDialog: false,
      bulkLoading: false,
      bulkBatchId: null,
      bulkProgress: {},
      bulkPollTimer: null,
      bulkForm: {
        provider: 'openai',
        content_type: 'all',
        overwrite: false,
        daily_count: 2,
      },
      barChart: null,
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
    bulkProgressStatus() {
      if (!this.bulkProgress.is_finished) return null
      return this.bulkProgress.failed > 0 ? 'exception' : 'success'
    },
  },
  created() {
    this.fetchSites()
    this.fetchAnalytics()
  },
  methods: {
    async fetchAnalytics() {
      this.analyticsLoading = true
      try {
        const { data } = await getAnalyticsSummary({ period: this.period })
        this.analyticsTotals = data.data?.totals || { active_users: 0, page_views: 0, sessions: 0, clicks: 0, impressions: 0 }
        this.perSiteData = (data.data?.per_site || []).filter((s) => !s.ga_error)
        this.$nextTick(() => {
          this.renderBarChart()
        })
      } catch {
        // GA not configured, skip silently
      } finally {
        this.analyticsLoading = false
      }
    },
    onPeriodChange() {
      this.fetchAnalytics()
    },
    renderBarChart() {
      if (this.barChart) {
        this.barChart.destroy()
        this.barChart = null
      }

      const canvas = this.$refs.barChart
      if (!canvas || this.perSiteData.length === 0) return

      const sorted = [...this.perSiteData].sort((a, b) => (b.active_users || 0) - (a.active_users || 0))
      const labels = sorted.map((s) => s.domain.replace(/\.(com|net|me|click|site|online|one|link)$/, ''))
      const visitors = sorted.map((s) => s.active_users || 0)
      const clicks = sorted.map((s) => s.clicks || 0)

      this.barChart = new Chart(canvas, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              label: 'Ziyaretçi',
              data: visitors,
              backgroundColor: 'rgba(64, 158, 255, 0.7)',
              borderColor: '#409eff',
              borderWidth: 1,
              borderRadius: 3,
            },
            {
              label: 'Google Tıklama',
              data: clicks,
              backgroundColor: 'rgba(103, 194, 58, 0.7)',
              borderColor: '#67c23a',
              borderWidth: 1,
              borderRadius: 3,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
              labels: { font: { size: 12 }, usePointStyle: true, pointStyle: 'rectRounded' },
            },
            tooltip: {
              callbacks: {
                title: (items) => {
                  const idx = items[0].dataIndex
                  return sorted[idx].domain
                },
              },
            },
          },
          scales: {
            x: {
              ticks: { font: { size: 10 }, maxRotation: 45, minRotation: 30 },
              grid: { display: false },
            },
            y: {
              beginAtZero: true,
              ticks: { font: { size: 11 } },
              grid: { color: 'rgba(0,0,0,0.05)' },
            },
          },
        },
      })
    },
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
    formatNumber(n) {
      if (n == null || n === 0) return '0'
      return Number(n).toLocaleString('tr-TR')
    },
    async startBulkContent() {
      this.bulkLoading = true
      try {
        const { data } = await startBulkContent(this.bulkForm)
        this.bulkBatchId = data.batch_id
        this.$message.success(data.message)
        this.pollBulkProgress()
      } catch (err) {
        const msg = err.response?.data?.message || 'Toplu içerik başlatılamadı'
        this.$message.error(msg)
      } finally {
        this.bulkLoading = false
      }
    },
    async pollBulkProgress() {
      if (!this.bulkBatchId) return
      try {
        const { data } = await getBulkContentProgress(this.bulkBatchId)
        this.bulkProgress = data
        if (!data.is_finished) {
          this.bulkPollTimer = setTimeout(() => this.pollBulkProgress(), 3000)
        }
      } catch {
        // silently retry
        this.bulkPollTimer = setTimeout(() => this.pollBulkProgress(), 5000)
      }
    },
    closeBulkDialog() {
      this.showBulkDialog = false
      if (this.bulkPollTimer) {
        clearTimeout(this.bulkPollTimer)
        this.bulkPollTimer = null
      }
      this.bulkBatchId = null
      this.bulkProgress = {}
    },
    bulkStatusTag(status) {
      const map = { completed: 'success', failed: 'danger', processing: 'warning', pending: 'info' }
      return map[status] || 'info'
    },
    bulkStatusLabel(status) {
      const map = { completed: 'Tamam', failed: 'Hata', processing: 'İşleniyor', pending: 'Bekliyor' }
      return map[status] || status
    },
  },
  beforeDestroy() {
    if (this.bulkPollTimer) clearTimeout(this.bulkPollTimer)
    if (this.barChart) this.barChart.destroy()
  },
}
</script>

<style scoped>
.stat-card {
  text-align: center;
  padding: 10px 0;
}
.stat-number {
  font-size: 32px;
  font-weight: 700;
  color: #409eff;
}
.stat-label {
  font-size: 13px;
  color: #909399;
  margin-top: 4px;
}
.stat-visitors { color: #e6a23c; }
.stat-pageviews { color: #909399; }
.stat-clicks { color: #67c23a; }
.stat-impressions { color: #409eff; }
</style>
