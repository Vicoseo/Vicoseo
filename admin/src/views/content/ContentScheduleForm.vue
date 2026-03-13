<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px">
      <h3 style="margin: 0">İçerik Planları</h3>
      <el-button size="small" type="primary" @click="openCreateDialog">Yeni Plan</el-button>
    </div>

    <el-table :data="schedules" v-loading="loading" size="small" border>
      <el-table-column label="Sıklık" prop="frequency" width="100">
        <template slot-scope="{ row }">
          {{ row.frequency === 'daily' ? 'Günlük' : 'Özel' }}
        </template>
      </el-table-column>
      <el-table-column label="Saat" prop="run_at" width="80" />
      <el-table-column label="Aralık (saat)" prop="interval_hours" width="110">
        <template slot-scope="{ row }">
          {{ row.interval_hours || '-' }}
        </template>
      </el-table-column>
      <el-table-column label="Konular">
        <template slot-scope="{ row }">
          <el-tag v-for="t in (row.topics || [])" :key="t" size="mini" style="margin-right: 4px">{{ t }}</el-tag>
          <span v-if="!row.topics || !row.topics.length" style="color: #909399">Tüm konular</span>
        </template>
      </el-table-column>
      <el-table-column label="Öğe" width="60" align="center">
        <template slot-scope="{ row }">
          <el-tag v-if="row.items && row.items.length" size="mini" type="success">{{ row.items.length }}</el-tag>
          <span v-else style="color: #C0C4CC">-</span>
        </template>
      </el-table-column>
      <el-table-column label="Aktif" width="70" align="center">
        <template slot-scope="{ row }">
          <el-switch v-model="row.is_active" @change="toggleActive(row)" size="mini" />
        </template>
      </el-table-column>
      <el-table-column label="Son Çalışma" width="150">
        <template slot-scope="{ row }">
          {{ row.last_run_at || '-' }}
        </template>
      </el-table-column>
      <el-table-column label="İşlemler" width="140" align="center">
        <template slot-scope="{ row }">
          <el-button size="mini" @click="openEditDialog(row)">Düzenle</el-button>
          <el-button size="mini" type="danger" @click="handleDelete(row)">Sil</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :title="dialogForm.id ? 'Plan Düzenle' : 'Yeni Plan'" :visible.sync="dialogVisible" width="700px" top="5vh">
      <el-form :model="dialogForm" label-width="130px" size="small">
        <el-form-item label="Sıklık">
          <el-select v-model="dialogForm.frequency">
            <el-option label="Günlük" value="daily" />
            <el-option label="Özel" value="custom" />
          </el-select>
        </el-form-item>
        <el-form-item label="Çalışma Saati">
          <el-time-select
            v-model="dialogForm.run_at"
            :picker-options="{ start: '00:00', step: '01:00', end: '23:00' }"
            placeholder="Saat seçin"
          />
        </el-form-item>
        <el-form-item label="Aralık (saat)" v-if="dialogForm.frequency === 'custom'">
          <el-input-number v-model="dialogForm.interval_hours" :min="1" :max="168" />
        </el-form-item>
        <el-form-item label="Konular">
          <el-select v-model="dialogForm.topics" multiple placeholder="Boş = Tüm konular">
            <el-option label="Erişim" value="erisim" />
            <el-option label="Bonus" value="bonus" />
            <el-option label="Mobil" value="mobil" />
            <el-option label="Ödeme" value="odeme" />
            <el-option label="Oyun" value="oyun" />
            <el-option label="Güvenlik" value="guvenlik" />
            <el-option label="Genel" value="genel" />
          </el-select>
        </el-form-item>
        <el-form-item label="Aktif">
          <el-switch v-model="dialogForm.is_active" />
        </el-form-item>

        <!-- İçerik Öğeleri -->
        <el-divider content-position="left">İçerik Öğeleri</el-divider>
        <div style="font-size: 12px; color: #909399; margin-bottom: 12px">
          Konu başlığı ve opsiyonel görsel ekleyin. Görsel için alana tıklayıp Ctrl+V ile yapıştırabilir veya dosya seçebilirsiniz.
        </div>

        <div
          v-for="(item, index) in dialogForm.items"
          :key="index"
          style="border: 1px solid #EBEEF5; border-radius: 4px; padding: 12px; margin-bottom: 10px; position: relative"
        >
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px">
            <span style="font-size: 12px; color: #909399; min-width: 20px">{{ index + 1 }}.</span>
            <el-input
              v-model="item.topic"
              placeholder="Konu başlığı yazın..."
              size="small"
              style="flex: 1"
            />
            <el-button
              size="mini"
              type="danger"
              icon="el-icon-delete"
              circle
              @click="removeItem(index)"
            />
          </div>
          <div style="margin-left: 28px">
            <div
              v-if="item.image_url"
              style="display: flex; align-items: center; gap: 8px"
            >
              <img
                :src="getImageFullUrl(item.image_url)"
                style="height: 60px; border-radius: 4px; object-fit: cover"
              />
              <el-button size="mini" type="text" @click="item.image_url = null">Kaldır</el-button>
            </div>
            <div
              v-else
              class="paste-area"
              :class="{ 'paste-area--active': pasteActiveIndex === index }"
              @click="triggerFileInput(index)"
              @paste="handlePaste($event, index)"
              @dragover.prevent="pasteActiveIndex = index"
              @dragleave="pasteActiveIndex = null"
              @drop.prevent="handleDrop($event, index)"
              tabindex="0"
            >
              <i v-if="uploadingIndex === index" class="el-icon-loading" />
              <template v-else>
                <i class="el-icon-picture-outline" style="font-size: 18px" />
                <span>Yapıştır (Ctrl+V) veya tıkla</span>
              </template>
            </div>
          </div>
        </div>

        <el-button
          size="small"
          type="text"
          icon="el-icon-plus"
          @click="addItem"
          style="margin-top: 4px"
        >
          Öğe Ekle
        </el-button>

        <input
          ref="fileInput"
          type="file"
          accept="image/*"
          style="display: none"
          @change="handleFileSelect"
        />
      </el-form>
      <span slot="footer">
        <el-button @click="dialogVisible = false">İptal</el-button>
        <el-button type="primary" :loading="saving" @click="handleSave">Kaydet</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import {
  getContentSchedules,
  createContentSchedule,
  updateContentSchedule,
  deleteContentSchedule,
} from '../../api/contentSchedules'
import { uploadImage } from '../../api/upload'

export default {
  name: 'ContentScheduleForm',
  props: {
    siteId: { type: [Number, String], required: true },
  },
  data() {
    return {
      schedules: [],
      loading: false,
      saving: false,
      dialogVisible: false,
      dialogForm: this.emptyForm(),
      uploadingIndex: null,
      pasteActiveIndex: null,
      fileInputTargetIndex: null,
    }
  },
  created() {
    this.fetchSchedules()
  },
  methods: {
    emptyForm() {
      return {
        id: null,
        frequency: 'daily',
        run_at: '06:00',
        interval_hours: null,
        topics: [],
        items: [],
        is_active: true,
      }
    },
    async fetchSchedules() {
      this.loading = true
      try {
        const { data } = await getContentSchedules(this.siteId)
        this.schedules = data.data
      } catch {
        this.$message.error('Planlar yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    openCreateDialog() {
      this.dialogForm = this.emptyForm()
      this.dialogVisible = true
    },
    openEditDialog(row) {
      this.dialogForm = {
        id: row.id,
        frequency: row.frequency,
        run_at: row.run_at,
        interval_hours: row.interval_hours,
        topics: row.topics || [],
        items: (row.items || []).map(i => ({ ...i })),
        is_active: row.is_active,
      }
      this.dialogVisible = true
    },
    async handleSave() {
      this.saving = true
      try {
        const items = this.dialogForm.items.filter(i => i.topic && i.topic.trim())
        const payload = {
          frequency: this.dialogForm.frequency,
          run_at: this.dialogForm.run_at,
          interval_hours: this.dialogForm.frequency === 'custom' ? this.dialogForm.interval_hours : null,
          topics: this.dialogForm.topics.length ? this.dialogForm.topics : null,
          items: items.length ? items : null,
          is_active: this.dialogForm.is_active,
        }
        if (this.dialogForm.id) {
          await updateContentSchedule(this.siteId, this.dialogForm.id, payload)
          this.$message.success('Plan güncellendi')
        } else {
          await createContentSchedule(this.siteId, payload)
          this.$message.success('Plan oluşturuldu')
        }
        this.dialogVisible = false
        this.fetchSchedules()
      } catch (err) {
        const msg = err.response?.data?.message || 'Plan kaydedilemedi'
        this.$message.error(msg)
      } finally {
        this.saving = false
      }
    },
    async toggleActive(row) {
      try {
        await updateContentSchedule(this.siteId, row.id, { is_active: row.is_active })
      } catch {
        row.is_active = !row.is_active
        this.$message.error('Durum güncellenemedi')
      }
    },
    async handleDelete(row) {
      try {
        await this.$confirm('Bu plan silinecek. Emin misiniz?', 'Uyarı', {
          confirmButtonText: 'Sil',
          cancelButtonText: 'İptal',
          type: 'warning',
        })
      } catch {
        return
      }
      try {
        await deleteContentSchedule(this.siteId, row.id)
        this.$message.success('Plan silindi')
        this.fetchSchedules()
      } catch {
        this.$message.error('Plan silinemedi')
      }
    },

    // ─── Items ───
    addItem() {
      this.dialogForm.items.push({ topic: '', image_url: null })
    },
    removeItem(index) {
      this.dialogForm.items.splice(index, 1)
    },

    // ─── Image Upload ───
    getImageFullUrl(url) {
      if (!url) return ''
      if (url.startsWith('http')) return url
      const base = process.env.VUE_APP_API_URL || ''
      return base.replace('/api/v1', '') + url
    },
    triggerFileInput(index) {
      this.fileInputTargetIndex = index
      this.$refs.fileInput.value = ''
      this.$refs.fileInput.click()
    },
    handleFileSelect(event) {
      const file = event.target.files[0]
      if (file && this.fileInputTargetIndex !== null) {
        this.uploadItemImage(file, this.fileInputTargetIndex)
      }
    },
    handlePaste(event, index) {
      const clipboardItems = event.clipboardData?.items
      if (!clipboardItems) return
      for (const item of clipboardItems) {
        if (item.type.startsWith('image/')) {
          event.preventDefault()
          const file = item.getAsFile()
          this.uploadItemImage(file, index)
          return
        }
      }
    },
    handleDrop(event, index) {
      this.pasteActiveIndex = null
      const file = event.dataTransfer?.files?.[0]
      if (file && file.type.startsWith('image/')) {
        this.uploadItemImage(file, index)
      }
    },
    async uploadItemImage(file, index) {
      this.uploadingIndex = index
      try {
        const { data } = await uploadImage(file, 'content-plans')
        this.$set(this.dialogForm.items[index], 'image_url', data.url)
        this.$message.success('Görsel yüklendi')
      } catch {
        this.$message.error('Görsel yüklenemedi')
      } finally {
        this.uploadingIndex = null
      }
    },
  },
}
</script>

<style scoped>
.paste-area {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  border: 2px dashed #DCDFE6;
  border-radius: 4px;
  cursor: pointer;
  color: #909399;
  font-size: 12px;
  transition: border-color 0.2s, background-color 0.2s;
  outline: none;
}
.paste-area:hover,
.paste-area:focus {
  border-color: #409EFF;
  background-color: #F0F7FF;
}
.paste-area--active {
  border-color: #67C23A;
  background-color: #F0F9EB;
}
</style>
