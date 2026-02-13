<template>
  <div class="sponsor-page">
    <!-- Header -->
    <div class="page-header">
      <h2 class="page-title">Sponsorlar</h2>
      <el-button type="primary" icon="el-icon-plus" @click="openCreateModal">
        Site Ekle
      </el-button>
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
            <el-form-item label="Web Background Görsel URL" prop="web_background">
              <el-input
                v-model="form.web_background"
                placeholder="Banner URL veya dosya adı"
                clearable
              >
                <i slot="prefix" class="el-icon-picture-outline"></i>
              </el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="Mobil Background Görsel URL" prop="mobile_background">
              <el-input
                v-model="form.mobile_background"
                placeholder="Mobil banner URL veya dosya adı"
                clearable
              >
                <i slot="prefix" class="el-icon-mobile-phone"></i>
              </el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <!-- Logo -->
        <el-form-item label="Logo Görsel URL" prop="logo">
          <el-input
            v-model="form.logo"
            placeholder="Logo URL veya dosya adı"
            clearable
          >
            <i slot="prefix" class="el-icon-picture"></i>
          </el-input>
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

const NAME_REGEX = /^[a-z0-9]+$/
const URL_REGEX = /^https?:\/\/.+\..+/

export default {
  name: 'SponsorList',

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
