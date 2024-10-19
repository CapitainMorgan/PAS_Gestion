<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
</script>


<template>
  <AuthenticatedLayout>
    <Head title="Fournisseurs" />
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Fournisseurs
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">
              
                  <!-- Formulaire de filtres -->
                  <TextInput
                    type="text"
                    class="form-control"
                    v-model="searchTerm"
                    @input="searchFournisseurs"
                    placeholder="Rechercher par nom..."
                  />

                  <PrimaryButton><a :href="route('fournisseur.create')">Nouveau Fournisseur</a></PrimaryButton>
              
                  <!-- Tableau des fournisseurs -->
                  <table v-if="filteredFournisseurs.length > 0">
                    <thead>
                        <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>                        
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Téléphone</th>
                        <th>Numéro Professionnel</th> 
                        <th>Remarque</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="fournisseur in paginatedFournisseurs" :key="fournisseur.id">
                          
                            <td><a class="link" :href="route('fournisseur.show', fournisseur.id)">{{ fournisseur.id }}</a></td>
                            <td><a class="link" :href="route('fournisseur.show', fournisseur.id)">{{ fournisseur.nom }}</a></td>
                            <td><a class="link" :href="route('fournisseur.show', fournisseur.id)">{{ fournisseur.prenom }}</a></td>                        
                            <td><a class="link" :href="route('fournisseur.show', fournisseur.id)">{{ fournisseur.email }}</a></td>
                            <td><a class="link" :href="route('fournisseur.show', fournisseur.id)">{{ fournisseur.mobile }}</a></td>
                            <td><a class="link" :href="route('fournisseur.show', fournisseur.id)">{{ fournisseur.telephone }}</a></td>
                            <td><a class="link" :href="route('fournisseur.show', fournisseur.id)">{{ fournisseur.numProf }}</a></td>
                            <td><a class="link" :href="route('fournisseur.show', fournisseur.id)">{{ fournisseur.remarque }}</a></td>              
                                    
                        </tr>
                    </tbody>
                  </table>

                  <!-- Afficher un message s'il n'y a pas de fournisseurs -->
                  <p v-else>Aucun fournisseur trouvé.</p>
              
                  <!-- Pagination -->
                  <nav aria-label="Page navigation">
                    <ul class="pagination">
                      <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <SecondaryButton class="page-link" @click="changePage(currentPage - 1)">Précédent</SecondaryButton>
                      </li>
                      <li v-for="page in totalPages" :key="page" class="page-item" :class="{ active: currentPage === page }">
                        <SecondaryButton class="page-link" @click="changePage(page)">{{ page }}</SecondaryButton>
                      </li>
                      <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <SecondaryButton class="page-link" @click="changePage(currentPage + 1)">Suivant</SecondaryButton>
                      </li>
                    </ul>
                  </nav>
                </div>
              </div>
          </div>
      </div>
  </AuthenticatedLayout>
</template>
  
<script>


  export default {
    props: {
      fournisseurs: {
        type: Array,
        default: () => [], // Assurez-vous que c'est un tableau par défaut
      },
    },
    data() {
      return {
        searchTerm: '',
        currentPage: 1,
        pageSize: 10,
      };
    },
    computed: {
      filteredFournisseurs() {
        // Filtrer les fournisseurs en fonction du terme de recherche
        return this.fournisseurs.filter(fournisseur =>
        (fournisseur.nom?.toLowerCase() ?? '').includes(this.searchTerm.toLowerCase()) ||
        (fournisseur.prenom?.toLowerCase() ?? '').includes(this.searchTerm.toLowerCase()) ||
        (fournisseur.email?.toLowerCase() ?? '').includes(this.searchTerm.toLowerCase()) ||
        (fournisseur.mobile?.toLowerCase() ?? '').includes(this.searchTerm.toLowerCase()) ||
        (fournisseur.numProf?.toLowerCase() ?? '').includes(this.searchTerm.toLowerCase()) ||
        (fournisseur.id?.toString().toLowerCase() ?? '').includes(this.searchTerm.toLowerCase())
        );
      },
      totalPages() {
        return Math.ceil(this.filteredFournisseurs.length / this.pageSize);
      },
      paginatedFournisseurs() {
        const start = (this.currentPage - 1) * this.pageSize;
        return this.filteredFournisseurs.slice(start, start + this.pageSize);
      },
    },
    methods: {
      changePage(page) {
        if (page < 1 || page > this.totalPages) return; // Limiter la page
        this.currentPage = page;
      },
    }
  };
  </script>
  
  <style>
    td.link {
      width: 100%;
      height: 100%;
    }
  </style>
  