<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px">
      <span>Yazılar</span>
      <el-button type="primary" size="small" icon="el-icon-plus" @click="$router.push(`/sites/${siteId}/posts/create`)">
        Yazı Ekle
      </el-button>
    </div>

    <el-table :data="posts" v-loading="loading" stripe>
      <el-table-column prop="title" label="Başlık" />
      <el-table-column prop="slug" label="Slug" />
      <el-table-column label="Yayında" width="100">
        <template slot-scope="{ row }">
          <el-tag :type="row.is_published ? 'success' : 'info'" size="mini">
            {{ row.is_published ? 'Evet' : 'Hayır' }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="published_at" label="Yayın Tarihi" width="160">
        <template slot-scope="{ row }">
          {{ row.published_at ? formatDate(row.published_at) : '-' }}
        </template>
      </el-table-column>
      <el-table-column label="İşlemler" width="150" align="right">
        <template slot-scope="{ row }">
          <el-button type="text" @click="$router.push(`/sites/${siteId}/posts/${row.id}`)">Düzenle</el-button>
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
      @current-change="fetchPosts"
    />
  </div>
</template>

<script>
import { getPosts, deletePost } from '../../api/posts'

export default {
  name: 'PostList',
  props: { siteId: { type: Number, required: true } },
  data() {
    return {
      posts: [],
      loading: false,
      currentPage: 1,
      perPage: 15,
      total: 0,
    }
  },
  created() {
    this.fetchPosts()
  },
  methods: {
    async fetchPosts(page) {
      this.loading = true
      try {
        const { data } = await getPosts(this.siteId, { page: page || this.currentPage, per_page: this.perPage })
        this.posts = data.data || []
        this.total = data.total || 0
      } catch {
        this.$message.error('Yazılar yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    async handleDelete(post) {
      try {
        await this.$confirm(`"${post.title}" yazısını silmek istediğinize emin misiniz?`, 'Uyarı', { type: 'warning' })
        await deletePost(this.siteId, post.id)
        this.$message.success('Yazı silindi')
        this.fetchPosts()
      } catch (err) {
        if (err !== 'cancel') this.$message.error('Silme başarısız')
      }
    },
    formatDate(d) {
      if (!d) return ''
      return new Date(d).toLocaleDateString('tr-TR', { year: 'numeric', month: 'short', day: 'numeric' })
    },
  },
}
</script>
