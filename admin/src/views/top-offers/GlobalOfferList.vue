<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
      <h2 style="margin: 0">Sponsorlar</h2>
      <div style="display: flex; gap: 10px">
        <el-button type="primary" icon="el-icon-plus" @click="openForm()">Sponsor Ekle</el-button>
        <el-button type="success" icon="el-icon-refresh" :loading="deploying" @click="handleDeploy">Güncellemeleri Yayınla</el-button>
      </div>
    </div>

    <!-- Kart Önizleme — test sitesindeki sıralama -->
    <div class="preview-section" v-loading="loading">
      <div class="preview-label">Site Önizleme (sıralama)</div>
      <div class="preview-grid">
        <div
          v-for="offer in offers"
          :key="offer.id"
          class="preview-card"
          :class="{ 'is-inactive': !offer.is_active }"
        >
          <img
            v-if="offer.logo_url"
            :src="offer.logo_url"
            class="preview-logo"
            alt=""
          />
          <div v-else class="preview-logo preview-logo--empty">—</div>
          <span class="preview-bonus">{{ offer.bonus_text }}</span>
          <span class="preview-cta">{{ offer.cta_text || 'ÜYE OL' }}</span>
          <div class="preview-actions">
            <el-button type="text" icon="el-icon-edit" size="mini" @click="openForm(offer)" />
            <el-button type="text" icon="el-icon-delete" size="mini" style="color:#f56c6c" @click="handleDelete(offer)" />
          </div>
          <span v-if="!offer.is_active" class="preview-badge">Pasif</span>
          <span class="preview-order">#{{ offer.sort_order }}</span>
        </div>
        <div v-if="!loading && offers.length === 0" class="preview-empty">
          Henüz sponsor eklenmemiş
        </div>
      </div>
    </div>

    <el-pagination
      v-if="total > perPage"
      style="margin-top: 16px; text-align: center"
      layout="prev, pager, next, total"
      :total="total"
      :page-size="perPage"
      :current-page.sync="currentPage"
      @current-change="fetchOffers"
    />

    <!-- Ekle / Düzenle Dialog -->
    <el-dialog :title="editingOffer ? 'Sponsor Düzenle' : 'Yeni Sponsor'" :visible.sync="dialogVisible" width="520px">
      <el-form ref="form" :model="form" :rules="rules" label-width="120px">
        <el-form-item label="Slug" prop="slug">
          <el-input v-model="form.slug" placeholder="sponsor-adi" />
          <div style="color: #909399; font-size: 12px; margin-top: 2px">/go/slug üzerinden yönlendirme yapılır</div>
        </el-form-item>
        <el-form-item label="Logo URL" prop="logo_url">
          <el-input v-model="form.logo_url" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Bonus Yazısı" prop="bonus_text">
          <el-input v-model="form.bonus_text" placeholder="200% Hoş Geldin Bonusu" />
        </el-form-item>
        <el-form-item label="Buton Yazısı" prop="cta_text">
          <el-input v-model="form.cta_text" placeholder="ÜYE OL" />
        </el-form-item>
        <el-form-item label="Hedef URL" prop="target_url">
          <el-input v-model="form.target_url" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Sıralama">
          <el-input-number v-model="form.sort_order" :min="0" />
          <span style="color: #909399; font-size: 12px; margin-left: 8px">Küçük = önce</span>
        </el-form-item>
        <el-form-item label="Aktif">
          <el-switch v-model="form.is_active" />
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button @click="dialogVisible = false">İptal</el-button>
        <el-button type="primary" :loading="saving" @click="handleSave">Kaydet</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getGlobalOffers, createGlobalOffer, updateGlobalOffer, deleteGlobalOffer, restartFrontend } from '../../api/topOffers'

export default {
  name: 'GlobalOfferList',
  data() {
    return {
      offers: [],
      loading: false,
      saving: false,
      deploying: false,
      currentPage: 1,
      perPage: 30,
      total: 0,
      dialogVisible: false,
      editingOffer: null,
      form: this.emptyForm(),
      rules: {
        logo_url: [
          { required: true, message: 'Zorunlu', trigger: 'blur' },
          { type: 'url', message: 'Geçerli bir URL girin (https://...)', trigger: 'blur' },
        ],
        bonus_text: [{ required: true, message: 'Zorunlu', trigger: 'blur' }],
        cta_text: [{ required: true, message: 'Zorunlu', trigger: 'blur' }],
        target_url: [
          { required: true, message: 'Zorunlu', trigger: 'blur' },
          { type: 'url', message: 'Geçerli bir URL girin (https://...)', trigger: 'blur' },
        ],
      },
    }
  },
  created() {
    this.fetchOffers()
  },
  methods: {
    emptyForm() {
      return { slug: '', logo_url: '', bonus_text: '', cta_text: '', target_url: '', sort_order: 0, is_active: true }
    },
    async fetchOffers(page) {
      this.loading = true
      try {
        const { data } = await getGlobalOffers({ page: page || this.currentPage, per_page: this.perPage })
        this.offers = data.data || []
        this.total = data.total || 0
      } catch {
        this.$message.error('Sponsorlar yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    openForm(offer) {
      this.editingOffer = offer || null
      this.form = offer ? { ...offer } : this.emptyForm()
      this.dialogVisible = true
      this.$nextTick(() => this.$refs.form?.clearValidate())
    },
    handleSave() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.saving = true
        try {
          if (this.editingOffer) {
            await updateGlobalOffer(this.editingOffer.id, this.form)
            this.$message.success('Sponsor güncellendi')
          } else {
            await createGlobalOffer(this.form)
            this.$message.success('Sponsor eklendi')
          }
          this.dialogVisible = false
          this.fetchOffers()
        } catch (err) {
          const errors = err?.response?.data?.errors
          if (errors) {
            const msg = Object.values(errors).flat().join('\n')
            this.$message.error(msg)
          } else {
            this.$message.error('Kaydetme başarısız')
          }
        } finally {
          this.saving = false
        }
      })
    },
    async handleDeploy() {
      this.deploying = true
      try {
        const { data } = await restartFrontend()
        this.$message.success(data.message || 'Güncellemeler yayınlandı')
      } catch {
        this.$message.error('Yayınlama başarısız')
      } finally {
        this.deploying = false
      }
    },
    async handleDelete(offer) {
      try {
        await this.$confirm(`"${offer.bonus_text}" sponsorunu silmek istediğinize emin misiniz?`, 'Uyarı', {
          confirmButtonText: 'Sil',
          cancelButtonText: 'İptal',
          type: 'warning',
        })
        await deleteGlobalOffer(offer.id)
        this.$message.success('Sponsor silindi')
        this.fetchOffers()
      } catch (err) {
        if (err !== 'cancel') this.$message.error('Silme başarısız')
      }
    },
  },
}
</script>

<style scoped>
.preview-section {
  background: #0f0f23;
  border-radius: 8px;
  padding: 16px;
  min-height: 100px;
}

.preview-label {
  color: #666;
  font-size: 12px;
  margin-bottom: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.preview-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

.preview-card {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #16213e;
  border-radius: 8px;
  padding: 10px 14px;
  position: relative;
  cursor: pointer;
  transition: background 0.2s;
}

.preview-card:hover {
  background: #1c2a4a;
}

.preview-card.is-inactive {
  opacity: 0.45;
}

.preview-logo {
  width: 44px;
  height: 44px;
  object-fit: contain;
  border-radius: 6px;
  flex-shrink: 0;
}

.preview-logo--empty {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #1a1a3e;
  color: #555;
  font-size: 18px;
}

.preview-bonus {
  flex: 1;
  font-size: 13px;
  font-weight: 600;
  color: #e0e0e0;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.preview-cta {
  flex-shrink: 0;
  padding: 5px 14px;
  background: #007bff;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  border-radius: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.preview-actions {
  position: absolute;
  top: 4px;
  right: 4px;
  display: none;
  gap: 0;
}

.preview-card:hover .preview-actions {
  display: flex;
}

.preview-actions .el-button {
  padding: 4px;
  font-size: 14px;
}

.preview-badge {
  position: absolute;
  top: -6px;
  left: 8px;
  background: #f56c6c;
  color: #fff;
  font-size: 10px;
  padding: 1px 6px;
  border-radius: 3px;
  font-weight: 600;
}

.preview-order {
  position: absolute;
  bottom: 2px;
  right: 8px;
  color: #555;
  font-size: 10px;
  font-weight: 600;
}

.preview-empty {
  grid-column: 1 / -1;
  text-align: center;
  color: #555;
  padding: 24px;
  font-size: 14px;
}
</style>
