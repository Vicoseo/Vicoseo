<template>
  <div>
    <div style="margin-bottom: 20px">
      <el-button icon="el-icon-back" @click="$router.push('/sites')">Sitelere Dön</el-button>
    </div>

    <el-card>
      <div slot="header">
        <span>{{ isEdit ? 'Site Düzenle' : 'Site Oluştur' }}</span>
      </div>

      <el-form
        ref="form"
        :model="form"
        :rules="rules"
        label-width="160px"
        v-loading="loading"
        @submit.native.prevent="handleSubmit"
      >
        <el-form-item label="Alan Adı" prop="domain">
          <el-input v-model="form.domain" placeholder="example.com" />
        </el-form-item>
        <el-form-item label="İsim" prop="name">
          <el-input v-model="form.name" placeholder="Site Adı" />
        </el-form-item>
        <el-form-item label="Logo">
          <image-upload v-model="form.logo_url" directory="site-logos" />
        </el-form-item>
        <el-form-item label="Favicon">
          <image-upload v-model="form.favicon_url" directory="site-logos" />
        </el-form-item>
        <el-form-item label="Ana Renk">
          <el-color-picker v-model="form.primary_color" />
        </el-form-item>
        <el-form-item label="İkincil Renk">
          <el-color-picker v-model="form.secondary_color" />
        </el-form-item>
        <el-form-item label="Header Arka Plan">
          <el-color-picker v-model="form.header_bg_color" />
          <span style="color: #909399; font-size: 12px; margin-left: 8px">
            Boşsa Ana Renk kullanılır
          </span>
        </el-form-item>
        <el-form-item label="Meta Başlık">
          <el-input v-model="form.meta_title" placeholder="SEO Başlık" />
        </el-form-item>
        <el-form-item label="Meta Açıklama">
          <el-input v-model="form.meta_description" type="textarea" :rows="3" placeholder="SEO Açıklama" />
        </el-form-item>

        <el-divider content-position="left">Site Ayarları</el-divider>

        <el-form-item label="Giriş URL" prop="entry_url">
          <el-input v-model="form.entry_url" placeholder="https://hedefsite.com">
            <template slot="prepend">🔗</template>
          </el-input>
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Ayarlanırsa sitenin en üstünde "Siteye Giriş Yap" butonu gösterilir
          </div>
        </el-form-item>
        <el-form-item label="Login URL" prop="login_url">
          <el-input v-model="form.login_url" placeholder="https://hedefsite.com/login">
            <template slot="prepend">🔑</template>
          </el-input>
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Sitenin en üstündeki "GİRİŞ YAP" butonunun yönlendirme adresi. Boşsa /go/login kullanılır.
          </div>
        </el-form-item>
        <el-form-item label="Sponsorlar Gözüksün mü">
          <el-switch v-model="form.show_sponsors" />
          <span style="color: #909399; font-size: 12px; margin-left: 8px">
            Kapatılırsa sponsor bölümü tamamen gizlenir
          </span>
        </el-form-item>

        <el-divider content-position="left">Google Analytics</el-divider>

        <el-form-item label="GA Measurement ID">
          <el-input v-model="form.ga_measurement_id" placeholder="G-XXXXXXXXXX">
            <template slot="prepend">📊</template>
          </el-input>
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Google Analytics 4 Measurement ID. Frontend'de otomatik olarak &lt;script&gt; eklenir.
          </div>
        </el-form-item>

        <el-divider content-position="left">SEO Ayarları</el-divider>

        <el-form-item label="301 Domain Redirect">
          <el-input v-model="form.fallback_domain" placeholder="yenidomain.com" />
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Bu alana domain yazılırsa, siteye gelen TÜM trafik 301 ile bu domain'e yönlendirilir (path korunur).
            BTK engellemesi durumunda yeni domain'i buraya girin. Boş bırakılırsa redirect yapılmaz.
          </div>
        </el-form-item>
        <el-form-item label="Noindex Modu">
          <el-switch v-model="form.noindex_mode" />
          <span style="color: #909399; font-size: 12px; margin-left: 8px">
            Aktifse arama motorları bu siteyi indekslemez
          </span>
        </el-form-item>
        <el-form-item label="robots.txt">
          <el-input
            v-model="form.robots_txt"
            type="textarea"
            :rows="5"
            placeholder="User-agent: *&#10;Allow: /&#10;Disallow: /admin/"
            style="font-family: monospace"
          />
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Özel robots.txt içeriği. Boş bırakılırsa varsayılan kullanılır.
          </div>
        </el-form-item>

        <el-divider content-position="left">Özel CSS</el-divider>

        <el-form-item label="Custom CSS">
          <el-input
            v-model="form.custom_css"
            type="textarea"
            :rows="6"
            placeholder=".page-content h1 { color: #333; }&#10;.page-content .info-box { background: #f0f9ff; }"
            style="font-family: monospace; font-size: 13px"
          />
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Siteye özel CSS. Sadece .page-content içinde geçerli olur, sponsor bölümüne etki etmez. Max 10KB.
          </div>
        </el-form-item>

        <el-divider content-position="left">İçerik Şablonu</el-divider>

        <el-form-item label="Prompt Şablonu">
          <el-input
            v-model="form.content_prompt_template"
            type="textarea"
            :rows="6"
            placeholder="AI içerik üretiminde kullanılacak ek talimatlar. Placeholders: {{brand_name}}, {{domain}}, {{telegram}}, {{login_url}}, {{entry_url}}"
            style="font-family: monospace"
          />
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Her AI içerik üretiminde bu talimatlar prompt'a eklenir. Boş bırakılırsa varsayılan kullanılır.
          </div>
        </el-form-item>

        <el-divider content-position="left">Sosyal Medya Linkleri</el-divider>

        <el-form-item label="Telegram">
          <el-input v-model="form.social_links.telegram" placeholder="https://t.me/kanal" />
        </el-form-item>
        <el-form-item label="Instagram">
          <el-input v-model="form.social_links.instagram" placeholder="https://instagram.com/hesap" />
        </el-form-item>
        <el-form-item label="X (Twitter)">
          <el-input v-model="form.social_links.x" placeholder="https://x.com/hesap" />
        </el-form-item>
        <el-form-item label="YouTube">
          <el-input v-model="form.social_links.youtube" placeholder="https://youtube.com/@kanal" />
        </el-form-item>
        <el-form-item label="TikTok">
          <el-input v-model="form.social_links.tiktok" placeholder="https://tiktok.com/@hesap" />
        </el-form-item>
        <el-form-item label="WhatsApp">
          <el-input v-model="form.social_links.whatsapp" placeholder="https://wa.me/905xxxxxxxxx" />
        </el-form-item>
        <el-form-item label="Destek E-posta">
          <el-input v-model="form.social_links.support_email" placeholder="destek@site.com" />
        </el-form-item>

        <el-divider />

        <el-form-item label="Aktif">
          <el-switch v-model="form.is_active" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="saving" native-type="submit">
            {{ isEdit ? 'Güncelle' : 'Oluştur' }}
          </el-button>
          <el-button @click="$router.push('/sites')">İptal</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import { getSite, createSite, updateSite } from '../../api/sites'
import ImageUpload from '../../components/ImageUpload.vue'

export default {
  name: 'SiteForm',
  components: { ImageUpload },
  data() {
    return {
      form: {
        domain: '',
        name: '',
        logo_url: '',
        favicon_url: '',
        primary_color: '#000000',
        secondary_color: '#ffffff',
        header_bg_color: '',
        meta_title: '',
        meta_description: '',
        entry_url: '',
        login_url: '',
        show_sponsors: true,
        sponsor_page_visible: true,
        sponsor_contact_url: '',
        sponsor_contact_text: '',
        fallback_domain: '',
        robots_txt: '',
        noindex_mode: false,
        content_prompt_template: '',
        custom_css: '',
        ga_measurement_id: '',
        social_links: {
          telegram: '',
          instagram: '',
          x: '',
          youtube: '',
          tiktok: '',
          whatsapp: '',
          support_email: '',
        },
        is_active: true,
      },
      rules: {
        domain: [{ required: true, message: 'Alan adı zorunludur', trigger: 'blur' }],
        name: [{ required: true, message: 'İsim zorunludur', trigger: 'blur' }],
      },
      loading: false,
      saving: false,
    }
  },
  computed: {
    isEdit() {
      return !!this.$route.params.id
    },
    siteId() {
      return this.$route.params.id
    },
  },
  created() {
    if (this.isEdit) this.fetchSite()
  },
  methods: {
    async fetchSite() {
      this.loading = true
      try {
        const { data } = await getSite(this.siteId)
        const site = data.data
        Object.keys(this.form).forEach((key) => {
          if (key === 'social_links') {
            if (site.social_links && typeof site.social_links === 'object') {
              Object.keys(this.form.social_links).forEach((sk) => {
                this.form.social_links[sk] = site.social_links[sk] || ''
              })
            }
          } else if (site[key] !== undefined) {
            this.form[key] = site[key]
          }
        })
      } catch {
        this.$message.error('Site yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    handleSubmit() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.saving = true
        try {
          if (this.isEdit) {
            await updateSite(this.siteId, this.form)
            this.$message.success('Site güncellendi')
          } else {
            await createSite(this.form)
            this.$message.success('Site oluşturuldu')
          }
          this.$router.push('/sites')
        } catch (err) {
          const errors = err.response?.data?.errors
          if (errors) {
            const first = Object.values(errors)[0]
            this.$message.error(Array.isArray(first) ? first[0] : first)
          } else {
            this.$message.error('Site kaydedilemedi')
          }
        } finally {
          this.saving = false
        }
      })
    },
  },
}
</script>
