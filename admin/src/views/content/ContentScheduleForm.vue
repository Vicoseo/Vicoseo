<template>
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px">
      <h3 style="margin: 0">Icerik Planlari</h3>
      <el-button size="small" type="primary" @click="openCreateDialog">Yeni Plan</el-button>
    </div>

    <el-table :data="schedules" v-loading="loading" size="small" border>
      <el-table-column label="Siklil" prop="frequency" width="100">
        <template slot-scope="{ row }">
          {{ row.frequency === 'daily' ? 'Gunluk' : 'Ozel' }}
        </template>
      </el-table-column>
      <el-table-column label="Saat" prop="run_at" width="80" />
      <el-table-column label="Aralik (saat)" prop="interval_hours" width="110">
        <template slot-scope="{ row }">
          {{ row.interval_hours || '-' }}
        </template>
      </el-table-column>
      <el-table-column label="Konular">
        <template slot-scope="{ row }">
          <el-tag v-for="t in (row.topics || [])" :key="t" size="mini" style="margin-right: 4px">{{ t }}</el-tag>
          <span v-if="!row.topics || !row.topics.length" style="color: #909399">Tum konular</span>
        </template>
      </el-table-column>
      <el-table-column label="Aktif" width="70" align="center">
        <template slot-scope="{ row }">
          <el-switch v-model="row.is_active" @change="toggleActive(row)" size="mini" />
        </template>
      </el-table-column>
      <el-table-column label="Son Calisma" width="150">
        <template slot-scope="{ row }">
          {{ row.last_run_at || '-' }}
        </template>
      </el-table-column>
      <el-table-column label="Islemler" width="140" align="center">
        <template slot-scope="{ row }">
          <el-button size="mini" @click="openEditDialog(row)">Duzenle</el-button>
          <el-button size="mini" type="danger" @click="handleDelete(row)">Sil</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog :title="dialogForm.id ? 'Plan Duzenle' : 'Yeni Plan'" :visible.sync="dialogVisible" width="500px">
      <el-form :model="dialogForm" label-width="130px" size="small">
        <el-form-item label="Siklil">
          <el-select v-model="dialogForm.frequency">
            <el-option label="Gunluk" value="daily" />
            <el-option label="Ozel" value="custom" />
          </el-select>
        </el-form-item>
        <el-form-item label="Calisma Saati">
          <el-time-select
            v-model="dialogForm.run_at"
            :picker-options="{ start: '00:00', step: '01:00', end: '23:00' }"
            placeholder="Saat secin"
          />
        </el-form-item>
        <el-form-item label="Aralik (saat)" v-if="dialogForm.frequency === 'custom'">
          <el-input-number v-model="dialogForm.interval_hours" :min="1" :max="168" />
        </el-form-item>
        <el-form-item label="Konular">
          <el-select v-model="dialogForm.topics" multiple placeholder="Bos = Tum konular">
            <el-option label="Erisim" value="erisim" />
            <el-option label="Bonus" value="bonus" />
            <el-option label="Mobil" value="mobil" />
            <el-option label="Odeme" value="odeme" />
            <el-option label="Oyun" value="oyun" />
            <el-option label="Guvenlik" value="guvenlik" />
            <el-option label="Genel" value="genel" />
          </el-select>
        </el-form-item>
        <el-form-item label="Aktif">
          <el-switch v-model="dialogForm.is_active" />
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button @click="dialogVisible = false">Iptal</el-button>
        <el-button type="primary" :loading="saving" @click="handleSave">Kaydet</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import {
  getContentSchedules,
  createContentSchedule,
  updateContentSchedule,
  deleteContentSchedule,
} from '../../api/contentSchedules'

export default {
  name: 'ContentScheduleForm',
  props: {
    siteId: { type: [Number, String], required: true },
  },
  data() {
    return {
      schedules: [],
      loading: false,
      saving: false,
      dialogVisible: false,
      dialogForm: this.emptyForm(),
    }
  },
  created() {
    this.fetchSchedules()
  },
  methods: {
    emptyForm() {
      return {
        id: null,
        frequency: 'daily',
        run_at: '06:00',
        interval_hours: null,
        topics: [],
        is_active: true,
      }
    },
    async fetchSchedules() {
      this.loading = true
      try {
        const { data } = await getContentSchedules(this.siteId)
        this.schedules = data.data
      } catch {
        this.$message.error('Planlar yuklenemedi')
      } finally {
        this.loading = false
      }
    },
    openCreateDialog() {
      this.dialogForm = this.emptyForm()
      this.dialogVisible = true
    },
    openEditDialog(row) {
      this.dialogForm = {
        id: row.id,
        frequency: row.frequency,
        run_at: row.run_at,
        interval_hours: row.interval_hours,
        topics: row.topics || [],
        is_active: row.is_active,
      }
      this.dialogVisible = true
    },
    async handleSave() {
      this.saving = true
      try {
        const payload = {
          frequency: this.dialogForm.frequency,
          run_at: this.dialogForm.run_at,
          interval_hours: this.dialogForm.frequency === 'custom' ? this.dialogForm.interval_hours : null,
          topics: this.dialogForm.topics.length ? this.dialogForm.topics : null,
          is_active: this.dialogForm.is_active,
        }
        if (this.dialogForm.id) {
          await updateContentSchedule(this.siteId, this.dialogForm.id, payload)
          this.$message.success('Plan guncellendi')
        } else {
          await createContentSchedule(this.siteId, payload)
          this.$message.success('Plan olusturuldu')
        }
        this.dialogVisible = false
        this.fetchSchedules()
      } catch (err) {
        const msg = err.response?.data?.message || 'Plan kaydedilemedi'
        this.$message.error(msg)
      } finally {
        this.saving = false
      }
    },
    async toggleActive(row) {
      try {
        await updateContentSchedule(this.siteId, row.id, { is_active: row.is_active })
      } catch {
        row.is_active = !row.is_active
        this.$message.error('Durum guncellenemedi')
      }
    },
    async handleDelete(row) {
      try {
        await this.$confirm('Bu plan silinecek. Emin misiniz?', 'Uyari', {
          confirmButtonText: 'Sil',
          cancelButtonText: 'Iptal',
          type: 'warning',
        })
      } catch {
        return
      }
      try {
        await deleteContentSchedule(this.siteId, row.id)
        this.$message.success('Plan silindi')
        this.fetchSchedules()
      } catch {
        this.$message.error('Plan silinemedi')
      }
    },
  },
}
</script>
