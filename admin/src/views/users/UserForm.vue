<template>
  <div>
    <div style="margin-bottom: 20px">
      <el-button icon="el-icon-back" @click="$router.push('/users')">Geri</el-button>
    </div>

    <el-card v-loading="loading">
      <div slot="header">
        <span>{{ isEdit ? 'Admin Duzenle' : 'Yeni Admin' }}</span>
      </div>

      <el-form ref="form" :model="form" :rules="rules" label-width="160px" @submit.native.prevent="handleSubmit">
        <el-form-item label="Ad" prop="name">
          <el-input v-model="form.name" />
        </el-form-item>
        <el-form-item label="E-posta" prop="email">
          <el-input v-model="form.email" />
        </el-form-item>
        <el-form-item v-if="!isEdit" label="Sifre" prop="password">
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

        <el-divider content-position="left">Site Erisimi</el-divider>
        <el-form-item label="Erisim Verilecek Siteler" v-if="form.role === 'admin'">
          <el-select v-model="form.site_ids" multiple filterable placeholder="Siteleri secin" style="width: 100%">
            <el-option v-for="site in allSites" :key="site.id" :label="site.name + ' (' + site.domain + ')'" :value="site.id" />
          </el-select>
          <div style="color: #909399; font-size: 12px; margin-top: 4px">
            Admin sadece secilen siteleri yonetebilir. Master tum sitelere erisir.
          </div>
        </el-form-item>

        <el-divider content-position="left">Yetkiler</el-divider>
        <el-form-item label="Yetkiler" v-if="form.role === 'admin'">
          <el-checkbox-group v-model="form.permissions">
            <el-checkbox label="sponsor_manage">Sponsor Yonetimi</el-checkbox>
            <el-checkbox label="redirect_manage">Redirect Yonetimi</el-checkbox>
            <el-checkbox label="content_generate">Icerik Uretimi</el-checkbox>
            <el-checkbox label="analytics_view">Analytics Goruntuleme</el-checkbox>
            <el-checkbox label="site_manage">Site Yonetimi</el-checkbox>
            <el-checkbox label="user_manage">Kullanici Yonetimi</el-checkbox>
          </el-checkbox-group>
        </el-form-item>

        <el-divider content-position="left">IP Kisitlama</el-divider>
        <el-form-item label="IP Kisitlama">
          <el-switch v-model="form.ip_restriction_enabled" />
        </el-form-item>
        <el-form-item label="Izin Verilen IP'ler" v-if="form.ip_restriction_enabled">
          <el-select v-model="form.allowed_ips" multiple filterable allow-create default-first-option
            placeholder="IP adresi ekleyin" style="width: 100%">
          </el-select>
        </el-form-item>

        <template v-if="isEdit">
          <el-divider content-position="left">Guvenlik</el-divider>
          <el-form-item label="2FA Durumu">
            <span v-if="form.two_factor_enabled" style="color: #67c23a">Aktif</span>
            <span v-else style="color: #e6a23c">Pasif</span>
            <el-button v-if="form.two_factor_enabled" type="warning" size="small" style="margin-left: 12px" @click="handleReset2FA">
              2FA Sifirla
            </el-button>
          </el-form-item>
          <el-form-item label="Sifre Degistir">
            <el-input v-model="newPassword" type="password" show-password placeholder="Yeni sifre" style="width: 300px" />
            <el-button type="warning" size="small" style="margin-left: 8px" :loading="passwordSaving" @click="handleResetPassword">
              Guncelle
            </el-button>
          </el-form-item>
        </template>

        <el-divider />
        <el-form-item>
          <el-button type="primary" :loading="saving" native-type="submit">
            {{ isEdit ? 'Guncelle' : 'Olustur' }}
          </el-button>
          <el-button @click="$router.push('/users')">Iptal</el-button>
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
          { type: 'email', message: 'Gecersiz e-posta', trigger: 'blur' },
        ],
        password: [{ required: true, message: 'Sifre zorunludur', trigger: 'blur', min: 8 }],
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
        this.$message.error('Kullanici yuklenemedi')
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
            this.$message.success('Admin guncellendi')
          } else {
            await createUser(payload)
            this.$message.success('Admin olusturuldu')
          }
          this.$router.push('/users')
        } catch (err) {
          const errors = err?.response?.data?.errors
          if (errors) {
            this.$message.error(Object.values(errors).flat()[0])
          } else {
            this.$message.error(err?.response?.data?.message || 'Islem basarisiz')
          }
        } finally {
          this.saving = false
        }
      })
    },
    async handleResetPassword() {
      if (!this.newPassword || this.newPassword.length < 8) {
        return this.$message.warning('Sifre en az 8 karakter olmalidir')
      }
      this.passwordSaving = true
      try {
        await resetUserPassword(this.userId, {
          password: this.newPassword,
          password_confirmation: this.newPassword,
        })
        this.$message.success('Sifre guncellendi')
        this.newPassword = ''
      } catch (err) {
        this.$message.error(err?.response?.data?.message || 'Sifre guncellenemedi')
      } finally {
        this.passwordSaving = false
      }
    },
    async handleReset2FA() {
      try {
        await this.$confirm('Bu kullanicinin 2FA ayarini sifirlamak istediginize emin misiniz?', 'Uyari', {
          type: 'warning',
        })
        await resetUser2FA(this.userId)
        this.form.two_factor_enabled = false
        this.$message.success('2FA sifirlandi')
      } catch (err) {
        if (err !== 'cancel') this.$message.error('Islem basarisiz')
      }
    },
  },
}
</script>
