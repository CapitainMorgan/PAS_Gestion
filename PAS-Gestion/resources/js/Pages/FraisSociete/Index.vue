<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
import TextInput from '@/components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Frais Société" />
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Frais Société
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
                            @input="searchFrais"
                            placeholder="Rechercher par nom..."
                        />

                        <PrimaryButton @click="createFrais()">Créer un nouveau</PrimaryButton>
              
                        <!-- Tableau des frais -->
                        <table v-if="filteredFrais.length > 0">
                            <thead>
                                <tr>
                                <th>ID</th>
                                <th>Description</th>
                                <th>Prix</th>    
                                <th v-if="isAdmin">Action</th>     
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="frais in paginatedFrais" :key="frais.id">                                
                                  <td @click="editFrais(frais.id)">{{ frais.id }} </td>
                                  <td @click="editFrais(frais.id)">{{ frais.description }} </td>
                                  <td @click="editFrais(frais.id)">{{ frais.prix }} </td>    
                                  <td @click="deleteFrais(frais.id)" v-if="isAdmin">
                                    <DangerButton>Supprimer</DangerButton>
                                  </td>                                        
                                </tr>
                            </tbody>
                        </table>

                        <!-- Afficher un message s'il n'y a pas de frais -->
                        <p v-else>Aucun frais trouvé.</p>
                    
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
      frais: {
        type: Array,
        default: () => [], // Assurez-vous que c'est un tableau par défaut
      },
    },
    data() {
      return {
        searchTerm: '',
        currentPage: 1,
        pageSize: 10,
        isAdmin: false,
      };
    },
    mounted() {
      // Vérifier si l'utilisateur est un administrateur
      this.isAdmin = this.$page.props.auth.user.role  === 'admin';
    },
    computed: {
      filteredFrais() {
        // Filtrer les frais en fonction du terme de recherche
        return this.frais.filter(frais =>
        (frais.description?.toLowerCase() ?? '').includes(this.searchTerm.toLowerCase()) ||
        (frais.prix?.toLowerCase() ?? '').includes(this.searchTerm.toLowerCase()) ||
        (frais.id?.toString().toLowerCase() ?? '').includes(this.searchTerm.toLowerCase())
        );
      },
      totalPages() {
        return Math.ceil(this.filteredFrais.length / this.pageSize);
      },
      paginatedFrais() {
        const start = (this.currentPage - 1) * this.pageSize;
        return this.filteredFrais.slice(start, start + this.pageSize);
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
      createFrais() {
        this.$inertia.visit('/frais.create');
      },
      editFrais(id) {
        this.$inertia.visit(`/frais/${id}/edit`);
      },
      deleteFrais(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce frais ?')) {
          // Si l'utilisateur confirme, envoyer la requête DELETE avec Inertia
          this.$inertia.delete(route('frais-societe.destroy', id));
        }
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

