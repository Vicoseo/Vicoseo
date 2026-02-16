<template>
  <div class="login-wrapper">
    <el-card class="login-card">
      <h2 style="text-align: center; margin-bottom: 30px">CMS Admin Girisi</h2>

      <!-- Step 1: Email + Password -->
      <el-form v-if="!requires2FA" ref="form" :model="form" :rules="rules" label-position="top" @submit.native.prevent="handleLogin">
        <el-form-item label="E-posta" prop="email">
          <el-input v-model="form.email" prefix-icon="el-icon-message" placeholder="admin@cms.local" />
        </el-form-item>
        <el-form-item label="Sifre" prop="password">
          <el-input v-model="form.password" type="password" prefix-icon="el-icon-lock" placeholder="Sifre" show-password />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="loading" native-type="submit" style="width: 100%">Giris Yap</el-button>
        </el-form-item>
      </el-form>

      <!-- Step 2: 2FA Code -->
      <el-form v-else ref="tfaForm" :model="tfaForm" label-position="top" @submit.native.prevent="handleVerify2FA">
        <div style="text-align: center; margin-bottom: 20px">
          <i class="el-icon-lock" style="font-size: 40px; color: #409EFF"></i>
          <p style="margin-top: 12px; color: #606266">Authenticator uygulamanizdan 6 haneli kodu girin</p>
        </div>
        <el-form-item>
          <el-input
            v-model="tfaForm.code"
            placeholder="000000"
            maxlength="6"
            style="font-size: 24px; text-align: center; letter-spacing: 8px"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="verifying" native-type="submit" style="width: 100%">Dogrula</el-button>
        </el-form-item>
        <el-button type="text" @click="requires2FA = false; partialToken = ''">Geri Don</el-button>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import { login } from '../api/auth'
import client from '../api/client'

export default {
  name: 'Login',
  data() {
    return {
      form: { email: '', password: '' },
      rules: {
        email: [
          { required: true, message: 'E-posta zorunludur', trigger: 'blur' },
          { type: 'email', message: 'Gecersiz e-posta formati', trigger: 'blur' },
        ],
        password: [{ required: true, message: 'Sifre zorunludur', trigger: 'blur' }],
      },
      loading: false,
      requires2FA: false,
      partialToken: '',
      tfaForm: { code: '' },
      verifying: false,
    }
  },
  methods: {
    handleLogin() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.loading = true
        try {
          const { data } = await login(this.form)

          if (data.data.requires_2fa) {
            this.requires2FA = true
            this.partialToken = data.data.partial_token
            return
          }

          this.$store.commit('auth/SET_TOKEN', data.data.token)
          this.$store.commit('auth/SET_USER', data.data.user)
          this.$message.success('Giris basarili')
          this.$router.push('/')
        } catch (err) {
          const msg = err.response?.data?.message || err.response?.data?.errors?.email?.[0] || 'Giris basarisiz'
          this.$message.error(msg)
        } finally {
          this.loading = false
        }
      })
    },
    async handleVerify2FA() {
      if (!this.tfaForm.code || this.tfaForm.code.length !== 6) {
        return this.$message.warning('6 haneli kodu girin')
      }
      this.verifying = true
      try {
        const { data } = await client.post('/admin/verify-2fa', {
          partial_token: this.partialToken,
          code: this.tfaForm.code,
        })

        this.$store.commit('auth/SET_TOKEN', data.data.token)
        this.$store.commit('auth/SET_USER', data.data.user)
        this.$message.success('Giris basarili')
        this.$router.push('/')
      } catch (err) {
        this.$message.error(err?.response?.data?.message || 'Dogrulama basarisiz')
      } finally {
        this.verifying = false
      }
    },
  },
}
</script>

<style scoped>
.login-wrapper {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.login-card {
  width: 400px;
}
</style>
