<template>
  <div>
    <div class="container center-form w-50">
      <form>
        <div class="form-group mb-3">
          <label for="username">Email:</label>
          <input v-model="username" type="text" class="form-control input-width" id="username" placeholder="Enter email">
        </div>
        <div class="form-group mb-3">
          <label for="password">Password:</label>
          <input v-model="password" type="password" class="form-control input-width" id="password" placeholder="Enter password">
        </div>
        <button @click.prevent="login" type="submit" class="btn btn-primary mb-3">Sign in</button>
        <div v-if="error" class="text-danger">Invalid username or password</div>
      </form>
    </div>
  </div>
</template>

<script>
  import axios from "axios";

  export default {
    name: 'SignIn',

    data() {
      return {
        username: '',
        password: '',
        error: null
      }
    },

    methods: {
      login() {
        axios.post('/api/v1/auth/login', {username: this.username, password: this.password})
            .then(response => {
              localStorage.setItem('token', response.data.token);
              localStorage.setItem('refresh_token', response.data.refresh_token);

              this.$router.push({name: 'cart'});
            })
            .catch(error => {
              this.error = error;
              //const passwordInput = document.getElementById('password');
             // if (passwordInput) {
               // passwordInput.value = '';
                //this.password = '';
              //}
              this.password = '';
            })
      }
    }
  }
</script>

<style scoped>

</style>
