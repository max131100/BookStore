<template>
      <!-- <router-link :to="{name: 'person.index'}">People</router-link>
      <br>
      <router-link :to="{name: 'person.create'}">Add</router-link> -->
      <header id="header">
        <nav>
          <div id="top-nav" class="container">
              <div class="row align-items-center">
                <div class="col-md-3">
              <router-link :to="{name: 'home.index'}">
                  <img src="./../../../public/upload/assets/book-store-logo.jpg" alt="logo" height="120"/>
              </router-link>
                </div>
                <div class="col-md-7">
                  <input class="form-control" type="text" placeholder="search for book...">
                </div>
                <div class="col-md-1">
                  <router-link :to="{name: 'login'}">
                    sign in
                  </router-link>
                </div>
                <div class="col-md-1">
                  <router-link :to="{name: 'cart'}">
                    cart
                  </router-link>
                </div>
            </div>
          </div>
        </nav>
      </header>
      <router-view></router-view>
</template>

<script>
import {defineComponent} from "vue";
import home from "../components/Home.vue";

export default defineComponent({
  name: "App",

  data() {
    return {

    };
  },
})
</script>

<style scoped>

</style>
