<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px">
      <span>Sayfalar</span>
      <el-button type="primary" size="small" icon="el-icon-plus" @click="$router.push(`/sites/${siteId}/pages/create`)">
        Sayfa Ekle
      </el-button>
    </div>

    <el-table :data="pages" v-loading="loading" stripe>
      <el-table-column prop="title" label="Başlık" />
      <el-table-column prop="slug" label="Slug" />
      <el-table-column label="Yayında" width="100">
        <template slot-scope="{ row }">
          <el-tag :type="row.is_published ? 'success' : 'info'" size="mini">
            {{ row.is_published ? 'Evet' : 'Hayır' }}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="sort_order" label="Sıralama" width="80" />
      <el-table-column label="İşlemler" width="150" align="right">
        <template slot-scope="{ row }">
          <el-button type="text" @click="$router.push(`/sites/${siteId}/pages/${row.id}`)">Düzenle</el-button>
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
      @current-change="fetchPages"
    />
  </div>
</template>

<script>
import { getPages, deletePage } from '../../api/pages'

export default {
  name: 'PageList',
  props: { siteId: { type: Number, required: true } },
  data() {
    return {
      pages: [],
      loading: false,
      currentPage: 1,
      perPage: 15,
      total: 0,
    }
  },
  created() {
    this.fetchPages()
  },
  methods: {
    async fetchPages(page) {
      this.loading = true
      try {
        const { data } = await getPages(this.siteId, { page: page || this.currentPage, per_page: this.perPage })
        this.pages = data.data || []
        this.total = data.total || 0
      } catch {
        this.$message.error('Sayfalar yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    async handleDelete(pg) {
      try {
        await this.$confirm(`"${pg.title}" sayfasını silmek istediğinize emin misiniz?`, 'Uyarı', { type: 'warning' })
        await deletePage(this.siteId, pg.id)
        this.$message.success('Sayfa silindi')
        this.fetchPages()
      } catch (err) {
        if (err !== 'cancel') this.$message.error('Silme başarısız')
      }
    },
  },
}
</script>
