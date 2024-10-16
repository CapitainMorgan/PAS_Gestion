require('./bootstrap');


import { createApp } from 'vue';

import MenuComponent from './components/MenuComponent.vue';
import HomeComponent from './components/HomeComponent.vue';

const app = createApp({});

// Enregistrez les composants Vue
app.component('menu-component', MenuComponent);
app.component('home-component', HomeComponent);

app.mount('#app');