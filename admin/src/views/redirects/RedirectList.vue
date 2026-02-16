<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px">
      <span>Yönlendirmeler</span>
      <el-button type="primary" size="small" icon="el-icon-plus" @click="openForm()">Yönlendirme Ekle</el-button>
    </div>

    <el-table :data="redirects" v-loading="loading" stripe>
      <el-table-column prop="slug" label="Slug" />
      <el-table-column prop="target_url" label="Hedef URL" show-overflow-tooltip />
      <el-table-column label="Kod" width="70">
        <template slot-scope="{ row }">
          <el-tag size="mini" :type="row.status_code === 301 ? 'warning' : 'info'">{{ row.status_code || 302 }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="description" label="Açıklama" show-overflow-tooltip />
      <el-table-column prop="click_count" label="Tıklama" width="80" />
      <el-table-column label="Son Tıklama" width="140">
        <template slot-scope="{ row }">
          <span v-if="row.last_clicked_at">{{ formatDate(row.last_clicked_at) }}</span>
          <span v-else style="color: #c0c4cc">—</span>
        </template>
      </el-table-column>
      <el-table-column label="Aktif" width="80">
        <template slot-scope="{ row }">
          <el-tag :type="row.is_active ? 'success' : 'info'" size="mini">{{ row.is_active ? 'Evet' : 'Hayır' }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="İşlemler" width="150" align="right">
        <template slot-scope="{ row }">
          <el-button type="text" @click="openForm(row)">Düzenle</el-button>
          <el-button type="text" style="color: #f56c6c" @click="handleDelete(row)">Sil</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      v-if="total > perPage"
      style="margin-top: 16px; text-align: right"
      layout="prev, pager, next"
      :total="total"
      :page-size="perPage"
      :current-page.sync="currentPage"
      @current-change="fetchRedirects"
    />

    <el-dialog :title="editingRedirect ? 'Yönlendirme Düzenle' : 'Yeni Yönlendirme'" :visible.sync="dialogVisible" width="500px">
      <el-form ref="form" :model="form" :rules="rules" label-width="110px">
        <el-form-item label="Slug" prop="slug">
          <el-input v-model="form.slug" placeholder="my-link" />
        </el-form-item>
        <el-form-item label="Hedef URL" prop="target_url">
          <el-input v-model="form.target_url" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Durum Kodu">
          <el-select v-model="form.status_code" style="width: 100%">
            <el-option :value="301" label="301 - Kalıcı Yönlendirme" />
            <el-option :value="302" label="302 - Geçici Yönlendirme" />
            <el-option :value="307" label="307 - Geçici (Metod Koruma)" />
            <el-option :value="308" label="308 - Kalıcı (Metod Koruma)" />
          </el-select>
        </el-form-item>
        <el-form-item label="Açıklama">
          <el-input v-model="form.description" placeholder="Opsiyonel not..." />
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
import { getRedirects, createRedirect, updateRedirect, deleteRedirect } from '../../api/redirects'

export default {
  name: 'RedirectList',
  props: { siteId: { type: Number, required: true } },
  data() {
    return {
      redirects: [],
      loading: false,
      saving: false,
      currentPage: 1,
      perPage: 15,
      total: 0,
      dialogVisible: false,
      editingRedirect: null,
      form: this.emptyForm(),
      rules: {
        slug: [{ required: true, message: 'Zorunlu', trigger: 'blur' }],
        target_url: [{ required: true, message: 'Zorunlu', trigger: 'blur' }],
      },
    }
  },
  created() {
    this.fetchRedirects()
  },
  methods: {
    emptyForm() {
      return { slug: '', target_url: '', status_code: 302, description: '', is_active: true }
    },
    async fetchRedirects(page) {
      this.loading = true
      try {
        const { data } = await getRedirects(this.siteId, { page: page || this.currentPage, per_page: this.perPage })
        this.redirects = data.data || []
        this.total = data.total || 0
      } catch {
        this.$message.error('Yönlendirmeler yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    openForm(redirect) {
      this.editingRedirect = redirect || null
      this.form = redirect ? { slug: redirect.slug, target_url: redirect.target_url, status_code: redirect.status_code || 302, description: redirect.description || '', is_active: redirect.is_active } : this.emptyForm()
      this.dialogVisible = true
      this.$nextTick(() => this.$refs.form?.clearValidate())
    },
    handleSave() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.saving = true
        try {
          if (this.editingRedirect) {
            await updateRedirect(this.siteId, this.editingRedirect.id, this.form)
            this.$message.success('Yönlendirme güncellendi')
          } else {
            await createRedirect(this.siteId, this.form)
            this.$message.success('Yönlendirme oluşturuldu')
          }
          this.dialogVisible = false
          this.fetchRedirects()
        } catch {
          this.$message.error('Yönlendirme kaydedilemedi')
        } finally {
          this.saving = false
        }
      })
    },
    formatDate(d) {
      if (!d) return ''
      return new Date(d).toLocaleDateString('tr-TR', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
    },
    async handleDelete(redirect) {
      try {
        await this.$confirm(`"/${redirect.slug}" yönlendirmesini silmek istediğinize emin misiniz?`, 'Uyarı', { type: 'warning' })
        await deleteRedirect(this.siteId, redirect.id)
        this.$message.success('Yönlendirme silindi')
        this.fetchRedirects()
      } catch (err) {
        if (err !== 'cancel') this.$message.error('Silme başarısız')
      }
    },
  },
}
</script>
