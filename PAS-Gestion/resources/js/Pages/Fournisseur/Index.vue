<template>
    <div>
      <h1>Liste des Fournisseurs</h1>
  
      <!-- Formulaire de filtres -->
      <form @submit.prevent="filter">
        <div>
          <label for="nom">Nom :</label>
          <input type="text" v-model="filters.nom" id="nom" />
        </div>
  
        <div>
          <label for="ville">Ville :</label>
          <input type="text" v-model="filters.ville" id="ville" />
        </div>
  
        <div>
          <label for="pays">Pays :</label>
          <input type="text" v-model="filters.pays" id="pays" />
        </div>
  
        <button type="submit">Rechercher</button>
        <button @click.prevent="resetFilters">Réinitialiser</button>
      </form>
  
      <!-- Tableau des fournisseurs -->
      <table v-if="fournisseurs.data && fournisseurs.data.length > 0">
        <thead>
            <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Ville</th>
            <th>Pays</th>
            <th>Date de Création</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="fournisseur in fournisseurs.data" :key="fournisseur.id">
            <td>{{ fournisseur.nom }}</td>
            <td>{{ fournisseur.prenom }}</td>
            <td>{{ fournisseur.ville }}</td>
            <td>{{ fournisseur.pays }}</td>
            <td>{{ fournisseur.dateCreation }}</td>
            </tr>
        </tbody>
      </table>

      <!-- Afficher un message s'il n'y a pas de fournisseurs -->
      <p v-else>Aucun fournisseur trouvé.</p>
  
      <!-- Pagination -->
      <div v-if="fournisseurs.links.length > 1">
        <button
          v-for="(link, index) in fournisseurs.links"
          :key="index"
          @click.prevent="getFournisseurs(link.url)"
          :disabled="!link.url"
          v-html="link.label"
        ></button>
      </div>
    </div>
  </template>
  
  <script>
  
  export default {
    props: {
      fournisseurs: Object,
      filters: {
        type: Object,
        default: () => ({
            nom: '',
            ville: '',
            pays: ''
        })
      }
    },
    data() {
      return {
        filters: {
          nom: this.filters.nom || '',
          ville: this.filters.ville || '',
          pays: this.filters.pays || ''
        },
      };
    },
    methods: {
      filter() {
        Inertia.get('/fournisseurs', this.filters, {
          preserveState: true
        });
      },
      resetFilters() {
        this.filters.nom = '';
        this.filters.ville = '';
        this.filters.pays = '';
        this.filter();
      },
      getFournisseurs(url) {
        Inertia.get(url, this.filters, {
          preserveState: true
        });
      }
    }
  };
  </script>
  
  <style>
  /* Ajoutez votre style ici */
  </style>
  