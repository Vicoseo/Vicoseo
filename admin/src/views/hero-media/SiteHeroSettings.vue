<template>
  <div v-loading="loading">
    <el-row :gutter="20">
      <!-- Form -->
      <el-col :span="14">
        <el-form label-width="160px" size="small">
          <!-- Background Package -->
          <el-form-item label="Arkaplan Paketi">
            <div class="package-grid">
              <div
                v-for="pkg in packages"
                :key="pkg.id"
                class="package-thumb"
                :class="{ selected: form.background_package_id === pkg.id }"
                @click="form.background_package_id = pkg.id"
              >
                <img :src="resolveUrl(pkg.thumbnail_url || pkg.image_url)" :alt="pkg.name" />
                <div class="package-overlay" :style="{ backgroundColor: pkg.overlay_color, mixBlendMode: pkg.overlay_blend }"></div>
                <span class="package-name">{{ pkg.name }}</span>
              </div>
              <div
                class="package-thumb package-none"
                :class="{ selected: !form.background_package_id }"
                @click="form.background_package_id = null"
              >
                <i class="el-icon-close" style="font-size: 24px; color: #909399"></i>
                <span class="package-name">Yok</span>
              </div>
            </div>
          </el-form-item>

          <!-- Overlay -->
          <el-form-item label="Overlay Rengi">
            <el-input v-model="form.overlay_color" placeholder="rgba(10,10,26,0.70)" />
          </el-form-item>
          <el-form-item label="Overlay Blend">
            <el-select v-model="form.overlay_blend" style="width: 100%">
              <el-option label="Multiply" value="multiply" />
              <el-option label="Overlay" value="overlay" />
              <el-option label="Screen" value="screen" />
              <el-option label="Normal" value="normal" />
            </el-select>
          </el-form-item>

          <!-- Branding -->
          <el-form-item label="Vurgu Rengi">
            <el-input v-model="form.accent_color" :placeholder="sitePrimaryColor || '#ffd700'" />
          </el-form-item>

          <!-- Badge -->
          <el-form-item label="Rozet Göster">
            <el-switch v-model="form.show_badge" />
          </el-form-item>
          <el-form-item v-if="form.show_badge" label="Rozet Metni">
            <el-input v-model="form.badge_text" placeholder="Güncel 2026" />
          </el-form-item>

          <!-- CTA -->
          <el-form-item label="CTA Göster">
            <el-switch v-model="form.show_cta" />
          </el-form-item>
          <el-form-item v-if="form.show_cta" label="CTA Metni">
            <el-input v-model="form.cta_text" placeholder="Hemen Giris Yap" />
          </el-form-item>
          <el-form-item v-if="form.show_cta" label="CTA URL">
            <el-input v-model="form.cta_url" :placeholder="siteEntryUrl || 'https://...'" />
          </el-form-item>

          <!-- Meta Toggles -->
          <el-form-item label="Tarih Göster">
            <el-switch v-model="form.show_date" />
          </el-form-item>
          <el-form-item label="Okuma Süresi">
            <el-switch v-model="form.show_reading_time" />
          </el-form-item>
          <el-form-item label="Kategori Göster">
            <el-switch v-model="form.show_category" />
          </el-form-item>

          <!-- Slogan -->
          <el-form-item label="Slogan">
            <el-input v-model="form.slogan" placeholder="En güvenilir bahis rehberi" />
          </el-form-item>

          <!-- Layout -->
          <el-form-item label="Düzen">
            <el-radio-group v-model="form.layout">
              <el-radio label="centered">Ortalanmış</el-radio>
              <el-radio label="left">Sola Hizalı</el-radio>
            </el-radio-group>
          </el-form-item>

          <el-form-item>
            <el-button type="primary" :loading="saving" @click="handleSave">Kaydet</el-button>
          </el-form-item>
        </el-form>
      </el-col>

      <!-- Live Preview -->
      <el-col :span="10">
        <div class="preview-label-top">Canlı Önizleme</div>
        <div class="hero-preview" :style="{ textAlign: form.layout === 'centered' ? 'center' : 'left' }">
          <img v-if="selectedPackage" :src="resolveUrl(selectedPackage.thumbnail_url || selectedPackage.image_url)" class="hero-preview-bg" alt="" />
          <div class="hero-preview-overlay" :style="{ backgroundColor: form.overlay_color || 'rgba(10,10,26,0.70)', mixBlendMode: form.overlay_blend || 'multiply' }"></div>
          <div class="hero-preview-content">
            <span v-if="form.show_badge && form.badge_text" class="hero-badge" :style="{ backgroundColor: previewAccent }">{{ form.badge_text }}</span>
            <h2 class="hero-title">Örnek Yazı Başlığı</h2>
            <p v-if="form.slogan" class="hero-slogan">{{ form.slogan }}</p>
            <div class="hero-meta">
              <span v-if="form.show_date">9 Mart 2026</span>
              <span v-if="form.show_reading_time"> · 5 dk</span>
              <span v-if="form.show_category"> · Spor Bahisleri</span>
            </div>
            <a v-if="form.show_cta" class="hero-cta" :style="{ backgroundColor: previewAccent }">{{ form.cta_text || 'Hemen Giris Yap' }}</a>
          </div>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { getSiteHeroSettings, updateSiteHeroSettings } from '../../api/heroMedia'
import { getSite } from '../../api/sites'

const BACKEND_URL = process.env.VUE_APP_API_BASE_URL || ''

export default {
  name: 'SiteHeroSettings',
  props: {
    siteId: { type: [String, Number], required: true },
  },
  data() {
    return {
      loading: false,
      saving: false,
      packages: [],
      sitePrimaryColor: '',
      siteEntryUrl: '',
      form: {
        background_package_id: null,
        overlay_color: 'rgba(10, 10, 26, 0.70)',
        overlay_blend: 'multiply',
        accent_color: '',
        show_badge: false,
        badge_text: '',
        show_cta: true,
        cta_text: 'Hemen Giris Yap',
        cta_url: '',
        show_date: true,
        show_reading_time: true,
        show_category: true,
        slogan: '',
        featured_image_in_hero: false,
        layout: 'centered',
      },
    }
  },
  computed: {
    selectedPackage() {
      if (!this.form.background_package_id) return null
      return this.packages.find((p) => p.id === this.form.background_package_id)
    },
    previewAccent() {
      return this.form.accent_color || this.sitePrimaryColor || '#ffd700'
    },
  },
  created() {
    this.fetchData()
  },
  methods: {
    resolveUrl(url) {
      if (!url) return ''
      if (url.startsWith('http')) return url
      return BACKEND_URL + url
    },
    async fetchData() {
      this.loading = true
      try {
        const [heroRes, siteRes] = await Promise.all([
          getSiteHeroSettings(this.siteId),
          getSite(this.siteId),
        ])
        this.packages = heroRes.data.data.packages
        const settings = heroRes.data.data.hero_settings || {}
        Object.keys(this.form).forEach((key) => {
          if (settings[key] !== undefined && settings[key] !== null) {
            this.form[key] = settings[key]
          }
        })
        const site = siteRes.data.data
        this.sitePrimaryColor = site.primary_color || ''
        this.siteEntryUrl = site.entry_url || ''
      } catch {
        this.$message.error('Hero ayarları yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    async handleSave() {
      this.saving = true
      try {
        await updateSiteHeroSettings(this.siteId, this.form)
        this.$message.success('Hero ayarları kaydedildi')
      } catch (err) {
        const errors = err.response?.data?.errors
        if (errors) {
          const first = Object.values(errors)[0]
          this.$message.error(Array.isArray(first) ? first[0] : first)
        } else {
          this.$message.error('Kayıt başarısız')
        }
      } finally {
        this.saving = false
      }
    },
  },
}
</script>

<style scoped>
.preview-label-top {
  font-size: 13px;
  color: #909399;
  margin-bottom: 8px;
  font-weight: 600;
}
.package-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.package-thumb {
  position: relative;
  width: 100px;
  height: 60px;
  border-radius: 6px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: border-color 0.2s;
}
.package-thumb.selected {
  border-color: #409eff;
}
.package-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.package-overlay {
  position: absolute;
  inset: 0;
}
.package-name {
  position: absolute;
  bottom: 2px;
  left: 4px;
  color: #fff;
  font-size: 10px;
  text-shadow: 0 1px 3px rgba(0,0,0,0.8);
  z-index: 1;
}
.package-none {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #f5f7fa;
}
.package-none .package-name {
  color: #909399;
  text-shadow: none;
  position: static;
  margin-top: 4px;
}
.hero-preview {
  position: relative;
  width: 100%;
  min-height: 260px;
  border-radius: 8px;
  overflow: hidden;
  background: #0a0a1a;
}
.hero-preview-bg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.hero-preview-overlay {
  position: absolute;
  inset: 0;
}
.hero-preview-content {
  position: relative;
  z-index: 1;
  padding: 24px 20px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-height: 260px;
}
.hero-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  color: #0a0a1a;
  text-transform: uppercase;
  margin-bottom: 10px;
  align-self: inherit;
  width: fit-content;
}
.hero-title {
  color: #fff;
  font-size: 20px;
  font-weight: 700;
  text-shadow: 0 2px 8px rgba(0,0,0,0.5);
  margin: 0 0 8px;
}
.hero-slogan {
  color: rgba(255,255,255,0.8);
  font-size: 13px;
  margin: 0 0 8px;
}
.hero-meta {
  color: rgba(255,255,255,0.6);
  font-size: 12px;
  margin-bottom: 14px;
}
.hero-cta {
  display: inline-block;
  padding: 8px 20px;
  border-radius: 6px;
  color: #0a0a1a;
  font-weight: 700;
  font-size: 13px;
  text-decoration: none;
  cursor: pointer;
  width: fit-content;
}
</style>
