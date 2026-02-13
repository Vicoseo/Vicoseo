<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px">
      <span>Sponsorlar</span>
      <el-button type="primary" size="small" icon="el-icon-plus" @click="openForm()">Sponsor Ekle</el-button>
    </div>

    <el-table :data="offers" v-loading="loading" stripe>
      <el-table-column prop="logo_url" label="Logo" width="80">
        <template slot-scope="{ row }">
          <img v-if="row.logo_url" :src="row.logo_url" style="width: 40px; height: 40px; object-fit: contain" />
        </template>
      </el-table-column>
      <el-table-column prop="slug" label="Slug" width="120" />
      <el-table-column prop="bonus_text" label="Bonus Yazısı" />
      <el-table-column prop="cta_text" label="CTA" width="150" />
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

    <el-pagination
      v-if="total > perPage"
      style="margin-top: 16px; text-align: right"
      layout="prev, pager, next"
      :total="total"
      :page-size="perPage"
      :current-page.sync="currentPage"
      @current-change="fetchOffers"
    />

    <el-dialog :title="editingOffer ? 'Sponsor Düzenle' : 'Yeni Sponsor'" :visible.sync="dialogVisible" width="500px">
      <el-form ref="form" :model="form" :rules="rules" label-width="110px">
        <el-form-item label="Slug" prop="slug">
          <el-input v-model="form.slug" placeholder="sponsor-adi" />
        </el-form-item>
        <el-form-item label="Logo URL" prop="logo_url">
          <el-input v-model="form.logo_url" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Bonus Yazısı" prop="bonus_text">
          <el-input v-model="form.bonus_text" />
        </el-form-item>
        <el-form-item label="Buton Yazısı" prop="cta_text">
          <el-input v-model="form.cta_text" />
        </el-form-item>
        <el-form-item label="Hedef URL" prop="target_url">
          <el-input v-model="form.target_url" placeholder="https://..." />
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
import { getSiteOffers, createSiteOffer, updateSiteOffer, deleteSiteOffer } from '../../api/topOffers'

export default {
  name: 'OfferList',
  props: { siteId: { type: Number, required: true } },
  data() {
    return {
      offers: [],
      loading: false,
      saving: false,
      currentPage: 1,
      perPage: 15,
      total: 0,
      dialogVisible: false,
      editingOffer: null,
      form: this.emptyForm(),
      rules: {
        logo_url: [{ required: true, message: 'Zorunlu', trigger: 'blur' }],
        bonus_text: [{ required: true, message: 'Zorunlu', trigger: 'blur' }],
        cta_text: [{ required: true, message: 'Zorunlu', trigger: 'blur' }],
        target_url: [{ required: true, message: 'Zorunlu', trigger: 'blur' }],
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
        const { data } = await getSiteOffers(this.siteId, { page: page || this.currentPage, per_page: this.perPage })
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
            await updateSiteOffer(this.siteId, this.editingOffer.id, this.form)
            this.$message.success('Sponsor güncellendi')
          } else {
            await createSiteOffer(this.siteId, this.form)
            this.$message.success('Sponsor oluşturuldu')
          }
          this.dialogVisible = false
          this.fetchOffers()
        } catch {
          this.$message.error('Sponsor kaydedilemedi')
        } finally {
          this.saving = false
        }
      })
    },
    async handleDelete(offer) {
      try {
        await this.$confirm('Bu sponsoru silmek istediğinize emin misiniz?', 'Uyarı', { type: 'warning' })
        await deleteSiteOffer(this.siteId, offer.id)
        this.$message.success('Sponsor silindi')
        this.fetchOffers()
      } catch (err) {
        if (err !== 'cancel') this.$message.error('Silme başarısız')
      }
    },
  },
}
</script>
