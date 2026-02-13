<template>
  <div class="login-wrapper">
    <el-card class="login-card">
      <h2 style="text-align: center; margin-bottom: 30px">CMS Admin Girişi</h2>
      <el-form ref="form" :model="form" :rules="rules" label-position="top" @submit.native.prevent="handleLogin">
        <el-form-item label="E-posta" prop="email">
          <el-input v-model="form.email" prefix-icon="el-icon-message" placeholder="admin@cms.local" />
        </el-form-item>
        <el-form-item label="Şifre" prop="password">
          <el-input v-model="form.password" type="password" prefix-icon="el-icon-lock" placeholder="Şifre" show-password />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="loading" native-type="submit" style="width: 100%">Giriş Yap</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      form: { email: '', password: '' },
      rules: {
        email: [
          { required: true, message: 'E-posta zorunludur', trigger: 'blur' },
          { type: 'email', message: 'Geçersiz e-posta formatı', trigger: 'blur' },
        ],
        password: [{ required: true, message: 'Şifre zorunludur', trigger: 'blur' }],
      },
      loading: false,
    }
  },
  methods: {
    handleLogin() {
      this.$refs.form.validate(async (valid) => {
        if (!valid) return
        this.loading = true
        try {
          await this.$store.dispatch('auth/login', this.form)
          this.$message.success('Giriş başarılı')
          this.$router.push('/')
        } catch (err) {
          const msg = err.response?.data?.message || err.response?.data?.errors?.email?.[0] || 'Giriş başarısız'
          this.$message.error(msg)
        } finally {
          this.loading = false
        }
      })
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
