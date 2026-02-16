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
      <div slot="header"><span>Domain Arama &amp; Sat&#305;n Alma</span></div>
      <el-form :inline="true" @submit.native.prevent="searchDomains">
        <el-form-item>
          <el-input
            v-model="searchQuery"
            placeholder="orneksite.com"
            clearable
            style="width: 320px"
          >
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
              {{ row.available ? 'M\u00fcsait' : 'Al\u0131nm\u0131\u015f' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Fiyat" width="100">
          <template slot-scope="{ row }">
            {{ row.available && row.price ? '$' + row.price.toFixed(2) : '-' }}
          </template>
        </el-table-column>
        <el-table-column label="İşlem" width="200">
          <template slot-scope="{ row }">
            <el-button
              v-if="row.available"
              size="small"
              type="success"
              @click="buyDomain(row)"
              :loading="row._buying"
            >
              Sat&#305;n Al
            </el-button>
            <el-button
              size="small"
              type="primary"
              @click="startSetup(row.domain)"
            >
              H&#305;zl&#305; Kurulum
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- Quick Setup -->
    <el-card shadow="hover" style="margin-top: 20px">
      <div slot="header"><span>H&#305;zl&#305; Kurulum (Full Setup)</span></div>
      <el-form :model="setupForm" label-width="120px" style="max-width: 600px">
        <el-form-item label="Domain">
          <el-input v-model="setupForm.domain" placeholder="orneksite.com" />
        </el-form-item>
        <el-form-item label="Site Ad&#305;">
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
            Kurulumu Ba&#351;lat
          </el-button>
        </el-form-item>
      </el-form>

      <!-- Setup Progress -->
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
            :title="setupResult.success ? 'Kurulum Tamamland\u0131!' : 'Kurulum Hatas\u0131'"
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

    <!-- Cloudflare Zones -->
    <el-card shadow="hover" style="margin-top: 20px">
      <div slot="header" class="card-header">
        <span>Cloudflare Zone'lar&#305;</span>
        <div>
          <el-button
            v-if="pendingZones.length > 0"
            size="small"
            type="warning"
            icon="el-icon-refresh-right"
            @click="fixAllPending"
            :loading="fixAllLoading"
          >
            T&#252;m Pending'leri D&#252;zelt ({{ pendingZones.length }})
          </el-button>
          <el-button size="small" icon="el-icon-refresh" @click="fetchZones" :loading="zonesLoading">
            Yenile
          </el-button>
        </div>
      </div>

      <el-table :data="zones" v-loading="zonesLoading">
        <el-table-column prop="name" label="Domain" />
        <el-table-column label="Durum" width="120">
          <template slot-scope="{ row }">
            <el-tag :type="row.status === 'active' ? 'success' : 'warning'" size="small">
              {{ row.status }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Nameserver'lar">
          <template slot-scope="{ row }">
            <span v-for="(ns, i) in row.name_servers" :key="ns">
              {{ ns }}<span v-if="i < row.name_servers.length - 1">, </span>
            </span>
          </template>
        </el-table-column>
        <el-table-column label="İşlemler" width="320">
          <template slot-scope="{ row }">
            <el-button size="mini" @click="openDnsDialog(row)">DNS Ekle</el-button>
            <el-button size="mini" type="info" @click="checkZoneStatus(row)">Durum</el-button>
            <el-button
              v-if="row.status !== 'active'"
              size="mini"
              type="warning"
              @click="fixPending(row)"
              :loading="row._fixing"
            >
              Aktifle&#351;tir
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- DNS Add Dialog -->
    <el-dialog title="DNS Kayd&#305; Ekle" :visible.sync="dnsDialogVisible" width="500px">
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
  addDnsRecord,
  fixPendingZone,
} from '../../api/domains'

const SETUP_STEP_LABELS = [
  { key: 'cloudflare_zone', label: "Cloudflare'e ekleniyor" },
  { key: 'nameservers', label: 'NS g\u00fcncelleniyor' },
  { key: 'dns_records', label: 'DNS kay\u0131tlar\u0131' },
  { key: 'ssl', label: 'SSL ayarlan\u0131yor' },
  { key: 'cms_site', label: 'CMS site olu\u015fturuluyor' },
  { key: 'nginx', label: 'Nginx yap\u0131land\u0131r\u0131l\u0131yor' },
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
      setupForm: {
        domain: '',
        site_name: '',
        server_ip: '',
      },
      setupLoading: false,
      setupSteps: [],
      setupResult: null,
      setupStepLabels: SETUP_STEP_LABELS,

      // Zones
      zones: [],
      zonesLoading: false,

      // Fix pending
      fixAllLoading: false,

      // DNS dialog
      dnsDialogVisible: false,
      dnsLoading: false,
      dnsForm: {
        zoneId: '',
        zoneName: '',
        type: 'A',
        name: '',
        content: '',
        proxied: true,
      },
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
  },

  methods: {
    async fetchBalance() {
      this.balanceLoading = true
      try {
        const { data } = await getBalance()
        this.balance = data.balance
      } catch (e) {
        this.$message.error('Bakiye al\u0131namad\u0131.')
      } finally {
        this.balanceLoading = false
      }
    },

    async searchDomains() {
      if (!this.searchQuery.trim()) return
      this.searchLoading = true
      this.searchResults = []
      try {
        const { data } = await searchDomain(this.searchQuery.trim())
        this.searchResults = (data.data || []).map((r) => ({ ...r, _buying: false }))
      } catch (e) {
        this.$message.error('Arama ba\u015far\u0131s\u0131z.')
      } finally {
        this.searchLoading = false
      }
    },

    async buyDomain(row) {
      try {
        await this.$confirm(
          `${row.domain} domaini $${row.price?.toFixed(2) || '?'} \u00fccretine sat\u0131n al\u0131nacak. Onayl\u0131yor musunuz?`,
          'Sat\u0131n Alma Onay\u0131',
          { type: 'warning' },
        )
      } catch {
        return
      }

      row._buying = true
      try {
        const { data } = await purchaseDomain({ domain: row.domain })
        if (data.success) {
          this.$message.success(data.message || 'Domain sat\u0131n al\u0131nd\u0131!')
          row.available = false
          this.fetchBalance()
        } else {
          this.$message.error(data.message || 'Sat\u0131n alma ba\u015far\u0131s\u0131z.')
        }
      } catch (e) {
        const msg = e.response?.data?.message || 'Sat\u0131n alma hatas\u0131.'
        this.$message.error(msg)
      } finally {
        row._buying = false
      }
    },

    startSetup(domain) {
      this.setupForm.domain = domain
      this.setupForm.site_name = domain.replace(/\.[^.]+$/, '').replace(/[^a-zA-Z0-9]/g, ' ')
      // Scroll to setup section
      this.$nextTick(() => {
        const el = document.querySelector('.setup-progress')
        if (el) el.scrollIntoView({ behavior: 'smooth' })
      })
    },

    async runSetup() {
      if (!this.setupForm.domain || !this.setupForm.site_name) return

      this.setupLoading = true
      this.setupResult = null
      // Simulate initial running steps
      this.setupSteps = SETUP_STEP_LABELS.map((s) => ({ step: s.key, status: 'pending' }))

      try {
        const payload = {
          domain: this.setupForm.domain,
          site_name: this.setupForm.site_name,
        }
        if (this.setupForm.server_ip) {
          payload.server_ip = this.setupForm.server_ip
        }

        const { data } = await fullSetup(payload)
        this.setupSteps = data.steps || []
        this.setupResult = data
        if (data.success) {
          this.$message.success('Kurulum tamamland\u0131!')
          this.fetchZones()
        } else {
          this.$message.warning(data.message || 'Kurulum k\u0131smen ba\u015far\u0131s\u0131z.')
        }
      } catch (e) {
        const errData = e.response?.data
        if (errData?.steps) {
          this.setupSteps = errData.steps
        }
        this.setupResult = {
          success: false,
          message: errData?.message || 'Kurulum hatas\u0131.',
        }
        this.$message.error(errData?.message || 'Kurulum hatas\u0131.')
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

    async fetchZones() {
      this.zonesLoading = true
      try {
        const { data } = await getCfZones()
        this.zones = (data.data || []).map((z) => ({ ...z, _fixing: false }))
      } catch (e) {
        this.$message.error('Zone listesi al\u0131namad\u0131.')
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
              message: `${zone.name} i\u00e7in NS: ${data.nameservers.join(', ')}`,
              type: 'info',
              duration: 10000,
            })
          }
          // Re-fetch zones after a short delay
          setTimeout(() => this.fetchZones(), 3000)
        } else {
          this.$message.warning(data.message || 'D\u00fczeltme k\u0131smen ba\u015far\u0131s\u0131z.')
          if (data.nameservers && data.nameservers.length) {
            this.$alert(
              `Nameserver'lar\u0131 registrar panelinizden manuel olarak g\u00fcncelleyin:\n\n${data.nameservers.join('\n')}`,
              'Manuel NS G\u00fcncelleme Gerekli',
              { type: 'warning' },
            )
          }
        }
      } catch (e) {
        const msg = e.response?.data?.message || 'Aktivasyon hatası.'
        this.$message.error(msg)
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
          if (data.success) {
            successCount++
          } else {
            failCount++
          }
        } catch {
          failCount++
        }
      }

      this.fixAllLoading = false
      this.$message.info(`${successCount} zone i\u00e7in aktivasyon tetiklendi, ${failCount} hata.`)
      setTimeout(() => this.fetchZones(), 5000)
    },

    async checkZoneStatus(zone) {
      try {
        const { data } = await getCfZoneDetail(zone.zone_id)
        if (data.success) {
          this.$message.info(`${zone.name}: ${data.status}`)
          // Update local data
          const idx = this.zones.findIndex((z) => z.zone_id === zone.zone_id)
          if (idx !== -1) {
            this.$set(this.zones, idx, { ...this.zones[idx], status: data.status })
          }
        } else {
          this.$message.warning(data.message || 'Durum al\u0131namad\u0131.')
        }
      } catch (e) {
        this.$message.error('Durum kontrol\u00fc ba\u015far\u0131s\u0131z.')
      }
    },

    openDnsDialog(zone) {
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
        this.$message.warning('Ad ve i\u00e7erik alanlar\u0131 zorunludur.')
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
          this.$message.success('DNS kayd\u0131 eklendi.')
          this.dnsDialogVisible = false
        } else {
          this.$message.error(data.message || 'DNS kayd\u0131 eklenemedi.')
        }
      } catch (e) {
        const msg = e.response?.data?.message || 'DNS kayd\u0131 eklenemedi.'
        this.$message.error(msg)
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
</style>
