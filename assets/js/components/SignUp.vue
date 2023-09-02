<template>
  <div>
    <div class="container center-form w-50">
      <form>
        <div class="form-group mb-3">
          <label for="first-name">First name:</label>
          <input v-model="firstName" type="text" class="form-control input-width" id="first-name" placeholder="first name">
        </div>
        <div v-if="validationErrors.firstName" class="text-danger mb-3">{{ validationErrors.firstName }}</div>
        <div class="form-group mb-3">
          <label for="last-name">Last name:</label>
          <input v-model="lastName" type="text" class="form-control input-width" id="last-name" placeholder="last name">
        </div>
        <div v-if="validationErrors.lastName" class="text-danger mb-3">{{ validationErrors.lastName }}</div>
        <div class="form-group mb-3">
          <label for="username">Email:</label>
          <input v-model="email" type="text" class="form-control input-width" id="username" placeholder="email">
        </div>
        <div v-if="validationErrors.email" class="text-danger mb-3 ">{{ validationErrors.email }}</div>
        <div class="form-group mb-3">
          <label for="password">Password:</label>
          <input v-model="password" type="password" class="form-control input-width" id="password" placeholder="password">
        </div>
        <div v-if="validationErrors.password" class="text-danger mb-3">{{ validationErrors.password }}</div>
        <div class="form-group mb-3">
          <label for="confirm-password">Confirm password:</label>
          <input v-model="confirmPassword" type="password" class="form-control input-width" id="confirm-password" placeholder="confirm password">
        </div>
        <div v-if="validationErrors.confirmPassword" class="text-danger mb-3">{{ validationErrors.confirmPassword }}</div>
        <button @click.prevent="signUp" type="submit" class="btn btn-primary">Sign up</button>
      </form>
    </div>
  </div>
</template>

<script>
  import axios from "axios";

  export default {
    name: 'SignUp',

    data() {
      return {
        firstName: '',
        lastName: '',
        email: '',
        password: '',
        confirmPassword: '',

        validationErrors: {
          firstName: '',
          lastName: '',
          email: '',
          password: '',
          confirmPassword: '',
        }
      }
    },

    methods: {
      signUp() {
        this.error = null;

        axios.post('/api/v1/signUp', {firstName: this.firstName, lastName: this.lastName,
          email: this.email, password: this.password, confirmPassword: this.confirmPassword})
            .then(response => {
              localStorage.setItem('token', response.data.token);
              localStorage.setItem('refresh_token', response.data.refresh_token);

              this.$router.push({name: 'cart'});
            })
            .catch(error => {
                  for (const field in this.validationErrors) {
                    this.validationErrors[field] = '';
                  }

              this.mapValidationViolationsToValidationErrors(error);
            })
      },

      mapValidationViolationsToValidationErrors(error) {
        if (error.response.status === 409) {
          this.validationErrors.email = error.response.data.message;
        } else {
          for (const field in this.validationErrors) {
            for (const violation of error.response.data.details.violations) {
              if (field === violation.field){
                this.validationErrors[field] = violation.message;
              }
            }
          }
        }
      },
    },
  }
</script>

<style scoped>

</style>
