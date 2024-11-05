<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head } from '@inertiajs/vue3';
import vSelect from 'vue-select';
</script>

<template>
    <AuthenticatedLayout>
      <Head title="Fournisseurs" />
      <template #header>
              <h2
                  class="text-xl font-semibold leading-tight text-gray-800"
              >
                  {{this.fournisseur.nom}}  {{this.fournisseur.prenom}}
              </h2>
    </template>
        <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">

                    <div class="card mb-4">
                    <div class="card-body">
                        <p><strong>ID:</strong> {{ fournisseur.id }}</p>
                        <p><strong>Email:</strong> {{ fournisseur.email ?? 'N/A' }}</p>
                        <p><strong>Mobile:</strong> {{ fournisseur.mobile ?? 'N/A' }}</p>
                        <p><strong>Telephone:</strong> {{ fournisseur.telephone ?? 'N/A' }}</p>
                        <p><strong>Numéro Professionnel:</strong> {{ fournisseur.numProf ?? 'N/A' }}</p>
                        <p><strong>Adresse:</strong> 
                          <p v-if="fournisseur.rue || fournisseur.ville || fournisseur.npa || fournisseur.pays">{{ fournisseur.rue }}, {{ fournisseur.ville }}, {{ fournisseur.npa }}, {{ fournisseur.pays }}</p>
                          <p v-else>N/A</p>
                        </p>                        
                        <p><strong>Remarque:</strong> {{ fournisseur.remarque ?? 'N/A' }}</p>
                        <p><strong>Date de Création:</strong> {{ formatDate(fournisseur.dateCreation) }}</p>
                    </div>
                    </div>

                    <div v-if="!fournisseur.articles.length" class="alert alert-info">
                    Aucun article trouvé pour ce fournisseur.
                    </div>
                    <div v-else>
                      <h1>Articles du Fournisseur</h1>
                      <table class="table">
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
                          <th>ID Dépot</th>
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
                              <td @click="showArticle(article.id)">{{ article.depot_id ?? 'N/A' }}</td>
                          </tr>
                      </tbody>
                      </table>

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

                    
                    <PrimaryButton class="button" @click="showModalFiche = true; getDepotIdFromFournisseurArticles()">Générer Fiche Client</PrimaryButton>

                    <!-- Utilisation du composant Modal -->
                    <Modal v-show="showModalFiche" @close="closeModal">
                      <template v-slot:header>
                        Générer Fiche Client
                      </template> 

                      <template v-slot:body>
                        <form @submit.prevent="generateFicheClient">
                          <!-- Choix du depot_id -->
                          <InputLabel for="depot_id" class="label">Choisir un dépot</InputLabel>
                          <v-select
                            :options="depots_id"
                            v-model="depot_id"
                            :reduce="depot => depot"
                            label="depot_id"
                            placeholder="Choisir un dépot"
                            search-placeholder="Rechercher un dépot..."
                            />
                          <InputLabel for="depotDate" class="label">Depuis le ...</InputLabel>
                          <TextInput style="width:100%" type="date" v-model="statusDate" required />
                          <InputLabel for="conditionGenerale" class="label">Conditions Générales</InputLabel>
                          <textarea v-model="tempConditionGenerale" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" id="conditionGenerale" rows="3" placeholder="Conditions Générales"></textarea>                          
                        </form>
                      </template>

                      <template v-slot:footer>
                        <PrimaryButton type="submit" class="btn btn-primary" @click="generateFicheClient">Générer</PrimaryButton>
                      </template>
                    </Modal>

                    <PrimaryButton class="button" @click="editFournisseur(fournisseur.id)">Modifier le Fournisseur</PrimaryButton>
                    <PrimaryButton class="button" @click="createDepot(fournisseur.id)">Faire un nouveau dépot</PrimaryButton>
                    <PrimaryButton class="button" @click="indexFournisseur()">Retour à la liste des Fournisseurs</PrimaryButton>
                </div>        
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<script>
export default {
  props: {
    fournisseur: {
      type: Object,
      required: true,
    },
    conditionGenerale: {
      type: String,
      required: true,
    },
  },
  data() {
    return {      
      searchTerm: '',
      currentPage: 1,
      pageSize: 10,
      showModalFiche: false,
      statusDate: null,
      tempConditionGenerale: this.conditionGenerale,
      depot_id: null,
      depots_id: [],
    };
  },
  computed: {
    getDepotIdFromFournisseurArticles() {
      this.depots_id = this.fournisseur.articles.map((article) => article.depot_id);
      // delete duplicate values
      this.depots_id = [...new Set(this.depots_id)];
    },
    totalPages() {
        return Math.ceil(this.fournisseur.articles.length / this.pageSize);
    },
    paginatedArticles() {
      const start = (this.currentPage - 1) * this.pageSize;
      return this.fournisseur.articles.slice(start, start + this.pageSize);
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
    generateFicheClient() {      
      if (this.statusDate) {        
        window.open(`/fiche-fournisseur-vente/${this.fournisseur.id}/${this.statusDate}/${this.tempConditionGenerale}`, '_blank');
      }
      if (this.depot_id) {
        window.open(`/fiche-fournisseur-depot/${this.fournisseur.id}/${this.depot_id}/${this.tempConditionGenerale}`, '_blank');
      }
      else {
        window.open(`/fiche-fournisseur/${this.fournisseur.id}/${this.tempConditionGenerale}`, '_blank');
      }
    },
    changePage(page) {
        if (page < 1 || page > this.totalPages) return; // Limiter la page
        this.currentPage = page;
    },
    closeModal() {
      this.showModalFiche = false;
    },
    showArticle(id) {
      this.$inertia.visit(`/article.show/${id}`);
    },
    editFournisseur(id) {
      this.$inertia.visit(`/fournisseur/${id}/edit`);
    },
    createDepot(id) {
      this.$inertia.visit(`/depot/${id}`);
    },
    indexFournisseur() {
      this.$inertia.visit('/fournisseur.index');
    },
  },
};
</script>


<style>
@import "vue-select/dist/vue-select.css";

</style>

<style scoped>
.label {
  margin-top: 10px ;
  margin-bottom: 5px ;
}
</style>