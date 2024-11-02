<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
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
                            class="form-control"
                            v-model="searchTerm"
                            @input="searchFrais"
                            placeholder="Rechercher par nom..."
                        />

                        <PrimaryButton><a :href="route('frais.create')">Nouveau Frais</a></PrimaryButton>
              
                        <!-- Tableau des frais -->
                        <table v-if="filteredFrais.length > 0">
                            <thead>
                                <tr>
                                <th>ID</th>
                                <th>Description</th>
                                <th>Prix</th>         
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="frais in paginatedFrais" :key="frais.id">
                                
                                    <td>{{ frais.id }}</td>
                                    <td>{{ frais.description }}</td>
                                    <td>{{ frais.prix }}</td>          
                                            
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
      };
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

