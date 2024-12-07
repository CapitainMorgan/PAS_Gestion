<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
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
                    class="form-control search-input"
                    v-model="searchTerm"
                    @input="searchFournisseurs"
                    placeholder="Rechercher par nom..."
                  />

                  <PrimaryButton @click="createFournisseur()">Créer un nouveau</PrimaryButton>
              
                  <!-- Tableau des fournisseurs -->
                  <table v-if="filteredFournisseurs.length > 0">
                    <thead>
                        <tr>
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
                          
                            <td @click="showFournisseur(fournisseur.id)">{{ fournisseur.nom ?? 'N/A'}}</td>
                            <td @click="showFournisseur(fournisseur.id)">{{ fournisseur.prenom ?? 'N/A'}}</td>                        
                            <td @click="showFournisseur(fournisseur.id)">{{ fournisseur.email ?? 'N/A' }}</td>
                            <td @click="showFournisseur(fournisseur.id)">{{ fournisseur.mobile ?? 'N/A'}}</td>
                            <td @click="showFournisseur(fournisseur.id)">{{ fournisseur.telephone ?? 'N/A'}}</td>
                            <td @click="showFournisseur(fournisseur.id)">{{ fournisseur.numProf ?? 'N/A'}}</td>
                            <td @click="showFournisseur(fournisseur.id)">{{ fournisseur.remarque ?? 'N/A' }}</td>              
                                    
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
                      <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: currentPage === page }">
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
      visiblePages() {
        const pages = [];
        const start = Math.max(1, this.currentPage - 1);
        const end = Math.min(this.totalPages, this.currentPage + 1);

        for (let i = start; i <= end; i++) {
          pages.push(i);
        }

        // Si la première page est affichée, on peut ajouter la page 2
        if (start > 1) {
          pages.unshift(start - 1); // Ajouter la page précédente si elle n'est pas affichée
        }

        // Si la dernière page est affichée, on peut ajouter la page juste avant la dernière
        if (end < this.totalPages) {
          pages.push(end + 1); // Ajouter la page suivante si elle n'est pas affichée
        }

        return pages;
      },
    },
    methods: {
      changePage(page) {
        if (page < 1 || page > this.totalPages) return; // Limiter la page
        this.currentPage = page;
      },
      createFournisseur() {
        this.$inertia.visit(route('fournisseur.create'));
      },
      showFournisseur(id) {
        this.$inertia.visit(route('fournisseur.show', id));
      },
    }
  };
  </script>
  
  