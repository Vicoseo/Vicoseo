<template>
  <div>
    <el-card>
      <div slot="header"><span>Iki Asamali Dogrulama (2FA)</span></div>

      <div v-if="!user.two_factor_enabled">
        <div v-if="!setupData">
          <p>Hesabinizi daha guvenli hale getirmek icin 2FA'yi etkinlestirin.</p>
          <el-button type="primary" :loading="enabling" @click="handleEnable">2FA Etkinlestir</el-button>
        </div>

        <div v-else>
          <p>Google Authenticator veya benzeri bir uygulamayla asagidaki QR kodu okutun:</p>
          <div style="margin: 20px 0; text-align: center">
            <img :src="qrImageUrl" alt="QR Code" style="max-width: 200px" />
          </div>
          <p style="font-size: 12px; color: #909399">
            Manuel giris icin secret: <code>{{ setupData.secret }}</code>
          </p>
          <el-form @submit.native.prevent="handleVerify" style="margin-top: 20px; max-width: 300px">
            <el-form-item label="Dogrulama Kodu">
              <el-input v-model="verifyCode" placeholder="6 haneli kod" maxlength="6" />
            </el-form-item>
            <el-button type="primary" :loading="verifying" @click="handleVerify">Dogrula ve Etkinlestir</el-button>
          </el-form>
        </div>
      </div>

      <div v-else>
        <p style="color: #67c23a; font-weight: 600">2FA aktif.</p>
        <el-form @submit.native.prevent="handleDisable" style="margin-top: 16px; max-width: 300px">
          <el-form-item label="Dogrulama Kodu">
            <el-input v-model="disableCode" placeholder="6 haneli kod" maxlength="6" />
          </el-form-item>
          <el-button type="danger" :loading="disabling" @click="handleDisable">2FA Kapat</el-button>
        </el-form>
      </div>
    </el-card>
  </div>
</template>

<script>
import { enable2FA, verify2FA, disable2FA } from '../../api/users'

export default {
  name: 'TwoFactorSetup',
  data() {
    return {
      setupData: null,
      verifyCode: '',
      disableCode: '',
      enabling: false,
      verifying: false,
      disabling: false,
    }
  },
  computed: {
    user() {
      return this.$store.getters['auth/currentUser'] || {}
    },
    qrImageUrl() {
      if (!this.setupData?.qr_url) return ''
      return `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(this.setupData.qr_url)}`
    },
  },
  methods: {
    async handleEnable() {
      this.enabling = true
      try {
        const { data } = await enable2FA()
        this.setupData = data.data
      } catch {
        this.$message.error('2FA baslatilamadi')
      } finally {
        this.enabling = false
      }
    },
    async handleVerify() {
      if (!this.verifyCode || this.verifyCode.length !== 6) {
        return this.$message.warning('6 haneli kodu girin')
      }
      this.verifying = true
      try {
        await verify2FA(this.verifyCode)
        this.$message.success('2FA etkinlestirildi!')
        this.$store.dispatch('auth/fetchUser')
        this.setupData = null
        this.verifyCode = ''
      } catch (err) {
        this.$message.error(err?.response?.data?.message || 'Dogrulama basarisiz')
      } finally {
        this.verifying = false
      }
    },
    async handleDisable() {
      if (!this.disableCode || this.disableCode.length !== 6) {
        return this.$message.warning('6 haneli kodu girin')
      }
      this.disabling = true
      try {
        await disable2FA(this.disableCode)
        this.$message.success('2FA kapatildi')
        this.$store.dispatch('auth/fetchUser')
        this.disableCode = ''
      } catch (err) {
        this.$message.error(err?.response?.data?.message || 'Islem basarisiz')
      } finally {
        this.disabling = false
      }
    },
  },
}
</script>
