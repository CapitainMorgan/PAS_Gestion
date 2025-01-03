<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
</script>


<template>
  <AuthenticatedLayout>
    <Head title="Articles" />
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Articles
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
                    @input="searchArticles"
                    placeholder="Rechercher par nom..."
                  />                 

                  <PrimaryButton @click="createArticle()">Créer un nouveau</PrimaryButton>

                  <div style="margin-top: 10px;">
                      <select v-model="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                          <option value="En Stock">En Stock</option>
                          <option value="Vendu">Vendu</option>
                          <option value="Rendu">Rendu</option>
                          <option value="Rendu défectueux">Rendu défectueux</option>
                          <option value="Donné">Donné</option>
                          <option value="">Tout</option>
                      </select>                 
                  </div> 

                  
              
                  <!-- Tableau des articles -->
                  <table v-if="filteredArticles.length > 0">
                    <thead>
                        <tr>
                        <th>ID</th>
                        <th>Description</th>
                        <th>Taille</th>
                        <th>Quantité</th>
                        <th>Localisation</th>                        
                        <th>Prix Vente</th>
                        <th>Prix Client</th>
                        <th>Prix Solde</th>
                        <th>Date dépot</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="article in paginatedArticles" :key="article.id">    
                            <td @click="showArticle(article.id)">{{ article.id }}</td>    
                            <td @click="showArticle(article.id)">{{ article.description }}</td>
                            <td @click="showArticle(article.id)">{{ article.taille ?? 'N/A' }}</td>
                            <td @click="showArticle(article.id)">{{ article.quantite ?? 'N/A' }}</td>
                            <td @click="showArticle(article.id)">{{ article.localisation ?? 'N/A' }}</td>
                            <td @click="showArticle(article.id)">{{ article.prixVente ?? 'N/A' }}</td>
                            <td @click="showArticle(article.id)">{{ article.prixClient ?? 'N/A' }}</td>
                            <td @click="showArticle(article.id)">{{ article.prixSolde ?? 'N/A' }}</td>
                            <td @click="showArticle(article.id)">{{ formatDate(article.dateDepot) }}</td>
                        </tr>
                    </tbody>
                  </table>

                  <!-- Afficher un message s'il n'y a pas de articles -->
                  <p v-else>Aucun article trouvé.</p>
              
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
      articles: {
        type: Array,
        default: () => [], // Assurez-vous que c'est un tableau par défaut
      },
    },
    data() {
      return {
        searchTerm: '',
        currentPage: 1,
        pageSize: 10,
        status: 'En Stock',
      };
    },
    computed: {
      filteredArticles() {
        // Diviser le terme de recherche en mots clés
        const searchTerms = this.searchTerm.toLowerCase().split(/\s+/);

        return this.articles.filter(article => {
          // Combiner les champs de l'article à rechercher dans une seule chaîne
          const articleFields = [
            article.description?.toLowerCase() ?? '',
            article.localisation?.toLowerCase() ?? '',
            article.taille?.toLowerCase() ?? '',
            article.id?.toString().toLowerCase() ?? ''
          ].join(' ');

          // Vérifier si tous les mots clés de recherche sont présents dans les champs de l'article
          const matchesSearch = searchTerms.every(term => articleFields.includes(term));

          // Appliquer également le filtre par statut
          const matchesStatus = article.status.includes(this.status);

          return matchesSearch && matchesStatus;
        });
      },
      totalPages() {
        return Math.ceil(this.filteredArticles.length / this.pageSize);
      },
      paginatedArticles() {
        const start = (this.currentPage - 1) * this.pageSize;
        return this.filteredArticles.slice(start, start + this.pageSize);
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
      formatDate(date) {
      return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    },
      changePage(page) {
        if (page < 1 || page > this.totalPages) return; // Limiter la page
        this.currentPage = page;
      },
      createArticle() {
        this.$inertia.visit(route('article.create'));
      },
      showArticle(id) {
        this.$inertia.visit(route('article.show', id));
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
  