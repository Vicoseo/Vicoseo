<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px">
      <span>Footer Linkleri</span>
      <el-button type="primary" size="small" icon="el-icon-plus" @click="openForm()">Footer Link Ekle</el-button>
    </div>

    <el-table :data="footerLinks" v-loading="loading" stripe>
      <el-table-column prop="link_text" label="Link Yazısı" />
      <el-table-column prop="link_url" label="URL" show-overflow-tooltip />
      <el-table-column prop="sort_order" label="Sıralama" width="80" />
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

    <el-dialog :title="editingLink ? 'Footer Link Düzenle' : 'Yeni Footer Link'" :visible.sync="dialogVisible" width="500px">
      <el-form ref="form" :model="form" :rules="rules" label-width="110px">
        <el-form-item label="Link Yazısı" prop="link_text">
          <el-input v-model="form.link_text" placeholder="Hakkımızda" />
        </el-form-item>
        <el-form-item label="URL" prop="link_url">
          <el-input v-model="form.link_url" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Sıralama" prop="sort_order">
          <el-input-number v-model="form.sort_order" :min="0" :max="999" />
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
import { getFooterLinks, createFooterLink, updateFooterLink, deleteFooterLink } from '../../api/footerLinks'

export default {
  name: 'FooterLinkList',
  props: { siteId: { type: Number, required: true } },
  data() {
    return {
      footerLinks: [],
      loading: false,
      saving: false,
      dialogVisible: false,
      editingLink: null,
      form: this.emptyForm(),
      rules: {
        link_text: [{ required: true, message: 'Zorunlu', trigger: 'blur' }],
        link_url: [{ required: true, message: 'Zorunlu', trigger: 'blur' }],
      },
    }
  },
  created() {
    this.fetchFooterLinks()
  },
  methods: {
    emptyForm() {
      return { link_text: '', link_url: '', sort_order: 0, is_active: true }
    },
    async fetchFooterLinks() {
      this.loading = true
      try {
        const { data } = await getFooterLinks(this.siteId, { per_page: 50 })
        this.footerLinks = data.data || []
      } catch {
        this.$message.error('Footer linkleri yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    openForm(link) {
      this.editingLink = link || null
      this.form = link
        ? { link_text: link.link_text, link_url: link.link_url, sort_order: link.sort_order, is_active: link.is_active }
        : this.emptyForm()
      this.dialogVisible = true
      this.$nextTick(() => this.$refs.form?.clearValidate())
    },
    handleSave() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.saving = true
        try {
          if (this.editingLink) {
            await updateFooterLink(this.siteId, this.editingLink.id, this.form)
            this.$message.success('Footer link güncellendi')
          } else {
            await createFooterLink(this.siteId, this.form)
            this.$message.success('Footer link oluşturuldu')
          }
          this.dialogVisible = false
          this.fetchFooterLinks()
        } catch {
          this.$message.error('Footer link kaydedilemedi')
        } finally {
          this.saving = false
        }
      })
    },
    async handleDelete(link) {
      try {
        await this.$confirm(`"${link.link_text}" footer linkini silmek istediğinize emin misiniz?`, 'Uyarı', { type: 'warning' })
        await deleteFooterLink(this.siteId, link.id)
        this.$message.success('Footer link silindi')
        this.fetchFooterLinks()
      } catch (err) {
        if (err !== 'cancel') this.$message.error('Silme başarısız')
      }
    },
  },
}
</script>
