<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
      <h2 style="margin: 0">Siteler</h2>
      <el-button type="primary" icon="el-icon-plus" @click="$router.push('/sites/create')">Yeni Site</el-button>
    </div>

    <el-card>
      <div style="margin-bottom: 16px">
        <el-input
          v-model="search"
          placeholder="Alan adı veya isim ile ara..."
          prefix-icon="el-icon-search"
          clearable
          style="width: 300px"
          @input="handleSearch"
        />
      </div>

      <el-table :data="filteredSites" v-loading="loading" stripe>
        <el-table-column prop="domain" label="Alan Adı" sortable />
        <el-table-column prop="name" label="İsim" sortable />
        <el-table-column label="Durum" width="100" sortable sort-by="is_active">
          <template slot-scope="{ row }">
            <el-tag :type="row.is_active ? 'success' : 'danger'" size="small">
              {{ row.is_active ? 'Aktif' : 'Pasif' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="created_at" label="Oluşturulma" width="160">
          <template slot-scope="{ row }">
            {{ formatDate(row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column label="İşlemler" width="220" align="right">
          <template slot-scope="{ row }">
            <el-button type="primary" size="mini" @click="$router.push(`/sites/${row.id}`)">Yönet</el-button>
            <el-button size="mini" @click="$router.push(`/sites/${row.id}/edit`)">Düzenle</el-button>
            <el-button type="danger" size="mini" @click="handleDelete(row)">Sil</el-button>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        v-if="total > perPage"
        style="margin-top: 16px; text-align: right"
        layout="prev, pager, next, total"
        :total="total"
        :page-size="perPage"
        :current-page.sync="currentPage"
        @current-change="fetchSites"
      />
    </el-card>
  </div>
</template>

<script>
import { getSites, deleteSite } from '../../api/sites'

export default {
  name: 'SiteList',
  data() {
    return {
      sites: [],
      loading: false,
      search: '',
      currentPage: 1,
      perPage: 15,
      total: 0,
    }
  },
  computed: {
    filteredSites() {
      if (!this.search) return this.sites
      const q = this.search.toLowerCase()
      return this.sites.filter(
        (s) => s.domain.toLowerCase().includes(q) || s.name.toLowerCase().includes(q)
      )
    },
  },
  created() {
    this.fetchSites()
  },
  methods: {
    async fetchSites(page) {
      this.loading = true
      try {
        const { data } = await getSites({ page: page || this.currentPage, per_page: this.perPage })
        this.sites = data.data || []
        this.total = data.total || 0
        this.currentPage = data.current_page || 1
      } catch {
        this.$message.error('Siteler yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    handleSearch() {
      // Client-side filtering for now; for large datasets, add server-side search
    },
    async handleDelete(site) {
      try {
        await this.$confirm(`"${site.domain}" sitesini silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`, 'Uyarı', {
          type: 'warning',
        })
        await deleteSite(site.id)
        this.$message.success('Site silindi')
        this.fetchSites()
      } catch (err) {
        if (err !== 'cancel') {
          this.$message.error('Silme başarısız')
        }
      }
    },
    formatDate(d) {
      if (!d) return ''
      return new Date(d).toLocaleDateString('tr-TR', { year: 'numeric', month: 'short', day: 'numeric' })
    },
  },
}
</script>
