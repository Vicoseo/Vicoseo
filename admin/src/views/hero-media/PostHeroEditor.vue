<template>
  <div>
    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 12px">
      <el-button icon="el-icon-back" @click="$router.push(`/sites/${siteId}/posts/${postId}`)">Yazıya Dön</el-button>
      <h2 style="margin: 0">Hero Medya Düzenle</h2>
    </div>

    <el-row :gutter="20" v-loading="loading">
      <!-- Form -->
      <el-col :span="14">
        <el-card>
          <div slot="header" style="display: flex; justify-content: space-between; align-items: center">
            <span>Yazı Hero Ayarları</span>
            <el-button size="mini" @click="clearOverrides">Site Varsayılanlarını Kullan</el-button>
          </div>

          <el-form label-width="160px" size="small">
            <!-- Background Package -->
            <el-form-item>
              <template slot="label">
                <el-checkbox v-model="overrides.background_package_id" style="margin-right: 4px" />Arkaplan
              </template>
              <div v-if="overrides.background_package_id" class="package-grid">
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
              </div>
              <span v-else class="inherited-value">
                {{ sitePackageName || 'Site varsayılanı' }}
              </span>
            </el-form-item>

            <!-- Overlay Color -->
            <el-form-item>
              <template slot="label">
                <el-checkbox v-model="overrides.overlay_color" style="margin-right: 4px" />Overlay Rengi
              </template>
              <el-input v-if="overrides.overlay_color" v-model="form.overlay_color" placeholder="rgba(10,10,26,0.70)" />
              <span v-else class="inherited-value">{{ siteDefaults.overlay_color || 'Varsayılan' }}</span>
            </el-form-item>

            <!-- Overlay Blend -->
            <el-form-item>
              <template slot="label">
                <el-checkbox v-model="overrides.overlay_blend" style="margin-right: 4px" />Overlay Blend
              </template>
              <el-select v-if="overrides.overlay_blend" v-model="form.overlay_blend" style="width: 100%">
                <el-option label="Multiply" value="multiply" />
                <el-option label="Overlay" value="overlay" />
                <el-option label="Screen" value="screen" />
                <el-option label="Normal" value="normal" />
              </el-select>
              <span v-else class="inherited-value">{{ siteDefaults.overlay_blend || 'multiply' }}</span>
            </el-form-item>

            <!-- Accent Color -->
            <el-form-item>
              <template slot="label">
                <el-checkbox v-model="overrides.accent_color" style="margin-right: 4px" />Vurgu Rengi
              </template>
              <el-input v-if="overrides.accent_color" v-model="form.accent_color" />
              <span v-else class="inherited-value">{{ siteDefaults.accent_color || 'Site varsayılanı' }}</span>
            </el-form-item>

            <!-- Badge -->
            <el-form-item>
              <template slot="label">
                <el-checkbox v-model="overrides.show_badge" style="margin-right: 4px" />Rozet
              </template>
              <div v-if="overrides.show_badge">
                <el-switch v-model="form.show_badge" style="margin-bottom: 8px" />
                <el-input v-if="form.show_badge" v-model="form.badge_text" placeholder="Güncel 2026" />
              </div>
              <span v-else class="inherited-value">{{ siteDefaults.show_badge ? siteDefaults.badge_text : 'Kapalı' }}</span>
            </el-form-item>

            <!-- CTA -->
            <el-form-item>
              <template slot="label">
                <el-checkbox v-model="overrides.show_cta" style="margin-right: 4px" />CTA
              </template>
              <div v-if="overrides.show_cta">
                <el-switch v-model="form.show_cta" style="margin-bottom: 8px" />
                <el-input v-if="form.show_cta" v-model="form.cta_text" placeholder="Hemen Giris Yap" style="margin-bottom: 8px" />
                <el-input v-if="form.show_cta" v-model="form.cta_url" placeholder="https://..." />
              </div>
              <span v-else class="inherited-value">{{ siteDefaults.show_cta ? (siteDefaults.cta_text || 'Hemen Giris Yap') : 'Kapalı' }}</span>
            </el-form-item>

            <!-- Slogan -->
            <el-form-item>
              <template slot="label">
                <el-checkbox v-model="overrides.slogan" style="margin-right: 4px" />Slogan
              </template>
              <el-input v-if="overrides.slogan" v-model="form.slogan" />
              <span v-else class="inherited-value">{{ siteDefaults.slogan || 'Yok' }}</span>
            </el-form-item>

            <!-- Featured Image in Hero -->
            <el-form-item label="Öne Çıkan Görsel Hero'da">
              <el-switch v-model="form.featured_image_in_hero" />
            </el-form-item>

            <!-- Layout -->
            <el-form-item>
              <template slot="label">
                <el-checkbox v-model="overrides.layout" style="margin-right: 4px" />Düzen
              </template>
              <el-radio-group v-if="overrides.layout" v-model="form.layout">
                <el-radio label="centered">Ortalanmış</el-radio>
                <el-radio label="left">Sola Hizalı</el-radio>
              </el-radio-group>
              <span v-else class="inherited-value">{{ siteDefaults.layout === 'left' ? 'Sola Hizalı' : 'Ortalanmış' }}</span>
            </el-form-item>

            <el-form-item>
              <el-button type="primary" :loading="saving" @click="handleSave">Kaydet</el-button>
            </el-form-item>
          </el-form>
        </el-card>
      </el-col>

      <!-- Preview -->
      <el-col :span="10">
        <div class="preview-label-top">Birleştirilmiş Önizleme</div>
        <div class="hero-preview" :style="{ textAlign: previewLayout === 'centered' ? 'center' : 'left' }">
          <img v-if="previewBgUrl" :src="resolveUrl(previewBgUrl)" class="hero-preview-bg" alt="" />
          <div class="hero-preview-overlay" :style="{ backgroundColor: previewOverlayColor, mixBlendMode: previewOverlayBlend }"></div>
          <div class="hero-preview-content">
            <span v-if="previewBadgeShow && previewBadgeText" class="hero-badge" :style="{ backgroundColor: previewAccent }">{{ previewBadgeText }}</span>
            <h2 class="hero-title">Örnek Yazı Başlığı</h2>
            <p v-if="previewSlogan" class="hero-slogan">{{ previewSlogan }}</p>
            <div class="hero-meta">
              <span>9 Mart 2026 · 5 dk · Kategori</span>
            </div>
            <a v-if="previewCtaShow" class="hero-cta" :style="{ backgroundColor: previewAccent }">{{ previewCtaText }}</a>
          </div>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { getPostHeroSettings, updatePostHeroSettings } from '../../api/heroMedia'

const BACKEND_URL = process.env.VUE_APP_API_BASE_URL || ''

const HERO_FIELDS = [
  'background_package_id', 'overlay_color', 'overlay_blend', 'accent_color',
  'show_badge', 'badge_text', 'show_cta', 'cta_text', 'cta_url',
  'show_date', 'show_reading_time', 'show_category',
  'slogan', 'featured_image_in_hero', 'layout',
]

export default {
  name: 'PostHeroEditor',
  data() {
    return {
      loading: false,
      saving: false,
      packages: [],
      siteDefaults: {},
      overrides: {},
      form: {
        background_package_id: null,
        overlay_color: '',
        overlay_blend: 'multiply',
        accent_color: '',
        show_badge: false,
        badge_text: '',
        show_cta: true,
        cta_text: '',
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
    siteId() {
      return this.$route.params.siteId
    },
    postId() {
      return this.$route.params.postId
    },
    sitePackageName() {
      if (!this.siteDefaults.background_package_id) return null
      const pkg = this.packages.find((p) => p.id === this.siteDefaults.background_package_id)
      return pkg ? pkg.name : 'Bilinmeyen paket'
    },
    previewPackage() {
      const id = this.overrides.background_package_id ? this.form.background_package_id : this.siteDefaults.background_package_id
      if (!id) return null
      return this.packages.find((p) => p.id === id)
    },
    previewBgUrl() {
      return this.previewPackage ? (this.previewPackage.thumbnail_url || this.previewPackage.image_url) : null
    },
    previewOverlayColor() {
      return (this.overrides.overlay_color ? this.form.overlay_color : this.siteDefaults.overlay_color) || 'rgba(10,10,26,0.70)'
    },
    previewOverlayBlend() {
      return (this.overrides.overlay_blend ? this.form.overlay_blend : this.siteDefaults.overlay_blend) || 'multiply'
    },
    previewAccent() {
      return (this.overrides.accent_color ? this.form.accent_color : this.siteDefaults.accent_color) || '#ffd700'
    },
    previewBadgeShow() {
      return this.overrides.show_badge ? this.form.show_badge : this.siteDefaults.show_badge
    },
    previewBadgeText() {
      return this.overrides.show_badge ? this.form.badge_text : this.siteDefaults.badge_text
    },
    previewCtaShow() {
      return this.overrides.show_cta ? this.form.show_cta : (this.siteDefaults.show_cta !== false)
    },
    previewCtaText() {
      return (this.overrides.show_cta ? this.form.cta_text : this.siteDefaults.cta_text) || 'Hemen Giris Yap'
    },
    previewSlogan() {
      return this.overrides.slogan ? this.form.slogan : this.siteDefaults.slogan
    },
    previewLayout() {
      return (this.overrides.layout ? this.form.layout : this.siteDefaults.layout) || 'centered'
    },
  },
  created() {
    // Init overrides as all false
    HERO_FIELDS.forEach((f) => { this.$set(this.overrides, f, false) })
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
        const { data } = await getPostHeroSettings(this.siteId, this.postId)
        this.packages = data.data.packages
        this.siteDefaults = data.data.site_hero_settings || {}
        const postSettings = data.data.post_hero_settings || {}

        // Set form values and mark overrides
        HERO_FIELDS.forEach((key) => {
          if (postSettings[key] !== undefined && postSettings[key] !== null) {
            this.form[key] = postSettings[key]
            this.overrides[key] = true
          } else if (this.siteDefaults[key] !== undefined && this.siteDefaults[key] !== null) {
            this.form[key] = this.siteDefaults[key]
          }
        })
      } catch {
        this.$message.error('Hero ayarları yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    clearOverrides() {
      HERO_FIELDS.forEach((f) => { this.overrides[f] = false })
      // Reset form to site defaults
      HERO_FIELDS.forEach((key) => {
        if (this.siteDefaults[key] !== undefined) {
          this.form[key] = this.siteDefaults[key]
        }
      })
    },
    async handleSave() {
      this.saving = true
      // Only send overridden fields
      const payload = {}
      HERO_FIELDS.forEach((key) => {
        if (this.overrides[key]) {
          payload[key] = this.form[key]
        }
      })
      // Always send featured_image_in_hero (post-only field)
      payload.featured_image_in_hero = this.form.featured_image_in_hero

      try {
        await updatePostHeroSettings(this.siteId, this.postId, payload)
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
.inherited-value {
  color: #909399;
  font-size: 13px;
  font-style: italic;
}
.package-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.package-thumb {
  position: relative;
  width: 90px;
  height: 55px;
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
  font-size: 9px;
  text-shadow: 0 1px 3px rgba(0,0,0,0.8);
  z-index: 1;
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
