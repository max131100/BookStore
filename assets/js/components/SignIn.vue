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
        <button @click.prevent="login" type="submit" class="btn btn-primary">Sign in</button>
      </form>
    </div>
  </div>
</template>

<script>
  import axios from "axios";
  import router from "../router";

  export default {
    name: 'SignIn',

    data() {
      return {
        username: null,
        password: null
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
      }
    }
  }
</script>

<style scoped>

</style>
