<template>
  <div>
    <div style="margin-bottom: 20px">
      <el-button icon="el-icon-back" @click="$router.push(`/sites/${siteId}`)">Siteye Dön</el-button>
    </div>

    <el-card>
      <div slot="header">{{ isEdit ? 'Sayfa Düzenle' : 'Sayfa Oluştur' }}</div>

      <el-form
        ref="form"
        :model="form"
        :rules="rules"
        label-width="150px"
        v-loading="loading"
        @submit.native.prevent="handleSubmit"
      >
        <el-form-item label="Başlık" prop="title">
          <el-input v-model="form.title" @blur="autoSlug" />
        </el-form-item>
        <el-form-item label="Slug" prop="slug">
          <el-input v-model="form.slug" />
        </el-form-item>
        <el-form-item label="İçerik" prop="content">
          <el-input v-model="form.content" type="textarea" :rows="12" />
        </el-form-item>
        <el-form-item label="Meta Başlık">
          <el-input v-model="form.meta_title" />
        </el-form-item>
        <el-form-item label="Meta Açıklama">
          <el-input v-model="form.meta_description" type="textarea" :rows="2" />
        </el-form-item>
        <el-form-item label="Meta Anahtar Kelimeler">
          <el-input v-model="form.meta_keywords" />
        </el-form-item>
        <el-form-item label="Sıralama">
          <el-input-number v-model="form.sort_order" :min="0" />
        </el-form-item>
        <el-form-item label="Yayında">
          <el-switch v-model="form.is_published" />
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
import { getPage, createPage, updatePage } from '../../api/pages'

export default {
  name: 'PageForm',
  data() {
    return {
      form: {
        title: '',
        slug: '',
        content: '',
        meta_title: '',
        meta_description: '',
        meta_keywords: '',
        sort_order: 0,
        is_published: false,
      },
      rules: {
        title: [{ required: true, message: 'Başlık zorunludur', trigger: 'blur' }],
        slug: [{ required: true, message: 'Slug zorunludur', trigger: 'blur' }],
        content: [{ required: true, message: 'İçerik zorunludur', trigger: 'blur' }],
      },
      loading: false,
      saving: false,
    }
  },
  computed: {
    siteId() {
      return this.$route.params.siteId
    },
    pageId() {
      return this.$route.params.pageId
    },
    isEdit() {
      return !!this.pageId
    },
  },
  created() {
    if (this.isEdit) this.fetchPage()
  },
  methods: {
    async fetchPage() {
      this.loading = true
      try {
        const { data } = await getPage(this.siteId, this.pageId)
        const page = data.data
        Object.keys(this.form).forEach((key) => {
          if (page[key] !== undefined && page[key] !== null) this.form[key] = page[key]
        })
      } catch {
        this.$message.error('Sayfa yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    autoSlug() {
      if (!this.isEdit && !this.form.slug && this.form.title) {
        this.form.slug = this.form.title
          .toLowerCase()
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/^-|-$/g, '')
      }
    },
    handleSubmit() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.saving = true
        try {
          if (this.isEdit) {
            await updatePage(this.siteId, this.pageId, this.form)
            this.$message.success('Sayfa güncellendi')
          } else {
            await createPage(this.siteId, this.form)
            this.$message.success('Sayfa oluşturuldu')
          }
          this.$router.push(`/sites/${this.siteId}`)
        } catch (err) {
          const errors = err.response?.data?.errors
          if (errors) {
            const first = Object.values(errors)[0]
            this.$message.error(Array.isArray(first) ? first[0] : first)
          } else {
            this.$message.error('Sayfa kaydedilemedi')
          }
        } finally {
          this.saving = false
        }
      })
    },
  },
}
</script>
