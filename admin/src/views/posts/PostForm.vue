<template>
  <div>
    <div style="margin-bottom: 20px">
      <el-button icon="el-icon-back" @click="$router.push(`/sites/${siteId}`)">Siteye Dön</el-button>
    </div>

    <el-card>
      <div slot="header">{{ isEdit ? 'Yazı Düzenle' : 'Yazı Oluştur' }}</div>

      <el-form
        ref="form"
        :model="form"
        :rules="rules"
        label-width="160px"
        v-loading="loading"
        @submit.native.prevent="handleSubmit"
      >
        <el-form-item label="Başlık" prop="title">
          <el-input v-model="form.title" @blur="autoSlug" />
        </el-form-item>
        <el-form-item label="Slug" prop="slug">
          <el-input v-model="form.slug" />
        </el-form-item>
        <el-form-item label="Özet">
          <el-input v-model="form.excerpt" type="textarea" :rows="3" />
        </el-form-item>
        <el-form-item label="İçerik" prop="content">
          <el-input v-model="form.content" type="textarea" :rows="12" />
        </el-form-item>
        <el-form-item label="Öne Çıkan Görsel">
          <el-input v-model="form.featured_image" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Meta Başlık">
          <el-input v-model="form.meta_title" />
        </el-form-item>
        <el-form-item label="Meta Açıklama">
          <el-input v-model="form.meta_description" type="textarea" :rows="2" />
        </el-form-item>
        <el-form-item label="Yayın Tarihi">
          <el-date-picker v-model="form.published_at" type="datetime" placeholder="Tarih seçin" value-format="yyyy-MM-dd HH:mm:ss" />
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
import { getPost, createPost, updatePost } from '../../api/posts'

export default {
  name: 'PostForm',
  data() {
    return {
      form: {
        title: '',
        slug: '',
        excerpt: '',
        content: '',
        featured_image: '',
        meta_title: '',
        meta_description: '',
        published_at: null,
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
    postId() {
      return this.$route.params.postId
    },
    isEdit() {
      return !!this.postId
    },
  },
  created() {
    if (this.isEdit) this.fetchPost()
  },
  methods: {
    async fetchPost() {
      this.loading = true
      try {
        const { data } = await getPost(this.siteId, this.postId)
        const post = data.data
        Object.keys(this.form).forEach((key) => {
          if (post[key] !== undefined && post[key] !== null) this.form[key] = post[key]
        })
      } catch {
        this.$message.error('Yazı yüklenemedi')
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
            await updatePost(this.siteId, this.postId, this.form)
            this.$message.success('Yazı güncellendi')
          } else {
            await createPost(this.siteId, this.form)
            this.$message.success('Yazı oluşturuldu')
          }
          this.$router.push(`/sites/${this.siteId}`)
        } catch (err) {
          const errors = err.response?.data?.errors
          if (errors) {
            const first = Object.values(errors)[0]
            this.$message.error(Array.isArray(first) ? first[0] : first)
          } else {
            this.$message.error('Yazı kaydedilemedi')
          }
        } finally {
          this.saving = false
        }
      })
    },
  },
}
</script>
