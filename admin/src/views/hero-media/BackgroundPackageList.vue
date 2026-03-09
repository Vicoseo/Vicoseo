<template>
  <div class="bg-package-page">
    <div class="page-header">
      <h2 class="page-title">Medya Alanı — Arkaplan Paketleri</h2>
      <el-button type="primary" icon="el-icon-plus" @click="openCreate">
        Paket Ekle
      </el-button>
    </div>

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
</style>
