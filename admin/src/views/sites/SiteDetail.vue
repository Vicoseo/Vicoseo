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
import PageList from '../pages/PageList.vue'
import PostList from '../posts/PostList.vue'
import OfferList from './OfferList.vue'
import RedirectList from '../redirects/RedirectList.vue'
import FooterLinkList from '../footerLinks/FooterLinkList.vue'

export default {
  name: 'SiteDetail',
  components: { PageList, PostList, OfferList, RedirectList, FooterLinkList },
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
