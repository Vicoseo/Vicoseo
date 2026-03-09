<template>
  <div class="bg-package-page">
    <div class="page-header">
      <h2 class="page-title">Medya Alanı — Arkaplan Paketleri</h2>
      <el-button type="primary" icon="el-icon-plus" @click="openCreate">
        Paket Ekle
      </el-button>
    </div>

    <!-- Kullanım Kılavuzu -->
    <el-card shadow="never" class="guide-card">
      <div slot="header" class="guide-header" @click="guideOpen = !guideOpen" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center">
        <span><i class="el-icon-info" style="margin-right: 6px; color: #409EFF"></i>Medya Alanı Kullanım Kılavuzu</span>
        <i :class="guideOpen ? 'el-icon-arrow-up' : 'el-icon-arrow-down'" style="color: #909399"></i>
      </div>
      <div v-show="guideOpen">
        <el-steps :active="0" direction="vertical" finish-status="wait" class="guide-steps">
          <el-step title="1. Arkaplan Paketi Oluşturun" description='Yukarıdaki "Paket Ekle" butonuna tıklayın. 1920x600px boyutunda, koyu tonlu, soyut/atmosferik bir JPG görsel yükleyin. Overlay rengi ve blend modu seçerek hero bölümünün karanlık tonunu ayarlayın. Casino siteleri için koyu lacivert/altın, spor siteleri için stadyum temalı görseller önerilir.' />
          <el-step title="2. Site Varsayılanlarını Belirleyin">
            <template slot="description">
              <span>
                <strong>Siteler</strong> sayfasından herhangi bir sitenin detay sayfasına gidin ve <strong>"Hero Ayarları"</strong> tab'ına tıklayın.
                Burada sitenin tüm yazılarında kullanılacak varsayılan hero ayarlarını belirleyin:
              </span>
              <ul class="guide-list">
                <li><strong>Arkaplan Paketi:</strong> Oluşturduğunuz paketlerden birini seçin</li>
                <li><strong>Vurgu Rengi:</strong> Rozet ve CTA butonunda kullanılır (boş bırakırsanız sitenin ana rengi kullanılır)</li>
                <li><strong>Rozet:</strong> "Güncel 2026" gibi bir etiket göstermek için açın</li>
                <li><strong>CTA Butonu:</strong> "Hemen Giriş Yap" gibi bir aksiyon butonu (URL boş bırakılırsa sitenin giriş adresi kullanılır)</li>
                <li><strong>Slogan:</strong> Başlığın altında görünen kısa tanıtım metni</li>
                <li><strong>Düzen:</strong> Ortalanmış veya sola hizalı (sola hizalıda öne çıkan görsel hero içinde gösterilir)</li>
              </ul>
              <span>Sağ taraftaki <strong>canlı önizleme</strong> panelinden değişiklikleri anında görebilirsiniz.</span>
            </template>
          </el-step>
          <el-step title="3. Yazı Bazında Özelleştirin (Opsiyonel)">
            <template slot="description">
              <span>
                Belirli bir yazı için farklı hero ayarları kullanmak isterseniz, yazının düzenleme sayfasındaki
                <strong>"Hero Düzenle"</strong> butonuna tıklayın. Her alan için "Özel değer kullan" kutusunu işaretleyerek
                sadece değiştirmek istediğiniz alanları override edin. İşaretlemediğiniz alanlar site varsayılanlarını kullanır.
                <strong>"Site Varsayılanlarını Kullan"</strong> butonu tüm post-level override'ları temizler.
              </span>
            </template>
          </el-step>
          <el-step title="4. Sonucu Görüntüleyin">
            <template slot="description">
              <span>
                Hero ayarları kaydedildikten sonra sitenin blog yazı sayfalarını ziyaret edin.
                Hero bölümü otomatik olarak görünecektir: koyu arkaplan, overlay, başlık, rozet, CTA butonu ve meta bilgileri.
                <strong>Hero ayarlanmamış sitelerde</strong> eski düzen korunur — hiçbir şey bozulmaz.
              </span>
            </template>
          </el-step>
        </el-steps>

        <el-alert
          title="Görsel Özellikleri"
          type="info"
          :closable="false"
          show-icon
          style="margin-top: 12px"
        >
          <span>
            Arkaplan görselleri <strong>1920x600px, JPG, 200KB altı</strong> olmalıdır.
            Soyut/atmosferik görseller kullanın (metin veya marka logosu içermeyen).
            Overlay katmanı (%65-80 opaklık) görseli karartarak metnin okunabilirliğini sağlar.
          </span>
        </el-alert>

        <div class="guide-pack-suggestions">
          <h4 style="margin: 16px 0 10px; font-size: 14px; color: #303133">Önerilen Paket Temaları</h4>
          <el-row :gutter="12">
            <el-col :span="8" v-for="pack in suggestedPacks" :key="pack.name">
              <div class="guide-pack-card" :style="{ borderLeftColor: pack.color }">
                <strong>{{ pack.name }}</strong>
                <p>{{ pack.desc }}</p>
                <code style="font-size: 11px">{{ pack.overlay }}</code>
              </div>
            </el-col>
          </el-row>
        </div>
      </div>
    </el-card>

    <el-card shadow="never">
      <el-table :data="packages" v-loading="loading" stripe style="width: 100%">
        <el-table-column label="Önizleme" width="140" align="center">
          <template slot-scope="{ row }">
            <div class="thumb-preview" v-if="row.image_url">
              <img :src="resolveUrl(row.thumbnail_url || row.image_url)" class="thumb-img" alt="" />
              <div class="thumb-overlay" :style="{ backgroundColor: row.overlay_color, mixBlendMode: row.overlay_blend }"></div>
            </div>
            <span v-else class="no-logo">—</span>
          </template>
        </el-table-column>
        <el-table-column prop="name" label="Paket Adı" min-width="140" />
        <el-table-column prop="slug" label="Slug" min-width="120" />
        <el-table-column label="Overlay" width="120" align="center">
          <template slot-scope="{ row }">
            <span class="color-chip" :style="{ backgroundColor: row.overlay_color }"></span>
          </template>
        </el-table-column>
        <el-table-column label="Etiketler" min-width="140">
          <template slot-scope="{ row }">
            <el-tag v-for="tag in (row.tags || [])" :key="tag" size="mini" style="margin: 2px">{{ tag }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="sort_order" label="Sıra" width="70" align="center" sortable />
        <el-table-column label="Aktif" width="80" align="center">
          <template slot-scope="{ row }">
            <el-switch v-model="row.is_active" @change="toggleActive(row)" />
          </template>
        </el-table-column>
        <el-table-column label="İşlem" width="140" align="center">
          <template slot-scope="{ row }">
            <el-button size="mini" icon="el-icon-edit" @click="openEdit(row)" />
            <el-button size="mini" type="danger" icon="el-icon-delete" @click="handleDelete(row)" />
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- Create / Edit Dialog -->
    <el-dialog :title="editingId ? 'Paketi Düzenle' : 'Yeni Paket'" :visible.sync="dialogVisible" width="640px" @close="resetForm">
      <!-- Mini Hero Preview -->
      <div v-if="form.image_url" class="dialog-preview">
        <img :src="resolveUrl(form.image_url)" class="dialog-preview-bg" alt="" />
        <div class="dialog-preview-overlay" :style="{ backgroundColor: form.overlay_color, mixBlendMode: form.overlay_blend }"></div>
        <div class="dialog-preview-text">Örnek Başlık</div>
      </div>

      <el-form ref="form" :model="form" :rules="rules" label-width="140px" size="small">
        <el-form-item label="Paket Adı" prop="name">
          <el-input v-model="form.name" @blur="autoSlug" />
        </el-form-item>
        <el-form-item label="Slug" prop="slug">
          <el-input v-model="form.slug" />
        </el-form-item>
        <el-form-item label="Açıklama">
          <el-input v-model="form.description" type="textarea" :rows="2" />
        </el-form-item>
        <el-form-item label="Arkaplan Görseli" prop="image_url">
          <ImageUpload v-model="form.image_url" directory="hero-backgrounds" />
        </el-form-item>
        <el-form-item label="Overlay Rengi">
          <el-input v-model="form.overlay_color" placeholder="rgba(10,10,26,0.75)" />
        </el-form-item>
        <el-form-item label="Overlay Blend">
          <el-select v-model="form.overlay_blend" style="width: 100%">
            <el-option label="Multiply" value="multiply" />
            <el-option label="Overlay" value="overlay" />
            <el-option label="Screen" value="screen" />
            <el-option label="Normal" value="normal" />
          </el-select>
        </el-form-item>
        <el-form-item label="Etiketler">
          <el-input v-model="tagsInput" placeholder="casino, premium, dark (virgülle ayırın)" />
        </el-form-item>
        <el-form-item label="Sıralama">
          <el-input-number v-model="form.sort_order" :min="0" />
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
import { getBackgroundPackages, createBackgroundPackage, updateBackgroundPackage, deleteBackgroundPackage } from '../../api/heroMedia'
import ImageUpload from '../../components/ImageUpload.vue'

const BACKEND_URL = process.env.VUE_APP_API_BASE_URL || ''

export default {
  name: 'BackgroundPackageList',
  components: { ImageUpload },
  data() {
    return {
      guideOpen: true,
      suggestedPacks: [
        { name: 'Royal Navy', desc: 'Casino, VIP siteleri icin koyu lacivert + altin tonlari', overlay: 'rgba(10,10,26,0.75)', color: '#ffd700' },
        { name: 'Midnight Stadium', desc: 'Spor bahis siteleri icin stadyum bokeh isiklari', overlay: 'rgba(10,15,30,0.72)', color: '#4dabf7' },
        { name: 'Velvet Cards', desc: 'Klasik casino siteleri icin koyu kirmizi/siyah', overlay: 'rgba(20,5,10,0.78)', color: '#e03131' },
      ],
      packages: [],
      loading: false,
      saving: false,
      dialogVisible: false,
      editingId: null,
      tagsInput: '',
      form: {
        name: '',
        slug: '',
        description: '',
        image_url: '',
        thumbnail_url: '',
        overlay_color: 'rgba(10,10,26,0.75)',
        overlay_blend: 'multiply',
        tags: [],
        sort_order: 0,
        is_active: true,
      },
      rules: {
        name: [{ required: true, message: 'Paket adı zorunludur', trigger: 'blur' }],
        slug: [{ required: true, message: 'Slug zorunludur', trigger: 'blur' }],
        image_url: [{ required: true, message: 'Görsel zorunludur', trigger: 'change' }],
      },
    }
  },
  created() {
    this.fetchPackages()
  },
  methods: {
    resolveUrl(url) {
      if (!url) return ''
      if (url.startsWith('http')) return url
      return BACKEND_URL + url
    },
    async fetchPackages() {
      this.loading = true
      try {
        const { data } = await getBackgroundPackages({ per_page: 100 })
        this.packages = data.data
      } catch {
        this.$message.error('Paketler yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    openCreate() {
      this.editingId = null
      this.resetForm()
      this.dialogVisible = true
    },
    openEdit(row) {
      this.editingId = row.id
      this.form = { ...row }
      this.tagsInput = (row.tags || []).join(', ')
      this.dialogVisible = true
    },
    resetForm() {
      this.form = {
        name: '',
        slug: '',
        description: '',
        image_url: '',
        thumbnail_url: '',
        overlay_color: 'rgba(10,10,26,0.75)',
        overlay_blend: 'multiply',
        tags: [],
        sort_order: 0,
        is_active: true,
      }
      this.tagsInput = ''
      this.editingId = null
    },
    autoSlug() {
      if (!this.editingId && !this.form.slug && this.form.name) {
        this.form.slug = this.form.name
          .toLowerCase()
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/^-|-$/g, '')
      }
    },
    async handleSave() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.saving = true
        const payload = { ...this.form }
        payload.tags = this.tagsInput ? this.tagsInput.split(',').map((t) => t.trim()).filter(Boolean) : []
        try {
          if (this.editingId) {
            await updateBackgroundPackage(this.editingId, payload)
            this.$message.success('Paket güncellendi')
          } else {
            await createBackgroundPackage(payload)
            this.$message.success('Paket oluşturuldu')
          }
          this.dialogVisible = false
          this.fetchPackages()
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
      })
    },
    async toggleActive(row) {
      try {
        await updateBackgroundPackage(row.id, { is_active: row.is_active })
      } catch {
        row.is_active = !row.is_active
        this.$message.error('Durum güncellenemedi')
      }
    },
    async handleDelete(row) {
      try {
        await this.$confirm(`"${row.name}" paketi silinecek. Emin misiniz?`, 'Sil', {
          confirmButtonText: 'Sil',
          cancelButtonText: 'İptal',
          type: 'warning',
        })
      } catch {
        return
      }
      try {
        await deleteBackgroundPackage(row.id)
        this.$message.success('Paket silindi')
        this.fetchPackages()
      } catch {
        this.$message.error('Silme başarısız')
      }
    },
  },
}
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.page-title {
  margin: 0;
  font-size: 20px;
}
.thumb-preview {
  position: relative;
  width: 120px;
  height: 50px;
  border-radius: 4px;
  overflow: hidden;
}
.thumb-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.thumb-overlay {
  position: absolute;
  inset: 0;
}
.color-chip {
  display: inline-block;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  border: 1px solid #dcdfe6;
}
.dialog-preview {
  position: relative;
  width: 100%;
  height: 100px;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 20px;
}
.dialog-preview-bg {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.dialog-preview-overlay {
  position: absolute;
  inset: 0;
}
.dialog-preview-text {
  position: absolute;
  bottom: 16px;
  left: 20px;
  color: #fff;
  font-size: 18px;
  font-weight: 700;
  text-shadow: 0 2px 8px rgba(0,0,0,0.5);
  z-index: 1;
}
.guide-card {
  margin-bottom: 20px;
}
.guide-card >>> .el-card__header {
  padding: 14px 20px;
  font-weight: 600;
  font-size: 15px;
}
.guide-card >>> .el-card__body {
  padding: 16px 20px 20px;
}
.guide-steps {
  margin-bottom: 0;
}
.guide-steps >>> .el-step__description {
  font-size: 13px;
  line-height: 1.7;
  color: #606266;
  padding-right: 20px;
}
.guide-list {
  margin: 8px 0;
  padding-left: 18px;
  font-size: 13px;
  line-height: 2;
}
.guide-list li {
  color: #606266;
}
.guide-pack-suggestions {
  border-top: 1px solid #ebeef5;
  padding-top: 4px;
}
.guide-pack-card {
  background: #f5f7fa;
  border-left: 3px solid;
  border-radius: 4px;
  padding: 10px 12px;
  margin-bottom: 8px;
}
.guide-pack-card strong {
  font-size: 13px;
  color: #303133;
}
.guide-pack-card p {
  font-size: 12px;
  color: #909399;
  margin: 4px 0 2px;
  line-height: 1.4;
}
</style>
