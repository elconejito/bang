<template>
  <div class="container">
    <div class="row">
      <div class="col" v-if="success">
        <p>You have successfully registered. Please login now.</p>
        <router-link :to="{ name: 'login' }">Login</router-link>
      </div>
      <div class="col" v-else>
        <h1>Register</h1>
        <FormError v-if="error" :error="error" />
        <div class="mb-3">
          <label for="email" class="form-label">Name</label>
          <input type="text" class="form-control" id="name" v-model="name">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" class="form-control" id="email" v-model="email">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" v-model="password">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" v-model="password_confirmation">
        </div>
        <button class="btn btn-primary" @click="register" :disabled="isLoading">
          Register
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import FormError from 'components/FormError.vue';
import HasLoading from '@/mixins/HasLoading';

export default {
  name: 'Register',
  components: { FormError },
  mixins: [HasLoading],
  data() {
    return {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      error: null,
      success: false,
    };
  },
  methods: {
    register() {
      this.isLoading = true;
      this.$set(this.loadingQueue, 'register', false);

      const payload = {
        name: this.name,
        email: this.email,
        password: this.password,
        password_confirmation: this.password_confirmation,
      };

      this.$store
        .dispatch('auth/register', payload)
        .then((response) => {
          console.log('Register register() then', response);
          this.loadingQueue.register = true;
          this.success = true
        })
        .catch((error) => {
          console.error('Register register() catch', error);
          this.error = this.$errorProcessor(error);
        });
    },
  },
};
</script>

<style></style>
