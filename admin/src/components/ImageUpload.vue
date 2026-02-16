<template>
  <div class="image-upload-wrapper">
    <el-upload
      class="image-uploader"
      :action="''"
      :auto-upload="false"
      :show-file-list="false"
      :on-change="handleFileChange"
      :before-upload="beforeUpload"
      accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml"
      drag
    >
      <div v-if="currentUrl" class="preview-container">
        <img :src="resolveUrl(currentUrl)" class="preview-image" alt="Preview" />
        <div class="preview-overlay">
          <i class="el-icon-edit"></i>
          <span>Değiştir</span>
        </div>
      </div>
      <div v-else class="upload-placeholder">
        <i class="el-icon-upload"></i>
        <div class="upload-text">Sürükle & bırak veya tıkla</div>
        <div class="upload-hint">JPG, PNG, GIF, WebP, SVG (max 2MB)</div>
      </div>
    </el-upload>
    <div v-if="currentUrl" class="upload-actions">
      <el-input :value="currentUrl" size="small" readonly class="url-display">
        <template slot="prepend"><i class="el-icon-link"></i></template>
      </el-input>
      <el-button
        type="danger"
        icon="el-icon-delete"
        size="small"
        circle
        @click="handleRemove"
      />
    </div>
  </div>
</template>

<script>
import { uploadImage } from '../api/upload'

const BACKEND_URL = process.env.VUE_APP_API_BASE_URL || ''

export default {
  name: 'ImageUpload',
  props: {
    value: { type: String, default: '' },
    directory: { type: String, default: 'sponsors' },
  },
  data() {
    return {
      uploading: false,
    }
  },
  computed: {
    currentUrl() {
      return this.value || ''
    },
  },
  methods: {
    resolveUrl(url) {
      if (!url) return ''
      if (url.startsWith('http')) return url
      return BACKEND_URL + url
    },
    beforeUpload(file) {
      const isValid = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'].includes(file.type)
      const isLt2M = file.size / 1024 / 1024 < 2

      if (!isValid) {
        this.$message.error('Sadece JPG, PNG, GIF, WebP veya SVG yükleyebilirsiniz')
        return false
      }
      if (!isLt2M) {
        this.$message.error('Dosya boyutu 2MB\'dan küçük olmalıdır')
        return false
      }
      return true
    },
    async handleFileChange(file) {
      if (!this.beforeUpload(file.raw)) return

      this.uploading = true
      try {
        const { data } = await uploadImage(file.raw, this.directory)
        this.$emit('input', data.url)
        this.$message.success('Görsel yüklendi')
      } catch (err) {
        const msg = err?.response?.data?.message || 'Yükleme başarısız'
        this.$message.error(msg)
      } finally {
        this.uploading = false
      }
    },
    handleRemove() {
      this.$emit('input', '')
    },
  },
}
</script>

<style scoped>
.image-upload-wrapper {
  width: 100%;
}

.image-uploader >>> .el-upload {
  width: 100%;
}

.image-uploader >>> .el-upload-dragger {
  width: 100%;
  height: auto;
  min-height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
}

.preview-container {
  position: relative;
  width: 100%;
  min-height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px;
}

.preview-image {
  max-width: 100%;
  max-height: 160px;
  object-fit: contain;
  border-radius: 4px;
}

.preview-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 14px;
  opacity: 0;
  transition: opacity 0.2s;
  border-radius: 6px;
  cursor: pointer;
}

.preview-overlay i {
  font-size: 24px;
  margin-bottom: 4px;
}

.preview-container:hover .preview-overlay {
  opacity: 1;
}

.upload-placeholder {
  padding: 20px;
  text-align: center;
}

.upload-placeholder i {
  font-size: 32px;
  color: #c0c4cc;
}

.upload-text {
  font-size: 14px;
  color: #606266;
  margin-top: 8px;
}

.upload-hint {
  font-size: 12px;
  color: #909399;
  margin-top: 4px;
}

.upload-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}

.url-display {
  flex: 1;
}
</style>
