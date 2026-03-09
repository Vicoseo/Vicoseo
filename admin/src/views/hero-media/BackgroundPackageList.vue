<template>
  <div class="bg-package-page">
    <div class="page-header">
      <h2 class="page-title">Medya Alani — Arkaplan Paketleri</h2>
      <el-button type="primary" icon="el-icon-plus" @click="openCreate">
        Paket Ekle
      </el-button>
    </div>

    <!-- Kullanim Kilavuzu -->
    <el-card shadow="never" class="guide-card">
      <div slot="header" class="guide-header" @click="guideOpen = !guideOpen" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center">
        <span><i class="el-icon-info" style="margin-right: 6px; color: #409EFF"></i>Medya Alani Kullanim Kilavuzu</span>
        <i :class="guideOpen ? 'el-icon-arrow-up' : 'el-icon-arrow-down'" style="color: #909399"></i>
      </div>
      <div v-show="guideOpen">
        <el-steps :active="0" direction="vertical" finish-status="wait" class="guide-steps">
          <el-step title="1. Arkaplan Paketi Olusturun" description='Yukaridaki "Paket Ekle" butonuna tiklayin. 1920x600px boyutunda, koyu tonlu, soyut/atmosferik bir JPG gorsel yukleyin.' />
          <el-step title="2. Siteye Atayin" description='Dropdown ile paketi bir siteye atayin. Her sitenin aktif olan ilk paketi o sitenin yazi sayfalarinda hero arkaplan olarak kullanilir.' />
          <el-step title="3. Sonucu Goruntuleyln">
            <template slot="description">
              Sitenin blog yazi sayfalarini ziyaret edin. Hero bolumu otomatik gorunecektir: koyu arkaplan, overlay ve baslik.
              <strong>Hero atanmamis sitelerde</strong> eski duzen korunur.
            </template>
          </el-step>
        </el-steps>

        <el-alert
          title="Gorsel Ozellikleri"
          type="info"
          :closable="false"
          show-icon
          style="margin-top: 12px"
        >
          <span>
            Arkaplan gorselleri <strong>1920x600px, JPG, 200KB alti</strong> olmalidir.
            Soyut/atmosferik gorseller kullanin (metin veya marka logosu icermeyen).
            Overlay katmani (%65-80 opaklik) gorseli karartarak metnin okunabilirligini saglar.
          </span>
        </el-alert>
      </div>
    </el-card>

    <el-card shadow="never">
      <el-table :data="packages" v-loading="loading" stripe style="width: 100%">
        <el-table-column label="Onizleme" width="140" align="center">
          <template slot-scope="{ row }">
            <div class="thumb-preview" v-if="row.image_url">
              <img :src="resolveUrl(row.image_url)" class="thumb-img" alt="" />
              <div class="thumb-overlay" :style="{ backgroundColor: row.overlay_color }"></div>
            </div>
            <span v-else class="no-logo">—</span>
          </template>
        </el-table-column>
        <el-table-column prop="name" label="Ad" min-width="140" />
        <el-table-column label="Site" min-width="160">
          <template slot-scope="{ row }">
            <span v-if="row.site">{{ row.site.name }}</span>
            <span v-else style="color: #c0c4cc">Atanmamis</span>
          </template>
        </el-table-column>
        <el-table-column label="Aktif" width="80" align="center">
          <template slot-scope="{ row }">
            <el-switch v-model="row.is_active" @change="toggleActive(row)" />
          </template>
        </el-table-column>
        <el-table-column label="Islem" width="140" align="center">
          <template slot-scope="{ row }">
            <el-button size="mini" icon="el-icon-edit" @click="openEdit(row)" />
            <el-button size="mini" type="danger" icon="el-icon-delete" @click="handleDelete(row)" />
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- Create / Edit Dialog -->
    <el-dialog :title="editingId ? 'Paketi Duzenle' : 'Yeni Paket'" :visible.sync="dialogVisible" width="640px" @close="resetForm">
      <!-- Mini Hero Preview -->
      <div v-if="form.image_url" class="dialog-preview">
        <img :src="resolveUrl(form.image_url)" class="dialog-preview-bg" alt="" />
        <div class="dialog-preview-overlay" :style="{ backgroundColor: form.overlay_color }"></div>
        <div class="dialog-preview-text">Ornek Baslik</div>
      </div>

      <el-form ref="form" :model="form" :rules="rules" label-width="140px" size="small">
        <el-form-item label="Paket Adi" prop="name">
          <el-input v-model="form.name" />
        </el-form-item>
        <el-form-item label="Arkaplan Gorseli" prop="image_url">
          <ImageUpload v-model="form.image_url" directory="hero-backgrounds" />
        </el-form-item>
        <el-form-item label="Site">
          <el-select v-model="form.site_id" placeholder="Site secin" clearable filterable style="width: 100%">
            <el-option v-for="s in sites" :key="s.id" :label="s.name" :value="s.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="Overlay Rengi">
          <el-input v-model="form.overlay_color" placeholder="rgba(10,10,26,0.75)" />
        </el-form-item>
        <el-form-item label="Aktif">
          <el-switch v-model="form.is_active" />
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button @click="dialogVisible = false">Iptal</el-button>
        <el-button type="primary" :loading="saving" @click="handleSave">Kaydet</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getBackgroundPackages, createBackgroundPackage, updateBackgroundPackage, deleteBackgroundPackage } from '../../api/heroMedia'
import { getSites } from '../../api/sites'
import ImageUpload from '../../components/ImageUpload.vue'

const BACKEND_URL = process.env.VUE_APP_API_BASE_URL || ''

export default {
  name: 'BackgroundPackageList',
  components: { ImageUpload },
  data() {
    return {
      guideOpen: false,
      packages: [],
      sites: [],
      loading: false,
      saving: false,
      dialogVisible: false,
      editingId: null,
      form: {
        name: '',
        image_url: '',
        site_id: null,
        overlay_color: 'rgba(10,10,26,0.75)',
        is_active: true,
      },
      rules: {
        name: [{ required: true, message: 'Paket adi zorunludur', trigger: 'blur' }],
        image_url: [{ required: true, message: 'Gorsel zorunludur', trigger: 'change' }],
      },
    }
  },
  created() {
    this.fetchPackages()
    this.fetchSites()
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
        this.$message.error('Paketler yuklenemedi')
      } finally {
        this.loading = false
      }
    },
    async fetchSites() {
      try {
        const { data } = await getSites({ per_page: 100 })
        this.sites = data.data
      } catch {
        // silent
      }
    },
    openCreate() {
      this.editingId = null
      this.resetForm()
      this.dialogVisible = true
    },
    openEdit(row) {
      this.editingId = row.id
      this.form = {
        name: row.name,
        image_url: row.image_url,
        site_id: row.site_id,
        overlay_color: row.overlay_color || 'rgba(10,10,26,0.75)',
        is_active: row.is_active,
      }
      this.dialogVisible = true
    },
    resetForm() {
      this.form = {
        name: '',
        image_url: '',
        site_id: null,
        overlay_color: 'rgba(10,10,26,0.75)',
        is_active: true,
      }
      this.editingId = null
    },
    async handleSave() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.saving = true
        try {
          if (this.editingId) {
            await updateBackgroundPackage(this.editingId, this.form)
            this.$message.success('Paket guncellendi')
          } else {
            await createBackgroundPackage(this.form)
            this.$message.success('Paket olusturuldu')
          }
          this.dialogVisible = false
          this.fetchPackages()
        } catch (err) {
          const errors = err.response?.data?.errors
          if (errors) {
            const first = Object.values(errors)[0]
            this.$message.error(Array.isArray(first) ? first[0] : first)
          } else {
            this.$message.error('Kayit basarisiz')
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
        this.$message.error('Durum guncellenemedi')
      }
    },
    async handleDelete(row) {
      try {
        await this.$confirm(`"${row.name}" paketi silinecek. Emin misiniz?`, 'Sil', {
          confirmButtonText: 'Sil',
          cancelButtonText: 'Iptal',
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
        this.$message.error('Silme basarisiz')
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
</style>
