<template>
  <div class="container">
    <div class="row">
      <div class="col">
        <h1>Login</h1>
        <FormError v-if="error" :error="error" />
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" v-model="email">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" v-model="password">
        </div>
        <button class="btn btn-primary" @click="login">
          Log in
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import HasLoading from '@/mixins/HasLoading';
import FormError from '@/components/FormError';

export default {
  name: 'Login',
  components: { FormError },
  mixins: [HasLoading],
  data() {
    return {
      email: '',
      password: '',
      error: null,
    };
  },
  methods: {
    // Log the user in
    login() {
      this.isLoading = true;
      this.$set(this.loadingQueue, 'login', false);

      const payload = {
        email: this.email,
        password: this.password,
      };

      this.$store
        .dispatch('auth/login', payload)
        .then((response) => {
          console.log('Login login() then', response);
          this.$router.push({ name: 'dashboard' })
        })
        .catch((error) => {
          console.error('Login login() catch', error);
        })
        .finally(() => {
          console.log('Login login() finally');
          this.loadingQueue.login = true;
        });
    },
  },
};
</script>

<style></style>
