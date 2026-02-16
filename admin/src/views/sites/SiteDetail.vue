<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
      <div style="display: flex; align-items: center; gap: 12px">
        <el-button icon="el-icon-back" @click="$router.push('/sites')">Siteler</el-button>
        <h2 style="margin: 0" v-if="site">{{ site.name }} <small style="color: #909399">({{ site.domain }})</small></h2>
      </div>
      <div style="display: flex; gap: 8px; align-items: center">
        <el-button
          size="small"
          type="warning"
          :loading="provisioning"
          :disabled="provisioning"
          @click="handleProvision"
        >
          <span v-if="!provisioning">
            <i class="el-icon-connection"></i>
            {{ provisionStatus && provisionStatus.provisioned ? 'SSL Aktif ✓' : 'SSL + Nginx Kur' }}
          </span>
          <span v-else>Kuruluyor...</span>
        </el-button>
        <el-button
          size="small"
          :loading="generating === 'openai'"
          :disabled="!!generating"
          style="background: #10a37f; color: #fff; border-color: #10a37f"
          @click="openAiDialog('openai')"
        >
          <span v-if="generating !== 'openai'">ChatGPT ile Oluştur</span>
          <span v-else>Oluşturuluyor...</span>
        </el-button>
        <el-button
          size="small"
          :loading="generating === 'anthropic'"
          :disabled="!!generating"
          style="background: #d97706; color: #fff; border-color: #d97706"
          @click="openAiDialog('anthropic')"
        >
          <span v-if="generating !== 'anthropic'">Claude ile Oluştur</span>
          <span v-else>Oluşturuluyor...</span>
        </el-button>
        <el-button size="small" @click="$router.push(`/sites/${siteId}/edit`)">Site Düzenle</el-button>
      </div>
    </div>

    <el-card v-loading="loading">
      <el-tabs v-model="activeTab">
        <el-tab-pane label="Sayfalar" name="pages">
          <PageList ref="pageList" :site-id="siteId" />
        </el-tab-pane>
        <el-tab-pane label="Yazılar" name="posts">
          <PostList ref="postList" :site-id="siteId" />
        </el-tab-pane>
        <el-tab-pane label="Sponsorlar" name="offers">
          <OfferList :site-id="siteId" />
        </el-tab-pane>
        <el-tab-pane label="Yönlendirmeler" name="redirects">
          <RedirectList :site-id="siteId" />
        </el-tab-pane>
        <el-tab-pane label="Footer Linkleri" name="footerLinks">
          <FooterLinkList :site-id="siteId" />
        </el-tab-pane>
        <el-tab-pane label="Icerik Plani" name="contentSchedules">
          <ContentScheduleForm :site-id="siteId" />
        </el-tab-pane>
        <el-tab-pane label="Analytics" name="analytics">
          <div v-if="activeTab === 'analytics'">
            <div style="margin-bottom: 12px">
              <el-radio-group v-model="analyticsPeriod" size="small" @change="fetchAnalytics">
                <el-radio-button label="7d">7 Gun</el-radio-button>
                <el-radio-button label="30d">30 Gun</el-radio-button>
                <el-radio-button label="90d">90 Gun</el-radio-button>
              </el-radio-group>
            </div>
            <div v-loading="analyticsLoading">
              <el-row :gutter="16" style="margin-bottom: 16px" v-if="analyticsData">
                <el-col :span="6">
                  <el-card shadow="never"><div class="mini-stat"><div class="mini-num">{{ analyticsData.summary.active_users }}</div><div class="mini-label">Kullanicilar</div></div></el-card>
                </el-col>
                <el-col :span="6">
                  <el-card shadow="never"><div class="mini-stat"><div class="mini-num">{{ analyticsData.summary.page_views }}</div><div class="mini-label">Sayfa Goruntulenme</div></div></el-card>
                </el-col>
                <el-col :span="6">
                  <el-card shadow="never"><div class="mini-stat"><div class="mini-num">{{ analyticsData.summary.sessions }}</div><div class="mini-label">Oturum</div></div></el-card>
                </el-col>
                <el-col :span="6">
                  <el-card shadow="never"><div class="mini-stat"><div class="mini-num">{{ analyticsData.summary.avg_session_duration }}s</div><div class="mini-label">Ort. Sure</div></div></el-card>
                </el-col>
              </el-row>
              <el-table :data="analyticsData ? analyticsData.top_pages : []" size="small" border v-if="analyticsData">
                <el-table-column prop="path" label="Sayfa" />
                <el-table-column prop="page_views" label="Goruntulenme" width="140" />
                <el-table-column prop="users" label="Kullanicilar" width="130" />
              </el-table>
              <div v-if="analyticsError" style="color: #f56c6c; padding: 20px">{{ analyticsError }}</div>
            </div>
          </div>
        </el-tab-pane>
        <el-tab-pane label="Search Console" name="gsc">
          <div v-if="activeTab === 'gsc'">
            <div style="margin-bottom: 12px; display: flex; gap: 8px; align-items: center">
              <el-radio-group v-model="gscPeriod" size="small" @change="fetchGsc">
                <el-radio-button label="7d">7 Gun</el-radio-button>
                <el-radio-button label="28d">28 Gun</el-radio-button>
                <el-radio-button label="90d">90 Gun</el-radio-button>
              </el-radio-group>
              <el-button size="small" type="success" :loading="submittingSitemap" @click="handleSubmitSitemap">Sitemap Gonder</el-button>
            </div>
            <div v-loading="gscLoading">
              <el-row :gutter="16" style="margin-bottom: 16px" v-if="gscData">
                <el-col :span="6">
                  <el-card shadow="never"><div class="mini-stat"><div class="mini-num">{{ gscData.summary.clicks }}</div><div class="mini-label">Tiklanma</div></div></el-card>
                </el-col>
                <el-col :span="6">
                  <el-card shadow="never"><div class="mini-stat"><div class="mini-num">{{ gscData.summary.impressions }}</div><div class="mini-label">Gosterim</div></div></el-card>
                </el-col>
                <el-col :span="6">
                  <el-card shadow="never"><div class="mini-stat"><div class="mini-num">{{ gscData.summary.ctr }}%</div><div class="mini-label">CTR</div></div></el-card>
                </el-col>
                <el-col :span="6">
                  <el-card shadow="never"><div class="mini-stat"><div class="mini-num">{{ gscData.summary.position }}</div><div class="mini-label">Ort. Pozisyon</div></div></el-card>
                </el-col>
              </el-row>
              <el-table :data="gscData ? gscData.queries : []" size="small" border v-if="gscData">
                <el-table-column prop="query" label="Sorgu" />
                <el-table-column prop="clicks" label="Tiklanma" width="100" />
                <el-table-column prop="impressions" label="Gosterim" width="100" />
                <el-table-column prop="ctr" label="CTR %" width="80" />
                <el-table-column prop="position" label="Pozisyon" width="90" />
              </el-table>
            </div>
          </div>
        </el-tab-pane>
      </el-tabs>
    </el-card>

    <!-- AI Content Generation Dialog -->
    <el-dialog
      :title="aiDialogTitle"
      :visible.sync="aiDialogVisible"
      width="440px"
      :close-on-click-modal="false"
    >
      <p style="margin-top: 0; color: #606266">
        <strong>{{ site ? site.name : '' }}</strong> sitesi için AI ile otomatik içerik oluşturulacak.
      </p>
      <el-form label-width="120px">
        <el-form-item label="İçerik Türü">
          <el-radio-group v-model="aiContentType">
            <el-radio label="all">Hepsi</el-radio>
            <el-radio label="pages">Sadece Sayfalar</el-radio>
            <el-radio label="posts">Sadece Yazılar</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <div style="background: #fdf6ec; border: 1px solid #faecd8; border-radius: 4px; padding: 10px 12px; font-size: 13px; color: #e6a23c">
        Bu işlem birkaç dakika sürebilir. Sayfa kapanana kadar bekleyin.
      </div>
      <span slot="footer">
        <el-button @click="aiDialogVisible = false">İptal</el-button>
        <el-button
          type="primary"
          :style="selectedProvider === 'openai'
            ? 'background: #10a37f; border-color: #10a37f'
            : 'background: #d97706; border-color: #d97706'"
          @click="runAiGenerate"
        >
          Oluştur
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getSite, aiGenerateContent, provisionSite, getProvisionStatus } from '../../api/sites'
import { getSiteAnalytics } from '../../api/analytics'
import { getGscPerformance, submitGscSitemap } from '../../api/gsc'
import PageList from '../pages/PageList.vue'
import PostList from '../posts/PostList.vue'
import OfferList from './OfferList.vue'
import RedirectList from '../redirects/RedirectList.vue'
import FooterLinkList from '../footerLinks/FooterLinkList.vue'
import ContentScheduleForm from '../content/ContentScheduleForm.vue'

export default {
  name: 'SiteDetail',
  components: { PageList, PostList, OfferList, RedirectList, FooterLinkList, ContentScheduleForm },
  data() {
    return {
      site: null,
      activeTab: 'pages',
      loading: false,
      generating: null, // null | 'openai' | 'anthropic'
      provisioning: false,
      provisionStatus: null,
      aiDialogVisible: false,
      selectedProvider: null,
      aiContentType: 'all',
      // Analytics
      analyticsPeriod: '7d',
      analyticsData: null,
      analyticsLoading: false,
      analyticsError: null,
      // GSC
      gscPeriod: '28d',
      gscData: null,
      gscLoading: false,
      submittingSitemap: false,
    }
  },
  computed: {
    siteId() {
      return Number(this.$route.params.id)
    },
    aiDialogTitle() {
      return this.selectedProvider === 'openai'
        ? 'ChatGPT ile İçerik Oluştur'
        : 'Claude ile İçerik Oluştur'
    },
  },
  watch: {
    activeTab(tab) {
      if (tab === 'analytics' && !this.analyticsData) this.fetchAnalytics()
      if (tab === 'gsc' && !this.gscData) this.fetchGsc()
    },
  },
  created() {
    this.fetchSite()
    this.fetchProvisionStatus()
  },
  methods: {
    async fetchSite() {
      this.loading = true
      try {
        const { data } = await getSite(this.siteId)
        this.site = data.data
      } catch {
        this.$message.error('Site yüklenemedi')
      } finally {
        this.loading = false
      }
    },

    async fetchProvisionStatus() {
      try {
        const { data } = await getProvisionStatus(this.siteId)
        this.provisionStatus = data
      } catch {
        // ignore
      }
    },

    async handleProvision() {
      if (this.provisionStatus && this.provisionStatus.provisioned) {
        this.$message.info('Bu site zaten yapılandırılmış.')
        return
      }

      try {
        await this.$confirm(
          'Bu domain için SSL sertifikası alınacak ve Nginx yapılandırması oluşturulacak. DNS ayarlarının sunucuya yönlendirilmiş olması gerekir.',
          'Site Yapılandırması',
          { confirmButtonText: 'Kur', cancelButtonText: 'İptal', type: 'info' }
        )
      } catch {
        return
      }

      this.provisioning = true
      try {
        const { data } = await provisionSite(this.siteId)
        this.$message.success(data.message || 'Site başarıyla yapılandırıldı')
        this.fetchProvisionStatus()
      } catch (err) {
        const msg = err.response?.data?.message || 'Yapılandırma başarısız oldu'
        this.$message.error(msg)
      } finally {
        this.provisioning = false
      }
    },

    async fetchAnalytics() {
      this.analyticsLoading = true
      this.analyticsError = null
      try {
        const { data } = await getSiteAnalytics(this.siteId, { period: this.analyticsPeriod })
        this.analyticsData = data.data
      } catch (err) {
        this.analyticsError = err.response?.data?.error || 'Analytics verileri yuklenemedi'
      } finally {
        this.analyticsLoading = false
      }
    },
    async fetchGsc() {
      this.gscLoading = true
      try {
        const { data } = await getGscPerformance(this.siteId, { period: this.gscPeriod })
        this.gscData = data.data
      } catch {
        this.$message.error('Search Console verileri yuklenemedi')
      } finally {
        this.gscLoading = false
      }
    },
    async handleSubmitSitemap() {
      this.submittingSitemap = true
      try {
        const { data } = await submitGscSitemap(this.siteId)
        if (data.success) {
          this.$message.success('Sitemap gonderildi')
        } else {
          this.$message.error(data.message || 'Sitemap gonderilemedi')
        }
      } catch {
        this.$message.error('Sitemap gonderilemedi')
      } finally {
        this.submittingSitemap = false
      }
    },

    openAiDialog(provider) {
      this.selectedProvider = provider
      this.aiContentType = 'all'
      this.aiDialogVisible = true
    },

    async runAiGenerate() {
      this.aiDialogVisible = false
      this.generating = this.selectedProvider

      const providerLabel = this.selectedProvider === 'openai' ? 'ChatGPT' : 'Claude'

      try {
        const { data } = await aiGenerateContent(this.siteId, {
          provider: this.selectedProvider,
          content_type: this.aiContentType,
        })

        // Show results
        if (data.errors && data.errors.length > 0) {
          this.$message.warning(data.message + ` (${data.errors.length} hata oluştu)`)
        } else {
          this.$message.success(data.message)
        }

        // Refresh lists
        this.$nextTick(() => {
          if (this.aiContentType !== 'posts' && this.$refs.pageList) {
            this.$refs.pageList.fetchPages()
          }
          if (this.aiContentType !== 'pages' && this.$refs.postList) {
            this.$refs.postList.fetchPosts()
          }
        })
      } catch (err) {
        const msg = err.response?.data?.message || `${providerLabel} ile içerik oluşturma başarısız`
        this.$message.error(msg)
      } finally {
        this.generating = null
      }
    },
  },
}
</script>

<style scoped>
.mini-stat { text-align: center; padding: 6px 0; }
.mini-num { font-size: 22px; font-weight: 700; color: #409eff; }
.mini-label { font-size: 12px; color: #909399; margin-top: 2px; }
</style>
