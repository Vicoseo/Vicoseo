<template>
  <div>
    <div style="margin-bottom: 20px">
      <el-button icon="el-icon-back" @click="$router.push(`/sites/${siteId}`)">Siteye Dön</el-button>
    </div>

    <el-card>
      <div slot="header">{{ isEdit ? 'Yönlendirme Düzenle' : 'Yönlendirme Oluştur' }}</div>

      <el-form ref="form" :model="form" :rules="rules" label-width="120px" v-loading="loading" @submit.native.prevent="handleSubmit">
        <el-form-item label="Slug" prop="slug">
          <el-input v-model="form.slug" placeholder="my-link" />
        </el-form-item>
        <el-form-item label="Hedef URL" prop="target_url">
          <el-input v-model="form.target_url" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Aktif">
          <el-switch v-model="form.is_active" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="saving" native-type="submit">
            {{ isEdit ? 'Güncelle' : 'Oluştur' }}
          </el-button>
          <el-button @click="$router.push(`/sites/${siteId}`)">İptal</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import { getRedirect, createRedirect, updateRedirect } from '../../api/redirects'

export default {
  name: 'RedirectForm',
  data() {
    return {
      form: { slug: '', target_url: '', is_active: true },
      rules: {
        slug: [{ required: true, message: 'Slug zorunludur', trigger: 'blur' }],
        target_url: [{ required: true, message: 'Hedef URL zorunludur', trigger: 'blur' }],
      },
      loading: false,
      saving: false,
    }
  },
  computed: {
    siteId() {
      return this.$route.params.siteId
    },
    redirectId() {
      return this.$route.params.redirectId
    },
    isEdit() {
      return !!this.redirectId
    },
  },
  created() {
    if (this.isEdit) this.fetchRedirect()
  },
  methods: {
    async fetchRedirect() {
      this.loading = true
      try {
        const { data } = await getRedirect(this.siteId, this.redirectId)
        const r = data.data
        this.form = { slug: r.slug, target_url: r.target_url, is_active: r.is_active }
      } catch {
        this.$message.error('Yönlendirme yüklenemedi')
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
            await updateRedirect(this.siteId, this.redirectId, this.form)
            this.$message.success('Yönlendirme güncellendi')
          } else {
            await createRedirect(this.siteId, this.form)
            this.$message.success('Yönlendirme oluşturuldu')
          }
          this.$router.push(`/sites/${this.siteId}`)
        } catch (err) {
          const errors = err.response?.data?.errors
          if (errors) {
            const first = Object.values(errors)[0]
            this.$message.error(Array.isArray(first) ? first[0] : first)
          } else {
            this.$message.error('Yönlendirme kaydedilemedi')
          }
        } finally {
          this.saving = false
        }
      })
    },
  },
}
</script>
