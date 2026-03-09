<template>
  <div class="domain-manager">
    <!-- Balance Card -->
    <el-card class="balance-card" shadow="hover">
      <div slot="header" class="card-header">
        <span>Namesilo Hesap Bakiyesi</span>
        <el-button size="small" icon="el-icon-refresh" @click="fetchBalance" :loading="balanceLoading">
          Yenile
        </el-button>
      </div>
      <div class="balance-amount">
        <span v-if="balance !== null">${{ balance.toFixed(2) }}</span>
        <span v-else class="text-muted">--</span>
      </div>
    </el-card>

    <!-- Domain Search -->
    <el-card shadow="hover" style="margin-top: 20px">
      <div slot="header"><span>Domain Arama &amp; Satın Alma</span></div>
      <el-form :inline="true" @submit.native.prevent="searchDomains">
        <el-form-item>
          <el-input v-model="searchQuery" placeholder="orneksite.com" clearable style="width: 320px">
            <template slot="prepend">www.</template>
          </el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="searchDomains" :loading="searchLoading" icon="el-icon-search">
            Ara
          </el-button>
        </el-form-item>
      </el-form>

      <el-table v-if="searchResults.length" :data="searchResults" style="margin-top: 15px">
        <el-table-column prop="domain" label="Domain" />
        <el-table-column label="Durum" width="120">
          <template slot-scope="{ row }">
            <el-tag :type="row.available ? 'success' : 'danger'" size="small">
              {{ row.available ? 'Müsait' : 'Alınmış' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Fiyat" width="100">
          <template slot-scope="{ row }">
            {{ row.available && row.price ? '$' + row.price.toFixed(2) : '-' }}
          </template>
        </el-table-column>
        <el-table-column label="İşlem" width="280">
          <template slot-scope="{ row }">
            <div class="action-buttons">
              <el-button v-if="row.available" size="small" type="success" @click="buyDomain(row)" :loading="row._buying">
                Satın Al
              </el-button>
              <el-button v-if="row.available" size="small" type="primary" @click="startSetup(row.domain)">
                Hızlı Kurulum
              </el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- Quick Setup -->
    <el-card shadow="hover" style="margin-top: 20px">
      <div slot="header"><span>Hızlı Kurulum (Full Setup)</span></div>
      <el-form :model="setupForm" label-width="120px" style="max-width: 600px">
        <el-form-item label="Domain">
          <el-input v-model="setupForm.domain" placeholder="orneksite.com" />
        </el-form-item>
        <el-form-item label="Site Adı">
          <el-input v-model="setupForm.site_name" placeholder="Örnek Site" />
        </el-form-item>
        <el-form-item label="Sunucu IP">
          <el-input v-model="setupForm.server_ip" placeholder="84.32.109.211" />
        </el-form-item>
        <el-form-item>
          <el-button
            type="primary"
            @click="runSetup"
            :loading="setupLoading"
            :disabled="!setupForm.domain || !setupForm.site_name"
            icon="el-icon-s-promotion"
          >
            Kurulumu Başlat
          </el-button>
        </el-form-item>
      </el-form>
      <div v-if="setupSteps.length" class="setup-progress">
        <el-steps :active="activeSetupStep" finish-status="success" align-center>
          <el-step
            v-for="(step, idx) in setupStepLabels"
            :key="idx"
            :title="step.label"
            :status="getStepStatus(step.key)"
            :description="getStepMessage(step.key)"
          />
        </el-steps>
        <div v-if="setupResult" class="setup-result" style="margin-top: 20px">
          <el-alert
            :title="setupResult.success ? 'Kurulum Tamamlandı!' : 'Kurulum Hatası'"
            :type="setupResult.success ? 'success' : 'error'"
            :description="setupResult.message"
            show-icon
            :closable="false"
          />
          <div v-if="setupResult.nameservers && setupResult.nameservers.length" style="margin-top: 10px">
            <strong>Nameserver'lar:</strong>
            <el-tag v-for="ns in setupResult.nameservers" :key="ns" size="small" style="margin: 2px">{{ ns }}</el-tag>
          </div>
        </div>
      </div>
    </el-card>

    <!-- Domain Listesi -->
    <el-card shadow="hover" style="margin-top: 20px">
      <div slot="header" class="card-header">
        <span>Domainler ({{ zones.length }})</span>
        <div>
          <el-button
            v-if="pendingZones.length > 0"
            size="small"
            type="warning"
            icon="el-icon-refresh-right"
            @click="fixAllPending"
            :loading="fixAllLoading"
          >
            Tüm Pending'leri Düzelt ({{ pendingZones.length }})
          </el-button>
          <el-button size="small" icon="el-icon-refresh" @click="fetchZones" :loading="zonesLoading">
            Yenile
          </el-button>
        </div>
      </div>

      <el-table
        :data="zones"
        v-loading="zonesLoading"
        highlight-current-row
        @row-click="openDrawer"
        class="domain-table"
      >
        <el-table-column label="Domain" min-width="200">
          <template slot-scope="{ row }">
            <span class="domain-name">{{ row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="CF" width="80" align="center">
          <template slot-scope="{ row }">
            <span :class="['status-dot', row.status === 'active' ? 'dot-green' : 'dot-orange']" />
          </template>
        </el-table-column>
        <el-table-column label="DNS" width="80" align="center">
          <template slot-scope="{ row }">
            <span :class="['status-dot', getDomainDnsStatus(row.name) === true ? 'dot-green' : getDomainDnsStatus(row.name) === false ? 'dot-red' : 'dot-gray']" />
          </template>
        </el-table-column>
        <el-table-column label="301 Redirect" width="200">
          <template slot-scope="{ row }">
            <span v-if="getSiteRedirect(row.name)" class="redirect-badge">
              → {{ getSiteRedirect(row.name) }}
            </span>
            <span v-else class="no-redirect">—</span>
          </template>
        </el-table-column>
      </el-table>

      <div class="table-legend">
        <span><span class="status-dot dot-green" /> Aktif</span>
        <span><span class="status-dot dot-orange" /> Pending</span>
        <span><span class="status-dot dot-red" /> Hata</span>
        <span><span class="status-dot dot-gray" /> Bilinmiyor</span>
        <span style="margin-left: auto; color: #909399; font-size: 12px">Satıra tıklayarak detay panelini aç</span>
      </div>
    </el-card>

    <!-- Domain Detail Drawer -->
    <el-drawer
      :visible.sync="drawerVisible"
      :title="drawerDomain"
      direction="rtl"
      size="520px"
      :before-close="closeDrawer"
    >
      <div class="drawer-content" v-loading="drawerLoading">
        <!-- Navigation -->
        <div class="drawer-nav">
          <el-button size="small" icon="el-icon-arrow-left" :disabled="drawerIndex <= 0" @click="navigateDomain(-1)">
            Önceki
          </el-button>
          <span class="drawer-counter">{{ drawerIndex + 1 }} / {{ zones.length }}</span>
          <el-button size="small" :disabled="drawerIndex >= zones.length - 1" @click="navigateDomain(1)">
            Sonraki
            <i class="el-icon-arrow-right" />
          </el-button>
        </div>

        <!-- Status Badges -->
        <div class="drawer-status">
          <el-tag :type="drawerZoneStatus === 'active' ? 'success' : 'warning'" size="small" effect="dark">
            CF: {{ drawerZoneStatus }}
          </el-tag>
          <el-tag :type="drawerDnsResolved ? 'success' : 'danger'" size="small" effect="dark">
            DNS: {{ drawerDnsResolved ? 'OK' : 'FAIL' }}
          </el-tag>
          <el-tag v-if="drawerHasCmsSite" size="small" type="info" effect="dark">
            CMS: Aktif
          </el-tag>
          <el-tag v-else size="small" type="danger" effect="dark">
            CMS: Yok
          </el-tag>
        </div>

        <!-- 301 Redirect Section -->
        <div class="drawer-section">
          <div class="section-title">301 Domain Redirect</div>
          <div class="redirect-row">
            <el-input
              v-model="drawerRedirect"
              placeholder="yenidomain.com"
              size="small"
              clearable
              style="flex: 1"
            />
            <el-button
              size="small"
              type="primary"
              @click="saveDrawerRedirect"
              :loading="drawerRedirectSaving"
              :disabled="drawerRedirect === drawerRedirectOriginal"
            >
              Kaydet
            </el-button>
          </div>
          <div class="section-hint">
            BTK engellemesinde yeni domain yaz. Tüm trafik 301 ile yönlendirilir (path korunur).
          </div>
        </div>

        <!-- DNS Records -->
        <div class="drawer-section">
          <div class="section-title" style="display: flex; justify-content: space-between; align-items: center">
            <span>DNS Kayıtları ({{ drawerDnsRecords.length }})</span>
            <el-button size="mini" icon="el-icon-plus" @click="openDnsDialog(zones[drawerIndex])">
              Ekle
            </el-button>
          </div>
          <el-table :data="drawerDnsRecords" size="mini" border stripe empty-text="DNS kaydı yok" style="margin-top: 8px">
            <el-table-column prop="type" label="Tip" width="60" />
            <el-table-column prop="name" label="Ad" show-overflow-tooltip>
              <template slot-scope="{ row }">
                <span style="font-family: monospace; font-size: 12px">{{ row.name }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="content" label="Değer" show-overflow-tooltip>
              <template slot-scope="{ row }">
                <span style="font-family: monospace; font-size: 12px">{{ row.content }}</span>
              </template>
            </el-table-column>
            <el-table-column label="P" width="40" align="center">
              <template slot-scope="{ row }">
                <i :class="row.proxied ? 'el-icon-check' : 'el-icon-close'" :style="{ color: row.proxied ? '#67c23a' : '#c0c4cc' }" />
              </template>
            </el-table-column>
          </el-table>
        </div>

        <!-- Quick Actions -->
        <div class="drawer-section">
          <div class="section-title">Hızlı İşlemler</div>
          <div class="quick-actions">
            <el-button size="small" icon="el-icon-refresh" @click="refreshDrawer">
              Yenile
            </el-button>
            <el-button size="small" type="info" icon="el-icon-view" @click="checkZoneStatus(zones[drawerIndex])">
              CF Durum Kontrol
            </el-button>
            <el-button
              v-if="drawerZoneStatus !== 'active'"
              size="small"
              type="warning"
              icon="el-icon-s-promotion"
              @click="fixPending(zones[drawerIndex])"
              :loading="zones[drawerIndex] && zones[drawerIndex]._fixing"
            >
              Aktifleştir
            </el-button>
          </div>
        </div>

        <!-- Next Domain Button -->
        <div class="drawer-next" v-if="drawerIndex < zones.length - 1">
          <el-button type="primary" style="width: 100%" @click="navigateDomain(1)">
            Sonraki Domain: {{ zones[drawerIndex + 1] && zones[drawerIndex + 1].name }} →
          </el-button>
        </div>
      </div>
    </el-drawer>

    <!-- DNS Add Dialog -->
    <el-dialog title="DNS Kaydı Ekle" :visible.sync="dnsDialogVisible" width="500px" append-to-body>
      <el-form :model="dnsForm" label-width="100px">
        <el-form-item label="Zone">
          <el-input :value="dnsForm.zoneName" disabled />
        </el-form-item>
        <el-form-item label="Tip">
          <el-select v-model="dnsForm.type" style="width: 100%">
            <el-option label="A" value="A" />
            <el-option label="AAAA" value="AAAA" />
            <el-option label="CNAME" value="CNAME" />
            <el-option label="MX" value="MX" />
            <el-option label="TXT" value="TXT" />
          </el-select>
        </el-form-item>
        <el-form-item label="Ad">
          <el-input v-model="dnsForm.name" placeholder="@ veya subdomain" />
        </el-form-item>
        <el-form-item label="İçerik">
          <el-input v-model="dnsForm.content" placeholder="IP adresi veya değer" />
        </el-form-item>
        <el-form-item label="Proxied">
          <el-switch v-model="dnsForm.proxied" />
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button @click="dnsDialogVisible = false">İptal</el-button>
        <el-button type="primary" @click="submitDns" :loading="dnsLoading">Ekle</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import {
  getBalance,
  searchDomain,
  purchaseDomain,
  fullSetup,
  getCfZones,
  getCfZoneDetail,
  listDnsRecords,
  addDnsRecord,
  fixPendingZone,
  getDomainStatus,
} from '../../api/domains'
import { getSites, updateSite } from '../../api/sites'

const SETUP_STEP_LABELS = [
  { key: 'cloudflare_zone', label: "Cloudflare'e ekleniyor" },
  { key: 'nameservers', label: 'NS güncelleniyor' },
  { key: 'dns_records', label: 'DNS kayıtları' },
  { key: 'ssl', label: 'SSL ayarlanıyor' },
  { key: 'cms_site', label: 'CMS site oluşturuluyor' },
  { key: 'nginx', label: 'Nginx yapılandırılıyor' },
]

export default {
  name: 'DomainManager',

  data() {
    return {
      // Balance
      balance: null,
      balanceLoading: false,

      // Search
      searchQuery: '',
      searchResults: [],
      searchLoading: false,

      // Setup
      setupForm: { domain: '', site_name: '', server_ip: '' },
      setupLoading: false,
      setupSteps: [],
      setupResult: null,
      setupStepLabels: SETUP_STEP_LABELS,

      // Zones
      zones: [],
      zonesLoading: false,
      fixAllLoading: false,

      // Sites (for redirect mapping)
      sites: [],
      dnsStatusCache: {},

      // Drawer
      drawerVisible: false,
      drawerLoading: false,
      drawerIndex: 0,
      drawerDomain: '',
      drawerZoneStatus: '',
      drawerDnsResolved: false,
      drawerHasCmsSite: false,
      drawerDnsRecords: [],
      drawerRedirect: '',
      drawerRedirectOriginal: '',
      drawerRedirectSaving: false,

      // DNS dialog
      dnsDialogVisible: false,
      dnsLoading: false,
      dnsForm: { zoneId: '', zoneName: '', type: 'A', name: '', content: '', proxied: true },
    }
  },

  computed: {
    activeSetupStep() {
      if (!this.setupSteps.length) return 0
      for (let i = this.setupSteps.length - 1; i >= 0; i--) {
        if (this.setupSteps[i].status === 'done') return i + 1
      }
      return 0
    },
    pendingZones() {
      return this.zones.filter((z) => z.status !== 'active')
    },
  },

  created() {
    this.fetchBalance()
    this.fetchZones()
    this.fetchSites()
  },

  methods: {
    // ─── Sites & Redirect helpers ───
    async fetchSites() {
      try {
        const { data } = await getSites({ per_page: 100 })
        this.sites = data.data || []
      } catch {
        // silent
      }
    },

    getSiteByDomain(domain) {
      return this.sites.find((s) => s.domain === domain)
    },

    getSiteRedirect(domain) {
      const site = this.getSiteByDomain(domain)
      return site?.fallback_domain || ''
    },

    getDomainDnsStatus(domain) {
      if (domain in this.dnsStatusCache) return this.dnsStatusCache[domain]
      return null // unknown
    },

    // ─── Drawer ───
    async openDrawer(zone) {
      const idx = this.zones.findIndex((z) => z.zone_id === zone.zone_id)
      this.drawerIndex = idx >= 0 ? idx : 0
      this.drawerVisible = true
      await this.loadDrawerData(zone)
    },

    closeDrawer(done) {
      done()
    },

    async navigateDomain(direction) {
      const newIdx = this.drawerIndex + direction
      if (newIdx < 0 || newIdx >= this.zones.length) return
      this.drawerIndex = newIdx
      await this.loadDrawerData(this.zones[newIdx])
    },

    async loadDrawerData(zone) {
      this.drawerDomain = zone.name
      this.drawerZoneStatus = zone.status
      this.drawerDnsRecords = []
      this.drawerDnsResolved = false
      this.drawerHasCmsSite = false
      this.drawerLoading = true

      const site = this.getSiteByDomain(zone.name)
      this.drawerHasCmsSite = !!site
      this.drawerRedirect = site?.fallback_domain || ''
      this.drawerRedirectOriginal = this.drawerRedirect

      try {
        const [dnsRes, statusRes] = await Promise.all([
          listDnsRecords(zone.zone_id),
          getDomainStatus(zone.name),
        ])
        this.drawerDnsRecords = dnsRes.data?.data || []
        this.drawerDnsResolved = statusRes.data?.dns_resolved || false
        this.$set(this.dnsStatusCache, zone.name, this.drawerDnsResolved)
        if (statusRes.data?.cloudflare?.status) {
          this.drawerZoneStatus = statusRes.data.cloudflare.status
        }
        if (statusRes.data?.cms_site) {
          this.drawerHasCmsSite = true
        }
      } catch {
        this.$message.error('Bilgiler yüklenemedi.')
      } finally {
        this.drawerLoading = false
      }
    },

    async refreshDrawer() {
      if (!this.zones[this.drawerIndex]) return
      await this.loadDrawerData(this.zones[this.drawerIndex])
      this.$message.success('Yenilendi')
    },

    async saveDrawerRedirect() {
      const site = this.getSiteByDomain(this.drawerDomain)
      if (!site) {
        this.$message.warning('Bu domain için CMS sitesi bulunamadı.')
        return
      }
      this.drawerRedirectSaving = true
      try {
        await updateSite(site.id, { fallback_domain: this.drawerRedirect || null })
        this.drawerRedirectOriginal = this.drawerRedirect
        this.$message.success(
          this.drawerRedirect ? `301 → ${this.drawerRedirect} ayarlandı` : 'Redirect kaldırıldı',
        )
        this.fetchSites()
      } catch (e) {
        this.$message.error(e.response?.data?.message || 'Kaydedilemedi.')
      } finally {
        this.drawerRedirectSaving = false
      }
    },

    // ─── Balance ───
    async fetchBalance() {
      this.balanceLoading = true
      try {
        const { data } = await getBalance()
        this.balance = data.balance
      } catch {
        this.$message.error('Bakiye alınamadı.')
      } finally {
        this.balanceLoading = false
      }
    },

    // ─── Search & Buy ───
    async searchDomains() {
      if (!this.searchQuery.trim()) return
      this.searchLoading = true
      this.searchResults = []
      try {
        const { data } = await searchDomain(this.searchQuery.trim())
        this.searchResults = (data.data || []).map((r) => ({ ...r, _buying: false }))
      } catch {
        this.$message.error('Arama başarısız.')
      } finally {
        this.searchLoading = false
      }
    },

    async buyDomain(row) {
      try {
        await this.$confirm(
          `${row.domain} domaini $${row.price?.toFixed(2) || '?'} ücretine satın alınacak. Onaylıyor musunuz?`,
          'Satın Alma Onayı',
          { type: 'warning' },
        )
      } catch {
        return
      }
      row._buying = true
      try {
        const { data } = await purchaseDomain({ domain: row.domain })
        if (data.success) {
          this.$message.success(data.message || 'Domain satın alındı!')
          row.available = false
          this.fetchBalance()
        } else {
          this.$message.error(data.message || 'Satın alma başarısız.')
        }
      } catch (e) {
        this.$message.error(e.response?.data?.message || 'Satın alma hatası.')
      } finally {
        row._buying = false
      }
    },

    startSetup(domain) {
      this.setupForm.domain = domain
      this.setupForm.site_name = domain.replace(/\.[^.]+$/, '').replace(/[^a-zA-Z0-9]/g, ' ')
      this.$nextTick(() => {
        const el = document.querySelector('.setup-progress')
        if (el) el.scrollIntoView({ behavior: 'smooth' })
      })
    },

    // ─── Setup ───
    async runSetup() {
      if (!this.setupForm.domain || !this.setupForm.site_name) return
      this.setupLoading = true
      this.setupResult = null
      this.setupSteps = SETUP_STEP_LABELS.map((s) => ({ step: s.key, status: 'pending' }))
      try {
        const payload = { domain: this.setupForm.domain, site_name: this.setupForm.site_name }
        if (this.setupForm.server_ip) payload.server_ip = this.setupForm.server_ip
        const { data } = await fullSetup(payload)
        this.setupSteps = data.steps || []
        this.setupResult = data
        if (data.success) {
          this.$message.success('Kurulum tamamlandı!')
          this.fetchZones()
        } else {
          this.$message.warning(data.message || 'Kurulum kısmen başarısız.')
        }
      } catch (e) {
        const errData = e.response?.data
        if (errData?.steps) this.setupSteps = errData.steps
        this.setupResult = { success: false, message: errData?.message || 'Kurulum hatası.' }
        this.$message.error(errData?.message || 'Kurulum hatası.')
      } finally {
        this.setupLoading = false
      }
    },

    getStepStatus(key) {
      const step = this.setupSteps.find((s) => s.step === key)
      if (!step) return 'wait'
      if (step.status === 'done') return 'success'
      if (step.status === 'error') return 'error'
      if (step.status === 'running') return 'process'
      if (step.status === 'warning') return 'warning'
      return 'wait'
    },

    getStepMessage(key) {
      const step = this.setupSteps.find((s) => s.step === key)
      return step?.message || ''
    },

    // ─── Zones ───
    async fetchZones() {
      this.zonesLoading = true
      try {
        const { data } = await getCfZones()
        this.zones = (data.data || []).map((z) => ({ ...z, _fixing: false }))
      } catch {
        this.$message.error('Zone listesi alınamadı.')
      } finally {
        this.zonesLoading = false
      }
    },

    async fixPending(zone) {
      zone._fixing = true
      try {
        const { data } = await fixPendingZone(zone.zone_id, zone.name)
        if (data.success) {
          this.$message.success(data.message || 'Aktivasyon tetiklendi!')
          if (data.nameservers && data.nameservers.length) {
            this.$notify({
              title: 'Nameserver Bilgisi',
              message: `${zone.name} için NS: ${data.nameservers.join(', ')}`,
              type: 'info',
              duration: 10000,
            })
          }
          setTimeout(() => this.fetchZones(), 3000)
        } else {
          this.$message.warning(data.message || 'Düzeltme kısmen başarısız.')
          if (data.nameservers && data.nameservers.length) {
            this.$alert(
              `Nameserver'ları registrar panelinizden manuel olarak güncelleyin:\n\n${data.nameservers.join('\n')}`,
              'Manuel NS Güncelleme Gerekli',
              { type: 'warning' },
            )
          }
        }
      } catch (e) {
        this.$message.error(e.response?.data?.message || 'Aktivasyon hatası.')
      } finally {
        zone._fixing = false
      }
    },

    async fixAllPending() {
      if (!this.pendingZones.length) return
      this.fixAllLoading = true
      let successCount = 0
      let failCount = 0
      for (const zone of this.pendingZones) {
        try {
          const { data } = await fixPendingZone(zone.zone_id, zone.name)
          data.success ? successCount++ : failCount++
        } catch {
          failCount++
        }
      }
      this.fixAllLoading = false
      this.$message.info(`${successCount} zone için aktivasyon tetiklendi, ${failCount} hata.`)
      setTimeout(() => this.fetchZones(), 5000)
    },

    async checkZoneStatus(zone) {
      try {
        const { data } = await getCfZoneDetail(zone.zone_id)
        if (data.success) {
          this.$message.info(`${zone.name}: ${data.status}`)
          const idx = this.zones.findIndex((z) => z.zone_id === zone.zone_id)
          if (idx !== -1) {
            this.$set(this.zones, idx, { ...this.zones[idx], status: data.status })
          }
          if (this.drawerVisible) {
            this.drawerZoneStatus = data.status
          }
        } else {
          this.$message.warning(data.message || 'Durum alınamadı.')
        }
      } catch {
        this.$message.error('Durum kontrolü başarısız.')
      }
    },

    // ─── DNS Dialog ───
    openDnsDialog(zone) {
      if (!zone) return
      this.dnsForm = {
        zoneId: zone.zone_id,
        zoneName: zone.name,
        type: 'A',
        name: zone.name,
        content: '',
        proxied: true,
      }
      this.dnsDialogVisible = true
    },

    async submitDns() {
      if (!this.dnsForm.name || !this.dnsForm.content) {
        this.$message.warning('Ad ve içerik alanları zorunludur.')
        return
      }
      this.dnsLoading = true
      try {
        const { data } = await addDnsRecord(this.dnsForm.zoneId, {
          type: this.dnsForm.type,
          name: this.dnsForm.name,
          content: this.dnsForm.content,
          proxied: this.dnsForm.proxied,
        })
        if (data.success) {
          this.$message.success('DNS kaydı eklendi.')
          this.dnsDialogVisible = false
          if (this.drawerVisible) this.refreshDrawer()
        } else {
          this.$message.error(data.message || 'DNS kaydı eklenemedi.')
        }
      } catch (e) {
        this.$message.error(e.response?.data?.message || 'DNS kaydı eklenemedi.')
      } finally {
        this.dnsLoading = false
      }
    },
  },
}
</script>

<style scoped>
.balance-card .balance-amount {
  font-size: 32px;
  font-weight: 700;
  color: #409eff;
}
.balance-card .balance-amount .text-muted {
  color: #c0c4cc;
}
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.setup-progress {
  margin-top: 25px;
  padding: 20px;
  background: #fafafa;
  border-radius: 4px;
}
.action-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
}

/* Domain Table */
.domain-table >>> .el-table__row {
  cursor: pointer;
}
.domain-name {
  font-weight: 600;
  color: #303133;
}
.status-dot {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 50%;
}
.dot-green { background: #67c23a; }
.dot-orange { background: #e6a23c; }
.dot-red { background: #f56c6c; }
.dot-gray { background: #c0c4cc; }
.redirect-badge {
  display: inline-block;
  padding: 2px 8px;
  background: #fdf6ec;
  color: #e6a23c;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}
.no-redirect {
  color: #c0c4cc;
}
.table-legend {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 10px 0 0;
  font-size: 12px;
  color: #606266;
}
.table-legend .status-dot {
  margin-right: 4px;
  vertical-align: middle;
}

/* Drawer */
.drawer-content {
  padding: 0 20px 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.drawer-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  background: #f5f7fa;
  border-radius: 6px;
}
.drawer-counter {
  font-size: 14px;
  font-weight: 600;
  color: #606266;
}
.drawer-status {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.drawer-section {
  background: #fafafa;
  border-radius: 6px;
  padding: 16px;
}
.section-title {
  font-size: 13px;
  font-weight: 600;
  color: #303133;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.section-hint {
  font-size: 11px;
  color: #909399;
  margin-top: 6px;
}
.redirect-row {
  display: flex;
  gap: 8px;
  align-items: center;
}
.quick-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.drawer-next {
  margin-top: auto;
  padding-top: 8px;
}
</style>
