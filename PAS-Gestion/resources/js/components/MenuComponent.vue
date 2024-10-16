<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Mon Application</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#" @click="navigateTo('home')">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" @click="navigateTo('caisse')">Caisse</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" @click="navigateTo('produit')">Produit</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" @click="navigateTo('fournisseurs')">Fournisseurs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" @click="navigateTo('frais')">Frais du magasin</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" @click="navigateTo('parametres')">Paramètres</a>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
export default {
  methods: {
    navigateTo(page) {
      this.$emit('navigate', page);
    }
  }
};
</script>
