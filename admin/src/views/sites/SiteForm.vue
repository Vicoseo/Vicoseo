<template>
  <div>
    <div style="margin-bottom: 20px">
      <el-button icon="el-icon-back" @click="$router.push('/sites')">Sitelere Dön</el-button>
    </div>

    <el-card>
      <div slot="header">
        <span>{{ isEdit ? 'Site Düzenle' : 'Site Oluştur' }}</span>
      </div>

      <el-form
        ref="form"
        :model="form"
        :rules="rules"
        label-width="160px"
        v-loading="loading"
        @submit.native.prevent="handleSubmit"
      >
        <el-form-item label="Alan Adı" prop="domain">
          <el-input v-model="form.domain" placeholder="example.com" />
        </el-form-item>
        <el-form-item label="İsim" prop="name">
          <el-input v-model="form.name" placeholder="Site Adı" />
        </el-form-item>
        <el-form-item label="Logo URL" prop="logo_url">
          <el-input v-model="form.logo_url" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Favicon URL" prop="favicon_url">
          <el-input v-model="form.favicon_url" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Ana Renk">
          <el-color-picker v-model="form.primary_color" />
        </el-form-item>
        <el-form-item label="İkincil Renk">
          <el-color-picker v-model="form.secondary_color" />
        </el-form-item>
        <el-form-item label="Meta Başlık">
          <el-input v-model="form.meta_title" placeholder="SEO Başlık" />
        </el-form-item>
        <el-form-item label="Meta Açıklama">
          <el-input v-model="form.meta_description" type="textarea" :rows="3" placeholder="SEO Açıklama" />
        </el-form-item>

        <el-divider content-position="left">Site Ayarları</el-divider>

        <el-form-item label="Giriş URL" prop="entry_url">
          <el-input v-model="form.entry_url" placeholder="https://hedefsite.com">
            <template slot="prepend">🔗</template>
          </el-input>
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Ayarlanırsa sitenin en üstünde "Siteye Giriş Yap" butonu gösterilir
          </div>
        </el-form-item>
        <el-form-item label="Login URL" prop="login_url">
          <el-input v-model="form.login_url" placeholder="https://hedefsite.com/login">
            <template slot="prepend">🔑</template>
          </el-input>
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Sitenin en üstündeki "GİRİŞ YAP" butonunun yönlendirme adresi. Boşsa /go/login kullanılır.
          </div>
        </el-form-item>
        <el-form-item label="Sponsorları Göster">
          <el-switch v-model="form.show_sponsors" />
          <span style="color: #909399; font-size: 12px; margin-left: 8px">
            Kapatılırsa bu sitede sponsor bölümü gizlenir
          </span>
        </el-form-item>

        <el-divider />

        <el-form-item label="Aktif">
          <el-switch v-model="form.is_active" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="saving" native-type="submit">
            {{ isEdit ? 'Güncelle' : 'Oluştur' }}
          </el-button>
          <el-button @click="$router.push('/sites')">İptal</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import { getSite, createSite, updateSite } from '../../api/sites'

export default {
  name: 'SiteForm',
  data() {
    return {
      form: {
        domain: '',
        name: '',
        logo_url: '',
        favicon_url: '',
        primary_color: '#000000',
        secondary_color: '#ffffff',
        meta_title: '',
        meta_description: '',
        entry_url: '',
        login_url: '',
        show_sponsors: true,
        is_active: true,
      },
      rules: {
        domain: [{ required: true, message: 'Alan adı zorunludur', trigger: 'blur' }],
        name: [{ required: true, message: 'İsim zorunludur', trigger: 'blur' }],
      },
      loading: false,
      saving: false,
    }
  },
  computed: {
    isEdit() {
      return !!this.$route.params.id
    },
    siteId() {
      return this.$route.params.id
    },
  },
  created() {
    if (this.isEdit) this.fetchSite()
  },
  methods: {
    async fetchSite() {
      this.loading = true
      try {
        const { data } = await getSite(this.siteId)
        const site = data.data
        Object.keys(this.form).forEach((key) => {
          if (site[key] !== undefined) this.form[key] = site[key]
        })
      } catch {
        this.$message.error('Site yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    handleSubmit() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.saving = true
        try {
          if (this.isEdit) {
            await updateSite(this.siteId, this.form)
            this.$message.success('Site güncellendi')
          } else {
            await createSite(this.form)
            this.$message.success('Site oluşturuldu')
          }
          this.$router.push('/sites')
        } catch (err) {
          const errors = err.response?.data?.errors
          if (errors) {
            const first = Object.values(errors)[0]
            this.$message.error(Array.isArray(first) ? first[0] : first)
          } else {
            this.$message.error('Site kaydedilemedi')
          }
        } finally {
          this.saving = false
        }
      })
    },
  },
}
</script>
