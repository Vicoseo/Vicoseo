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
          <el-button type="primary" @click="runSetup" :loading="setupLoading" :disabled="!setupForm.domain || !setupForm.site_name" icon="el-icon-s-promotion">
            Kurulumu Başlat
          </el-button>
        </el-form-item>
      </el-form>
      <div v-if="setupSteps.length" class="setup-progress">
        <el-steps :active="activeSetupStep" finish-status="success" align-center>
          <el-step v-for="(step, idx) in setupStepLabels" :key="idx" :title="step.label" :status="getStepStatus(step.key)" :description="getStepMessage(step.key)" />
        </el-steps>
        <div v-if="setupResult" style="margin-top: 20px">
          <el-alert :title="setupResult.success ? 'Kurulum Tamamlandı!' : 'Kurulum Hatası'" :type="setupResult.success ? 'success' : 'error'" :description="setupResult.message" show-icon :closable="false" />
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
          <el-button v-if="pendingZones.length > 0" size="small" type="warning" icon="el-icon-refresh-right" @click="fixAllPending" :loading="fixAllLoading">
            Tüm Pending'leri Düzelt ({{ pendingZones.length }})
          </el-button>
          <el-button size="small" icon="el-icon-refresh" @click="fetchZones" :loading="zonesLoading">
            Yenile
          </el-button>
        </div>
      </div>

      <el-table :data="zones" v-loading="zonesLoading" :row-class-name="tableRowClass" @expand-change="onExpandChange" ref="zoneTable">
        <el-table-column type="expand">
          <template slot-scope="{ row }">
            <div class="expand-panel" v-loading="row._dnsLoading">
              <!-- DNS Records -->
              <div class="expand-section">
                <div class="expand-title">
                  DNS Kayıtları ({{ (row._dnsRecords || []).length }})
                  <el-button size="mini" icon="el-icon-plus" style="margin-left: 8px" @click.stop="openDnsDialog(row)">Ekle</el-button>
                </div>
                <el-table :data="row._dnsRecords || []" size="mini" border stripe empty-text="DNS kaydı yok" style="margin-top: 8px">
                  <el-table-column prop="type" label="Tip" width="60" />
                  <el-table-column prop="name" label="Ad" show-overflow-tooltip>
                    <template slot-scope="scope">
                      <code>{{ scope.row.name }}</code>
                    </template>
                  </el-table-column>
                  <el-table-column prop="content" label="Değer" show-overflow-tooltip>
                    <template slot-scope="scope">
                      <code>{{ scope.row.content }}</code>
                    </template>
                  </el-table-column>
                  <el-table-column label="Proxy" width="50" align="center">
                    <template slot-scope="scope">
                      <i :class="scope.row.proxied ? 'el-icon-check' : 'el-icon-close'" :style="{ color: scope.row.proxied ? '#67c23a' : '#ddd' }" />
                    </template>
                  </el-table-column>
                </el-table>
              </div>

              <!-- 301 Redirect -->
              <div class="expand-section" style="margin-top: 16px">
                <div class="expand-title">301 Domain Redirect</div>
                <div style="display: flex; gap: 8px; align-items: center; margin-top: 8px">
                  <el-input v-model="row._redirectInput" placeholder="yenidomain.com" size="small" clearable style="max-width: 300px" @click.native.stop />
                  <el-button size="small" type="primary" @click.stop="saveRowRedirect(row)" :loading="row._redirectSaving" :disabled="row._redirectInput === (getSiteRedirect(row.name) || '')">
                    Kaydet
                  </el-button>
                </div>
                <div style="font-size: 11px; color: #909399; margin-top: 4px">
                  BTK engellemesinde yeni domain yaz. Tüm trafik 301 ile yönlendirilir (path korunur). Boş = redirect yok.
                </div>
              </div>
            </div>
          </template>
        </el-table-column>

        <el-table-column label="Domain" min-width="180">
          <template slot-scope="{ row }">
            <span class="domain-name">{{ row.name }}</span>
          </template>
        </el-table-column>

        <el-table-column label="Durum" width="100" align="center">
          <template slot-scope="{ row }">
            <el-tag :type="row.status === 'active' ? 'success' : 'warning'" size="mini" effect="dark">
              {{ row.status }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column label="301" width="160">
          <template slot-scope="{ row }">
            <span v-if="getSiteRedirect(row.name)" class="redirect-badge">→ {{ getSiteRedirect(row.name) }}</span>
            <span v-else class="no-redirect">—</span>
          </template>
        </el-table-column>

        <el-table-column label="İşlemler" width="340">
          <template slot-scope="{ row }">
            <el-button-group>
              <el-button size="mini" type="primary" icon="el-icon-search" @click.stop="toggleExpand(row)">
                DNS
              </el-button>
              <el-button size="mini" icon="el-icon-plus" @click.stop="openDnsDialog(row)">
                Ekle
              </el-button>
              <el-button size="mini" type="info" icon="el-icon-view" @click.stop="checkZoneStatus(row)">
                Durum
              </el-button>
              <el-button v-if="row.status !== 'active'" size="mini" type="warning" icon="el-icon-s-promotion" @click.stop="fixPending(row)" :loading="row._fixing">
                Aktifleştir
              </el-button>
            </el-button-group>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

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
      balance: null,
      balanceLoading: false,
      searchQuery: '',
      searchResults: [],
      searchLoading: false,
      setupForm: { domain: '', site_name: '', server_ip: '' },
      setupLoading: false,
      setupSteps: [],
      setupResult: null,
      setupStepLabels: SETUP_STEP_LABELS,
      zones: [],
      zonesLoading: false,
      fixAllLoading: false,
      sites: [],
      expandedRows: [],
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
    // ─── Sites ───
    async fetchSites() {
      try {
        const { data } = await getSites({ per_page: 100 })
        this.sites = data.data || []
      } catch {}
    },
    getSiteByDomain(domain) {
      return this.sites.find((s) => s.domain === domain)
    },
    getSiteRedirect(domain) {
      const site = this.getSiteByDomain(domain)
      return site?.fallback_domain || ''
    },

    // ─── Expand row ───
    tableRowClass({ row }) {
      const classes = []
      if (this.expandedRows.includes(row.zone_id)) classes.push('expanded-row')
      if (this.getSiteRedirect(row.name)) classes.push('redirected-row')
      return classes.join(' ')
    },

    toggleExpand(row) {
      this.$refs.zoneTable.toggleRowExpansion(row)
    },

    async onExpandChange(row, expandedRows) {
      this.expandedRows = expandedRows.map((r) => r.zone_id)
      if (expandedRows.includes(row)) {
        // Row just expanded - load DNS
        this.$set(row, '_dnsLoading', true)
        this.$set(row, '_dnsRecords', [])
        this.$set(row, '_redirectInput', this.getSiteRedirect(row.name) || '')
        this.$set(row, '_redirectSaving', false)
        try {
          const { data } = await listDnsRecords(row.zone_id)
          this.$set(row, '_dnsRecords', data?.data || [])
        } catch {
          this.$message.error('DNS kayıtları alınamadı.')
        } finally {
          this.$set(row, '_dnsLoading', false)
        }
      }
    },

    async saveRowRedirect(row) {
      const site = this.getSiteByDomain(row.name)
      if (!site) {
        this.$message.warning('Bu domain için CMS sitesi bulunamadı.')
        return
      }
      this.$set(row, '_redirectSaving', true)
      try {
        await updateSite(site.id, { fallback_domain: row._redirectInput || null })
        this.$message.success(row._redirectInput ? `301 → ${row._redirectInput} ayarlandı` : 'Redirect kaldırıldı')
        await this.fetchSites()
      } catch (e) {
        this.$message.error(e.response?.data?.message || 'Kaydedilemedi.')
      } finally {
        this.$set(row, '_redirectSaving', false)
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
        await this.$confirm(`${row.domain} domaini $${row.price?.toFixed(2) || '?'} ücretine satın alınacak. Onaylıyor musunuz?`, 'Satın Alma Onayı', { type: 'warning' })
      } catch { return }
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
        this.zones = (data.data || []).map((z) => ({ ...z, _fixing: false, _dnsLoading: false, _dnsRecords: [], _redirectInput: '', _redirectSaving: false }))
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
            this.$notify({ title: 'NS Bilgisi', message: `${zone.name}: ${data.nameservers.join(', ')}`, type: 'info', duration: 10000 })
          }
          setTimeout(() => this.fetchZones(), 3000)
        } else {
          this.$message.warning(data.message || 'Düzeltme kısmen başarısız.')
          if (data.nameservers && data.nameservers.length) {
            this.$alert(`NS'leri registrar panelinden güncelleyin:\n\n${data.nameservers.join('\n')}`, 'Manuel NS Güncelleme', { type: 'warning' })
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
      let ok = 0, fail = 0
      for (const zone of this.pendingZones) {
        try {
          const { data } = await fixPendingZone(zone.zone_id, zone.name)
          data.success ? ok++ : fail++
        } catch { fail++ }
      }
      this.fixAllLoading = false
      this.$message.info(`${ok} başarılı, ${fail} hata.`)
      setTimeout(() => this.fetchZones(), 5000)
    },

    async checkZoneStatus(zone) {
      try {
        const { data } = await getCfZoneDetail(zone.zone_id)
        if (data.success) {
          this.$message.info(`${zone.name}: ${data.status}`)
          const idx = this.zones.findIndex((z) => z.zone_id === zone.zone_id)
          if (idx !== -1) this.$set(this.zones, idx, { ...this.zones[idx], status: data.status })
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
      this.dnsForm = { zoneId: zone.zone_id, zoneName: zone.name, type: 'A', name: zone.name, content: '', proxied: true }
      this.dnsDialogVisible = true
    },

    async submitDns() {
      if (!this.dnsForm.name || !this.dnsForm.content) {
        this.$message.warning('Ad ve içerik zorunludur.')
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
          // Refresh expanded row if open
          const zone = this.zones.find((z) => z.zone_id === this.dnsForm.zoneId)
          if (zone && this.expandedRows.includes(zone.zone_id)) {
            this.onExpandChange(zone, [zone])
          }
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
.balance-card .balance-amount { font-size: 32px; font-weight: 700; color: #409eff; }
.balance-card .balance-amount .text-muted { color: #c0c4cc; }
.card-header { display: flex; justify-content: space-between; align-items: center; }
.setup-progress { margin-top: 25px; padding: 20px; background: #fafafa; border-radius: 4px; }
.action-buttons { display: flex; gap: 8px; align-items: center; }

.domain-name { font-weight: 600; color: #303133; }
.redirect-badge { display: inline-block; padding: 2px 8px; background: #fdf6ec; color: #e6a23c; border-radius: 4px; font-size: 12px; font-weight: 500; }
.no-redirect { color: #c0c4cc; }

.expand-panel { padding: 12px 20px 16px; }
.expand-section { }
.expand-title { font-size: 13px; font-weight: 600; color: #303133; display: flex; align-items: center; }
.expand-panel code { font-size: 12px; color: #606266; }

/* Expanded row highlight */
.expanded-row td { background: #f0f7ff !important; }

/* Redirected domain - muted orange/yellow background */
.redirected-row td { background: #fef8ed !important; }
.redirected-row .domain-name { color: #b88230; text-decoration: line-through; }
.redirected-row.expanded-row td { background: #fef0d5 !important; }
</style>
