<template>
  <div>
    <div style="margin-bottom: 20px">
      <el-button icon="el-icon-back" @click="$router.push('/users')">Geri</el-button>
    </div>

    <el-card v-loading="loading">
      <div slot="header">
        <span>{{ isEdit ? 'Admin Düzenle' : 'Yeni Admin' }}</span>
      </div>

      <el-form ref="form" :model="form" :rules="rules" label-width="160px" @submit.native.prevent="handleSubmit">
        <el-form-item label="Ad" prop="name">
          <el-input v-model="form.name" />
        </el-form-item>
        <el-form-item label="E-posta" prop="email">
          <el-input v-model="form.email" />
        </el-form-item>
        <el-form-item v-if="!isEdit" label="Şifre" prop="password">
          <el-input v-model="form.password" type="password" show-password />
        </el-form-item>
        <el-form-item label="Rol" prop="role">
          <el-select v-model="form.role" style="width: 100%">
            <el-option label="Master" value="master" />
            <el-option label="Admin" value="admin" />
          </el-select>
        </el-form-item>
        <el-form-item label="Aktif">
          <el-switch v-model="form.is_active" />
        </el-form-item>

        <el-divider content-position="left">Site Erişimi</el-divider>
        <el-form-item label="Erişim Verilecek Siteler" v-if="form.role === 'admin'">
          <el-select v-model="form.site_ids" multiple filterable placeholder="Siteleri seçin" style="width: 100%">
            <el-option v-for="site in allSites" :key="site.id" :label="site.name + ' (' + site.domain + ')'" :value="site.id" />
          </el-select>
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Admin sadece seçilen siteleri yönetebilir. Master tüm sitelere erişir.
          </div>
        </el-form-item>

        <el-divider content-position="left">Yetkiler</el-divider>
        <el-form-item label="Yetkiler" v-if="form.role === 'admin'">
          <el-checkbox-group v-model="form.permissions">
            <el-checkbox label="sponsor_manage">Sponsor Yönetimi</el-checkbox>
            <el-checkbox label="redirect_manage">Redirect Yönetimi</el-checkbox>
            <el-checkbox label="content_generate">İçerik Üretimi</el-checkbox>
            <el-checkbox label="analytics_view">Analytics Görüntüleme</el-checkbox>
            <el-checkbox label="site_manage">Site Yönetimi</el-checkbox>
            <el-checkbox label="user_manage">Kullanıcı Yönetimi</el-checkbox>
          </el-checkbox-group>
        </el-form-item>

        <el-divider content-position="left">IP Kısıtlama</el-divider>
        <el-form-item label="IP Kısıtlama">
          <el-switch v-model="form.ip_restriction_enabled" />
        </el-form-item>
        <el-form-item label="İzin Verilen IP'ler" v-if="form.ip_restriction_enabled">
          <el-select v-model="form.allowed_ips" multiple filterable allow-create default-first-option
            placeholder="IP adresi ekleyin" style="width: 100%">
          </el-select>
        </el-form-item>

        <template v-if="isEdit">
          <el-divider content-position="left">Güvenlik</el-divider>
          <el-form-item label="2FA Durumu">
            <span v-if="form.two_factor_enabled" style="color: #67c23a">Aktif</span>
            <span v-else style="color: #e6a23c">Pasif</span>
            <el-button v-if="form.two_factor_enabled" type="warning" size="small" style="margin-left: 12px" @click="handleReset2FA">
              2FA Sıfırla
            </el-button>
          </el-form-item>
          <el-form-item label="Şifre Değiştir">
            <el-input v-model="newPassword" type="password" show-password placeholder="Yeni şifre" style="width: 300px" />
            <el-button type="warning" size="small" style="margin-left: 8px" :loading="passwordSaving" @click="handleResetPassword">
              Güncelle
            </el-button>
          </el-form-item>
        </template>

        <el-divider />
        <el-form-item>
          <el-button type="primary" :loading="saving" native-type="submit">
            {{ isEdit ? 'Güncelle' : 'Oluştur' }}
          </el-button>
          <el-button @click="$router.push('/users')">İptal</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import { getUser, createUser, updateUser, resetUserPassword, resetUser2FA } from '../../api/users'
import { getSites } from '../../api/sites'

export default {
  name: 'UserForm',
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        role: 'admin',
        is_active: true,
        site_ids: [],
        permissions: [],
        ip_restriction_enabled: false,
        allowed_ips: [],
        two_factor_enabled: false,
      },
      rules: {
        name: [{ required: true, message: 'Ad zorunludur', trigger: 'blur' }],
        email: [
          { required: true, message: 'E-posta zorunludur', trigger: 'blur' },
          { type: 'email', message: 'Geçersiz e-posta', trigger: 'blur' },
        ],
        password: [{ required: true, message: 'Şifre zorunludur', trigger: 'blur', min: 8 }],
        role: [{ required: true, message: 'Rol zorunludur', trigger: 'change' }],
      },
      allSites: [],
      loading: false,
      saving: false,
      newPassword: '',
      passwordSaving: false,
    }
  },
  computed: {
    isEdit() {
      return !!this.$route.params.id
    },
    userId() {
      return this.$route.params.id
    },
  },
  created() {
    this.fetchSites()
    if (this.isEdit) this.fetchUser()
  },
  methods: {
    async fetchSites() {
      try {
        const { data } = await getSites({ per_page: 200 })
        this.allSites = data.data || []
      } catch {}
    },
    async fetchUser() {
      this.loading = true
      try {
        const { data } = await getUser(this.userId)
        const user = data.data
        this.form = {
          name: user.name || '',
          email: user.email || '',
          password: '',
          role: user.role || 'admin',
          is_active: user.is_active !== false,
          site_ids: (user.sites || []).map(s => s.id),
          permissions: user.permissions || [],
          ip_restriction_enabled: user.ip_restriction_enabled || false,
          allowed_ips: user.allowed_ips || [],
          two_factor_enabled: user.two_factor_enabled || false,
        }
      } catch {
        this.$message.error('Kullanıcı yüklenemedi')
      } finally {
        this.loading = false
      }
    },
    handleSubmit() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.saving = true
        try {
          const payload = { ...this.form }
          if (this.isEdit) {
            delete payload.password
            delete payload.two_factor_enabled
            await updateUser(this.userId, payload)
            this.$message.success('Admin güncellendi')
          } else {
            await createUser(payload)
            this.$message.success('Admin oluşturuldu')
          }
          this.$router.push('/users')
        } catch (err) {
          const errors = err?.response?.data?.errors
          if (errors) {
            this.$message.error(Object.values(errors).flat()[0])
          } else {
            this.$message.error(err?.response?.data?.message || 'İşlem başarısız')
          }
        } finally {
          this.saving = false
        }
      })
    },
    async handleResetPassword() {
      if (!this.newPassword || this.newPassword.length < 8) {
        return this.$message.warning('Şifre en az 8 karakter olmalıdır')
      }
      this.passwordSaving = true
      try {
        await resetUserPassword(this.userId, {
          password: this.newPassword,
          password_confirmation: this.newPassword,
        })
        this.$message.success('Şifre güncellendi')
        this.newPassword = ''
      } catch (err) {
        this.$message.error(err?.response?.data?.message || 'Şifre güncellenemedi')
      } finally {
        this.passwordSaving = false
      }
    },
    async handleReset2FA() {
      try {
        await this.$confirm('Bu kullanıcının 2FA ayarını sıfırlamak istediğinize emin misiniz?', 'Uyarı', {
          type: 'warning',
        })
        await resetUser2FA(this.userId)
        this.form.two_factor_enabled = false
        this.$message.success('2FA sıfırlandı')
      } catch (err) {
        if (err !== 'cancel') this.$message.error('İşlem başarısız')
      }
    },
  },
}
</script>
