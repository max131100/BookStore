/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import '../styles/app.scss';
import {createApp} from "vue";
import router from "./router";
import App from "./views/App.vue";
import 'bootstrap';


const app = createApp(App);

app.use(router);

app.mount('#app');


