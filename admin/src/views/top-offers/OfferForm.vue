<template>
  <div>
    <div style="margin-bottom: 20px">
      <el-button icon="el-icon-back" @click="$router.go(-1)">Geri</el-button>
    </div>

    <el-card>
      <div slot="header">{{ isEdit ? 'Sponsor Düzenle' : 'Yeni Sponsor' }}</div>

      <el-form ref="form" :model="form" :rules="rules" label-width="120px" v-loading="loading" @submit.native.prevent="handleSubmit">
        <el-form-item label="Logo URL" prop="logo_url">
          <el-input v-model="form.logo_url" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Bonus Yazısı" prop="bonus_text">
          <el-input v-model="form.bonus_text" />
        </el-form-item>
        <el-form-item label="Buton Yazısı" prop="cta_text">
          <el-input v-model="form.cta_text" />
        </el-form-item>
        <el-form-item label="Hedef URL" prop="target_url">
          <el-input v-model="form.target_url" placeholder="https://..." />
        </el-form-item>
        <el-form-item label="Sıralama">
          <el-input-number v-model="form.sort_order" :min="0" />
        </el-form-item>
        <el-form-item label="Aktif">
          <el-switch v-model="form.is_active" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" :loading="saving" native-type="submit">
            {{ isEdit ? 'Güncelle' : 'Oluştur' }}
          </el-button>
          <el-button @click="$router.go(-1)">İptal</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
export default {
  name: 'OfferForm',
  props: {
    isEdit: { type: Boolean, default: false },
    offerData: { type: Object, default: null },
  },
  data() {
    return {
      form: {
        logo_url: '',
        bonus_text: '',
        cta_text: '',
        target_url: '',
        sort_order: 0,
        is_active: true,
      },
      rules: {
        logo_url: [{ required: true, message: 'Logo URL zorunludur', trigger: 'blur' }],
        bonus_text: [{ required: true, message: 'Bonus yazısı zorunludur', trigger: 'blur' }],
        cta_text: [{ required: true, message: 'Buton yazısı zorunludur', trigger: 'blur' }],
        target_url: [{ required: true, message: 'Hedef URL zorunludur', trigger: 'blur' }],
      },
      loading: false,
      saving: false,
    }
  },
  created() {
    if (this.offerData) {
      Object.keys(this.form).forEach((key) => {
        if (this.offerData[key] !== undefined) this.form[key] = this.offerData[key]
      })
    }
  },
  methods: {
    handleSubmit() {
      this.$refs.form.validate((valid) => {
        if (!valid) return
        this.$emit('submit', { ...this.form })
      })
    },
  },
}
</script>
