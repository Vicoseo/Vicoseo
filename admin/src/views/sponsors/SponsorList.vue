<template>
  <div class="sponsor-page">
    <!-- Header -->
    <div class="page-header">
      <h2 class="page-title">Sponsorlar</h2>
      <el-button type="primary" icon="el-icon-plus" @click="openCreateModal">
        Site Ekle
      </el-button>
    </div>

    <!-- Site Önizleme — Sponsor Şeridi -->
    <div class="preview-card" style="margin-bottom: 16px">
      <div class="preview-header">
        <div class="preview-label">
          <i class="el-icon-view" style="margin-right: 6px"></i>
          Site Önizleme — Sponsor Şeridi
        </div>
        <el-switch
          v-model="tickerEnabled"
          active-text="Aktif"
          inactive-text="Pasif"
          active-color="#13ce66"
          inactive-color="#dcdfe6"
        />
      </div>
      <div v-if="tickerEnabled && activeSponsors.length" class="preview-site">
        <!-- Kategori butonları -->
        <div class="preview-categories">
          <span
            v-for="cat in categories"
            :key="cat.key"
            class="preview-cat-btn"
            :class="{ active: previewCategory === cat.key }"
            @click="previewCategory = cat.key"
          >{{ cat.label }}</span>
        </div>
        <!-- Kayan şerit -->
        <div class="preview-slider">
          <div class="preview-track" :style="{ animationDuration: tickerSpeed + 's' }">
            <a
              v-for="sponsor in tickerSponsors"
              :key="'t-' + sponsor._tickerId"
              :href="sponsor.url"
              target="_blank"
              class="preview-sponsor-card"
              :style="{ borderLeftColor: sponsor.primary_color }"
            >
              <img
                v-if="sponsor.logo"
                :src="sponsor.logo"
                :alt="sponsor.name"
                class="preview-sponsor-logo"
              />
              <div class="preview-sponsor-info">
                <span class="preview-sponsor-name">{{ sponsor.name }}</span>
                <span v-if="sponsor.promotions && sponsor.promotions.length" class="preview-sponsor-promo">{{ sponsor.promotions[0].highlight }}</span>
              </div>
              <div class="preview-sponsor-rating">
                <span v-for="s in sponsor.rating" :key="'s'+s">★</span><span v-for="e in (5 - sponsor.rating)" :key="'e'+e" style="opacity: 0.3">★</span>
              </div>
            </a>
          </div>
        </div>
      </div>
      <div v-else-if="tickerEnabled && !activeSponsors.length" class="preview-empty">
        <i class="el-icon-warning-outline"></i> Aktif sponsor bulunmuyor — yukarıdaki tablodan sponsor ekleyin
      </div>
    </div>

    <!-- Sponsor Table -->
    <el-card shadow="never">
      <el-table :data="sponsors" v-loading="tableLoading" stripe style="width: 100%">
        <el-table-column label="Logo" width="80" align="center">
          <template slot-scope="{ row }">
            <img
              v-if="row.logo"
              :src="row.logo"
              class="sponsor-logo-thumb"
              alt=""
            />
            <span v-else class="no-logo">—</span>
          </template>
        </el-table-column>
        <el-table-column prop="name" label="Site Adı" min-width="120" />
        <el-table-column prop="url" label="URL" min-width="200" show-overflow-tooltip />
        <el-table-column label="Renk" width="80" align="center">
          <template slot-scope="{ row }">
            <span
              class="color-dot"
              :style="{ backgroundColor: row.primary_color }"
            ></span>
          </template>
        </el-table-column>
        <el-table-column prop="sort_order" label="Sıra" width="70" align="center" sortable />
        <el-table-column label="Puan" width="80" align="center">
          <template slot-scope="{ row }">
            <span>{{ row.rating }}/5</span>
          </template>
        </el-table-column>
        <el-table-column label="Kategori" width="120" align="center">
          <template slot-scope="{ row }">
            <el-tag v-if="row.category === 'vip'" size="small" type="warning">VIP</el-tag>
            <el-tag v-else-if="row.category === 'popular'" size="small" type="success">Popüler</el-tag>
            <el-tag v-else-if="row.category === 'kutu'" size="small" type="primary">Kutu</el-tag>
            <span v-else class="no-logo">—</span>
          </template>
        </el-table-column>
        <el-table-column label="Durum" width="90" align="center">
          <template slot-scope="{ row }">
            <el-switch
              v-model="row.is_active"
              active-color="#13ce66"
              inactive-color="#ff4949"
              @change="toggleActive(row)"
            />
          </template>
        </el-table-column>
        <el-table-column label="Promosyon" width="100" align="center">
          <template slot-scope="{ row }">
            <el-tag size="small" type="info">
              {{ (row.promotions || []).length }} adet
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="İşlemler" width="150" align="right">
          <template slot-scope="{ row }">
            <el-button type="text" icon="el-icon-edit" @click="openEditModal(row)">Düzenle</el-button>
            <el-button type="text" icon="el-icon-delete" style="color: #f56c6c" @click="handleDelete(row)">Sil</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- Create/Edit Modal -->
    <el-dialog
      :title="isEditing ? 'Site Düzenleyin' : 'Site Ekleyin'"
      :visible.sync="modalVisible"
      width="680px"
      :close-on-click-modal="false"
      custom-class="sponsor-dialog"
      @closed="resetForm"
    >
      <el-form
        ref="sponsorForm"
        :model="form"
        :rules="rules"
        label-position="top"
        class="sponsor-form"
      >
        <!-- Site Adı -->
        <el-form-item label="Site" prop="name">
          <el-input
            v-model="form.name"
            placeholder="Site adı girin (örn: bets10, 1xbet)"
            maxlength="50"
            show-word-limit
          />
        </el-form-item>

        <!-- URL -->
        <el-form-item label="URL" prop="url">
          <el-input
            v-model="form.url"
            placeholder="Site URL'sini girin"
          >
            <template slot="prepend">https://</template>
          </el-input>
        </el-form-item>

        <!-- Site Rengi -->
        <el-form-item label="Site Rengi" prop="primary_color">
          <div class="color-picker-row">
            <el-color-picker v-model="form.primary_color" show-alpha />
            <span
              class="color-preview"
              :style="{ backgroundColor: form.primary_color }"
            ></span>
            <span class="color-hex">{{ form.primary_color }}</span>
          </div>
        </el-form-item>

        <!-- İki Sütun: Web BG + Mobil BG -->
        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item label="Web Background Görsel" prop="web_background">
              <image-upload v-model="form.web_background" directory="backgrounds" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="Mobil Background Görsel" prop="mobile_background">
              <image-upload v-model="form.mobile_background" directory="backgrounds" />
            </el-form-item>
          </el-col>
        </el-row>

        <!-- Logo -->
        <el-form-item label="Logo Görsel" prop="logo">
          <image-upload v-model="form.logo" directory="logos" />
        </el-form-item>

        <!-- Kategori -->
        <el-form-item label="Kategori" prop="category">
          <el-select v-model="form.category" placeholder="Kategori seçin" clearable style="width: 100%">
            <el-option label="VIP Siteler" value="vip" />
            <el-option label="En Çok Tercih Edilen" value="popular" />
            <el-option label="Kutu Açılışı" value="kutu" />
          </el-select>
        </el-form-item>

        <!-- İki Sütun: Yıldız + Sıra No -->
        <el-row :gutter="16">
          <el-col :span="12">
            <el-form-item label="Yıldız Sayısı" prop="rating">
              <el-input-number
                v-model="form.rating"
                :min="0"
                :max="5"
                :step="1"
                style="width: 100%"
              />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="Sıra No" prop="sort_order">
              <el-input-number
                v-model="form.sort_order"
                :min="0"
                style="width: 100%"
              />
              <div class="sort-hint">Küçük numara = en başta görünür</div>
            </el-form-item>
          </el-col>
        </el-row>

        <!-- Promosyon Yazıları -->
        <el-form-item label="Promosyon Yazıları" class="promo-section">
          <div
            v-for="(promo, index) in form.promotions"
            :key="index"
            class="promo-row"
          >
            <el-form-item
              :prop="'promotions.' + index + '.highlight'"
              :rules="promoHighlightRules"
              class="promo-field"
            >
              <el-input
                v-model="promo.highlight"
                placeholder="Vurgu (örn: 5000 TL Bonus)"
              >
                <template slot="prepend">
                  <i class="el-icon-star-on"></i>
                </template>
              </el-input>
            </el-form-item>

            <el-form-item
              :prop="'promotions.' + index + '.text'"
              :rules="promoTextRules"
              class="promo-field"
            >
              <el-input
                v-model="promo.text"
                placeholder="Cümle (örn: Hoş geldin bonusu)"
              />
            </el-form-item>

            <el-button
              type="danger"
              icon="el-icon-minus"
              circle
              size="small"
              class="promo-remove-btn"
              :disabled="form.promotions.length <= 1"
              @click="removePromotion(index)"
            />
          </div>

          <el-button
            type="primary"
            icon="el-icon-plus"
            plain
            size="small"
            @click="addPromotion"
            class="promo-add-btn"
          >
            Promosyon Ekle
          </el-button>
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="modalVisible = false">İptal</el-button>
        <el-button
          type="primary"
          :loading="submitLoading"
          @click="handleSubmit"
        >
          İşlemi Tamamla
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { getSponsors, createSponsor, updateSponsor, deleteSponsor } from '../../api/sponsors'
import ImageUpload from '../../components/ImageUpload.vue'

const NAME_REGEX = /^[a-z0-9]+$/
const URL_REGEX = /^https?:\/\/.+\..+/

export default {
  name: 'SponsorList',
  components: { ImageUpload },

  data() {
    const validateName = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('Site adı zorunludur'))
      }
      if (!NAME_REGEX.test(value)) {
        return callback(new Error('Sadece küçük harf ve rakam kullanılabilir'))
      }
      callback()
    }

    const validateUrl = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('URL zorunludur'))
      }
      const full = value.startsWith('http') ? value : 'https://' + value
      if (!URL_REGEX.test(full)) {
        return callback(new Error('Geçerli bir URL giriniz'))
      }
      callback()
    }

    return {
      // Preview ticker
      tickerEnabled: true,
      previewCategory: null,
      categories: [
        { key: null, label: 'Tümü' },
        { key: 'vip', label: 'VIP Siteler' },
        { key: 'popular', label: 'En Çok Tercih Edilen' },
        { key: 'kutu', label: 'Kutu Açılışı' },
      ],

      // Table
      sponsors: [],
      tableLoading: false,

      // Modal
      modalVisible: false,
      submitLoading: false,
      isEditing: false,
      editingId: null,

      // Form
      form: {
        name: '',
        url: '',
        primary_color: '#409EFF',
        web_background: '',
        mobile_background: '',
        logo: '',
        rating: 5,
        sort_order: 0,
        category: '',
        promotions: [{ highlight: '', text: '' }],
      },

      // Rules
      rules: {
        name: [{ validator: validateName, trigger: 'blur' }],
        url: [{ validator: validateUrl, trigger: 'blur' }],
        primary_color: [{ required: true, message: 'Site rengi zorunludur', trigger: 'change' }],
        rating: [{ required: true, message: 'Yıldız sayısı zorunludur', trigger: 'change' }],
      },

      promoHighlightRules: [
        { required: true, message: 'Vurgu alanı zorunludur', trigger: 'blur' },
      ],
      promoTextRules: [
        { required: true, message: 'Cümle alanı zorunludur', trigger: 'blur' },
      ],
    }
  },

  computed: {
    activeSponsors() {
      return this.sponsors.filter((s) => s.is_active)
    },
    filteredPreviewSponsors() {
      if (!this.previewCategory) return this.activeSponsors
      return this.activeSponsors.filter((s) => s.category === this.previewCategory)
    },
    tickerSponsors() {
      const list = this.filteredPreviewSponsors
      if (!list.length) return []
      // Triple for smooth infinite loop
      const tripled = [...list, ...list, ...list]
      return tripled.map((s, i) => ({ ...s, _tickerId: s.id + '-' + i }))
    },
    tickerSpeed() {
      const count = this.filteredPreviewSponsors.length
      return Math.max(count * 3, 8)
    },
  },

  created() {
    this.fetchSponsors()
  },

  methods: {
    // ─── Data ──────────────────────────────────────
    async fetchSponsors() {
      this.tableLoading = true
      try {
        const { data } = await getSponsors()
        this.sponsors = data.data || data || []
      } catch {
        this.$message.error('Sponsorlar yüklenemedi')
      } finally {
        this.tableLoading = false
      }
    },

    // ─── Modal ─────────────────────────────────────
    openCreateModal() {
      this.isEditing = false
      this.editingId = null
      this.modalVisible = true
    },

    openEditModal(row) {
      this.isEditing = true
      this.editingId = row.id
      this.form = {
        name: row.name || '',
        url: (row.url || '').replace(/^https?:\/\//, ''),
        primary_color: row.primary_color || '#409EFF',
        web_background: row.web_background || '',
        mobile_background: row.mobile_background || '',
        logo: row.logo || '',
        rating: row.rating ?? 5,
        sort_order: row.sort_order ?? 0,
        category: row.category || '',
        promotions:
          row.promotions && row.promotions.length
            ? row.promotions.map((p) => ({ ...p }))
            : [{ highlight: '', text: '' }],
      }
      this.modalVisible = true
    },

    resetForm() {
      this.form = {
        name: '',
        url: '',
        primary_color: '#409EFF',
        web_background: '',
        mobile_background: '',
        logo: '',
        rating: 5,
        sort_order: 0,
        category: '',
        promotions: [{ highlight: '', text: '' }],
      }
      this.isEditing = false
      this.editingId = null
      this.$nextTick(() => {
        this.$refs.sponsorForm && this.$refs.sponsorForm.clearValidate()
      })
    },

    // ─── Promotions ────────────────────────────────
    addPromotion() {
      this.form.promotions.push({ highlight: '', text: '' })
    },

    removePromotion(index) {
      if (this.form.promotions.length > 1) {
        this.form.promotions.splice(index, 1)
      }
    },

    // ─── Submit ────────────────────────────────────
    handleSubmit() {
      this.$refs.sponsorForm.validate(async (valid) => {
        if (!valid) return

        this.submitLoading = true

        const payload = {
          name: this.form.name,
          url: this.form.url.startsWith('http')
            ? this.form.url
            : 'https://' + this.form.url,
          primary_color: this.form.primary_color,
          web_background: this.form.web_background || null,
          mobile_background: this.form.mobile_background || null,
          logo: this.form.logo || null,
          rating: this.form.rating,
          sort_order: this.form.sort_order,
          category: this.form.category || null,
          promotions: this.form.promotions.filter(
            (p) => p.highlight && p.text
          ),
        }

        try {
          if (this.isEditing) {
            await updateSponsor(this.editingId, payload)
            this.$message.success('Site başarıyla güncellendi')
          } else {
            await createSponsor(payload)
            this.$message.success('Site başarıyla eklendi')
          }
          this.modalVisible = false
          this.fetchSponsors()
        } catch (err) {
          const errors = err.response?.data?.errors
          if (errors) {
            const firstMsg = Object.values(errors).flat()[0]
            this.$message.error(firstMsg)
          } else {
            this.$message.error(
              err.response?.data?.message || 'İşlem sırasında bir hata oluştu'
            )
          }
        } finally {
          this.submitLoading = false
        }
      })
    },

    // ─── Toggle Active ─────────────────────────────
    async toggleActive(row) {
      try {
        await updateSponsor(row.id, { is_active: row.is_active })
        this.$message.success(row.is_active ? 'Sponsor aktif edildi' : 'Sponsor pasif edildi')
      } catch {
        row.is_active = !row.is_active
        this.$message.error('Durum güncellenemedi')
      }
    },

    // ─── Delete ────────────────────────────────────
    async handleDelete(row) {
      try {
        await this.$confirm(
          `"${row.name}" sitesini silmek istediğinize emin misiniz?`,
          'Uyarı',
          { confirmButtonText: 'Sil', cancelButtonText: 'İptal', type: 'warning' }
        )
        await deleteSponsor(row.id)
        this.$message.success('Site silindi')
        this.fetchSponsors()
      } catch (err) {
        if (err !== 'cancel') {
          this.$message.error('Silme işlemi başarısız')
        }
      }
    },
  },
}
</script>

<style scoped>
.sponsor-page {
  padding: 0;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.page-title {
  margin: 0;
  font-size: 22px;
  font-weight: 600;
  color: #303133;
}

/* Site Preview */
.preview-card {
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #ebeef5;
}

.preview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  background: #fff;
  border-bottom: 1px solid #ebeef5;
}

.preview-label {
  font-size: 14px;
  font-weight: 600;
  color: #303133;
}

.preview-site {
  background: #1a1a2e;
  padding: 12px 16px;
}

.preview-categories {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
  flex-wrap: wrap;
}

.preview-cat-btn {
  padding: 4px 14px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  background: transparent;
  color: #bbb;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s;
  user-select: none;
}

.preview-cat-btn:hover {
  border-color: rgba(255, 255, 255, 0.5);
  color: #fff;
}

.preview-cat-btn.active {
  background: #ffd700;
  border-color: #ffd700;
  color: #0a0a1a;
  font-weight: 700;
}

.preview-slider {
  overflow: hidden;
  position: relative;
  border-radius: 6px;
}

.preview-slider::before,
.preview-slider::after {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  width: 40px;
  z-index: 2;
  pointer-events: none;
}

.preview-slider::before {
  left: 0;
  background: linear-gradient(to right, #1a1a2e, transparent);
}

.preview-slider::after {
  right: 0;
  background: linear-gradient(to left, #1a1a2e, transparent);
}

.preview-track {
  display: flex;
  align-items: center;
  gap: 12px;
  white-space: nowrap;
  animation: preview-scroll linear infinite;
  width: max-content;
}

.preview-track:hover {
  animation-play-state: paused;
}

@keyframes preview-scroll {
  0% { transform: translateX(0); }
  100% { transform: translateX(-33.333%); }
}

.preview-sponsor-card {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 10px;
  background: #16213e;
  border-radius: 8px;
  padding: 10px 16px;
  border-left: 3px solid;
  text-decoration: none;
  color: #fff;
  min-width: 200px;
  transition: background 0.2s;
}

.preview-sponsor-card:hover {
  background: #1c2a4a;
}

.preview-sponsor-logo {
  width: 40px;
  height: 40px;
  object-fit: contain;
  border-radius: 6px;
  flex-shrink: 0;
}

.preview-sponsor-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.preview-sponsor-name {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  overflow: hidden;
  text-overflow: ellipsis;
}

.preview-sponsor-promo {
  font-size: 11px;
  color: #ffd700;
  font-weight: 600;
}

.preview-sponsor-rating {
  font-size: 11px;
  color: #ffd700;
  flex-shrink: 0;
}

.preview-empty {
  text-align: center;
  color: #909399;
  font-size: 13px;
  padding: 24px 16px;
  background: #f5f7fa;
}

/* Table */
.sponsor-logo-thumb {
  width: 36px;
  height: 36px;
  object-fit: contain;
  border-radius: 4px;
  border: 1px solid #ebeef5;
}

.no-logo {
  color: #c0c4cc;
}

.color-dot {
  display: inline-block;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #ebeef5;
  vertical-align: middle;
}

/* Color Picker Row */
.color-picker-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.color-preview {
  display: inline-block;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 1px solid #dcdfe6;
}

.color-hex {
  font-size: 13px;
  color: #909399;
  font-family: monospace;
}

/* Promotion Rows */
.promo-section >>> .el-form-item__content {
  line-height: normal;
}

.promo-row {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin-bottom: 12px;
  padding: 12px;
  background: #fafafa;
  border-radius: 6px;
  border: 1px solid #ebeef5;
}

.promo-field {
  flex: 1;
  margin-bottom: 0 !important;
}

.promo-remove-btn {
  margin-top: 4px;
  flex-shrink: 0;
}

.promo-add-btn {
  margin-top: 4px;
}

.sort-hint {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
  line-height: 1.2;
}

/* Dialog */
.dialog-footer {
  text-align: right;
}
</style>

<style>
.sponsor-dialog .el-dialog__header {
  border-bottom: 1px solid #ebeef5;
  padding-bottom: 16px;
}

.sponsor-dialog .el-dialog__body {
  max-height: 65vh;
  overflow-y: auto;
  padding: 20px 24px;
}

.sponsor-dialog .el-dialog__footer {
  border-top: 1px solid #ebeef5;
  padding-top: 16px;
}
</style>
