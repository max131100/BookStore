<template>
  <v-app>
    <v-container>
      <!-- <router-link :to="{name: 'person.index'}">People</router-link>
      <br>
      <router-link :to="{name: 'person.create'}">Add</router-link> -->
      <router-view></router-view>
    </v-container>
  </v-app>
</template>

<script>
import {defineComponent} from "vue";

export default defineComponent({
  name: "App"
})
</script>

<style scoped>

</style>
