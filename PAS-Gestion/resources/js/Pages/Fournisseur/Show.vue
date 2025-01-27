<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import InputLabel from '@/components/InputLabel.vue';
import { Head } from '@inertiajs/vue3';
import vSelect from 'vue-select';
</script>

<template>
    <AuthenticatedLayout>
      <Head title="Fournisseurs" />
      <template #header>
              <h2
                  class="text-xl font-semibold leading-tight text-gray-800"
                  :style="{color:  fournisseur.color }"
              >
                  {{fournisseur.nom}}  {{fournisseur.prenom}}
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
                        <p><strong>Date de Création:</strong> {{ formatDate(fournisseur.created_at) }}</p>
                    </div>
                    </div>
                    <TextInput
                      type="text"
                      class="form-control search-input"
                      v-model="searchTerm"
                      @input="paginatedArticles"
                      placeholder="Rechercher par nom..."
                    />                 

                    <div style="margin-top: 10px;">
                        <select @change="paginatedArticles" v-model="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="En Stock">En Stock</option>
                            <option value="Vendu">Vendu</option>
                            <option value="Rendu">Rendu</option>
                            <option value="Rendu défectueux">Rendu défectueux</option>
                            <option value="Donné">Donné</option>
                            <option value="">Tout</option>
                        </select>                 
                    </div> 

                    <div v-if="!filteredArticles.length" class="alert alert-info">
                    Aucun article trouvé pour ce fournisseur.
                    </div>
                    <div v-else style="margin-top: 10px;">
                      <h1>Articles du Fournisseur</h1>
                      <table class="table">
                      <thead>
                          <tr>
                          <th>ID</th>
                          <th>Description</th>
                          <th>Localisation</th>                        
                          <th>Prix Vente</th>
                          <th>Prix Client</th>
                          <th>Prix Solde</th>
                          <th>Total des frais</th>
                          <th>Total des ventes</th>
                          <th>Date Dépot</th>
                          <th>Date chagement de status</th>
                          <th>Est payé</th>

                          </tr>
                      </thead>
                      <tbody>
                          <tr v-for="article in searchArticles" :key="article.id" :style="{color:  article.color }">
                              <td @click="showArticle(article.id)">{{ formatIdArticle(article) }}</td>
                              <td @click="showArticle(article.id)">{{ article.description }}</td>
                              <td @click="showArticle(article.id)">{{ article.localisation ?? 'N/A' }}</td>
                              <td @click="showArticle(article.id)">{{ article.prixVente ?? 'N/A' }}</td>
                              <td @click="showArticle(article.id)">{{ article.prixClient ?? 'N/A' }}</td>
                              <td @click="showArticle(article.id)">{{ article.prixSolde ?? 'N/A' }}</td>
                              <td @click="showArticle(article.id)">{{ article.frais }}</td>
                              <td @click="showArticle(article.id)">{{ article.vente_total }}</td>
                              <td @click="showArticle(article.id)">{{ formatDate(article.dateDepot) }}</td>
                              <td @click="showArticle(article.id)">{{ formatDate(article.dateStatus) }}</td>
                              <td><input type="checkbox" @change="updateIsPaid(article)" v-model="article.isPaid" :modelValue="String(article.isPaid)"></input></td>
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


                    <TextInput style="margin-right:100%; margin-bottom: 5px;" type="date" v-model="printDate" required />
                    <PrimaryButton style="margin-bottom: 5px;" class="button" @click="print()">Imprimer Code Barre</PrimaryButton>
                    
                    <PrimaryButton class="button" @click="showModalFiche = true; getDepotIdFromFournisseurArticles()">Générer Fiche Client</PrimaryButton>

                    <!-- Utilisation du composant Modal -->
                    <Modal v-show="showModalFiche" @close="closeModal">
                      <template v-slot:header>
                        Générer Fiche Client
                      </template> 

                      <template v-slot:body>
                        <div class="form-group">
                          <select v-model="ficheChoice" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                              <option value="1">Fiche Dépot</option>
                              <option value="2">Fiche Vente</option>
                              <option value="3">Fiche Conditions Générales</option>
                          </select>
                      </div>

                        <form @submit.prevent="generateFicheClient">
                          <!-- Choix du depot_id -->
                          <InputLabel v-if="ficheChoice == 1" for="depotDate" class="label">Dépot le ...</InputLabel>
                          <TextInput v-if="ficheChoice == 1" style="width:100%" type="date" v-model="dateDepot" required />
                          <InputLabel v-if="ficheChoice == 2" for="venteDate" class="label">Vendu depuis le ...</InputLabel>
                          <TextInput v-if="ficheChoice == 2" tyle="width:100%" type="date" v-model="statusDate" required />
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
                    <div v-if="isAdmin" style="margin-top: 10px;">
                      <DangerButton class="boutton" @click="deleteFournisseur()">Supprimer le fournisseur</DangerButton>
                    </div>
                </div>        
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<script>
  import { useToast } from 'vue-toastification';

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
      dateDepot: null,
      ficheChoice: 1,
      isAdmin: false,
      searchTerm: '',
      status: 'En Stock',
      printDate: new Date().toISOString().substr(0, 10),
      searchArticles: [],
    };
  },
  mounted() {
    // Vérifier si l'utilisateur est un administrateur
    this.isAdmin = this.$page.props.auth.user.role  === 'admin';
    this.paginatedArticles();
  },
  computed: {
    totalPages() {
        return Math.ceil(this.filteredArticles.length / this.pageSize);
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
    filteredArticles() {
      // Diviser le terme de recherche en mots clés
      const searchTerms = this.searchTerm.toLowerCase().split(/\s+/);

      return this.fournisseur.articles.filter(article => {
        // Combiner les champs de l'article à rechercher dans une seule chaîne
        const articleFields = [
          article.description?.toLowerCase() ?? '',
          article.localisation?.toLowerCase() ?? '',
          article.taille?.toLowerCase() ?? '',
          article.id?.toString().toLowerCase() ?? ''
        ].join(' ');

        //change all Article isPaid to true false if 0 or 1
        article.isPaid = article.isPaid == 1 ? true : false;

        // Vérifier si tous les mots clés de recherche sont présents dans les champs de l'article
        const matchesSearch = searchTerms.every(term => articleFields.includes(term));

        // Appliquer également le filtre par statut
        const matchesStatus = article.status.includes(this.status);        

        return matchesSearch && matchesStatus;
      });
    },
  },
  methods: {
    
    paginatedArticles() {
      const start = (this.currentPage - 1) * this.pageSize;
      this.searchArticles = this.filteredArticles.slice(start, start + this.pageSize);
    },
    formatIdArticle(article) {
      const createdAt = new Date(article.created_at);

      // Format en "dmy" (jour-mois-année)
      const date = `${String(createdAt.getDate()).padStart(2, '0')}${String(createdAt.getMonth() + 1).padStart(2, '0')}${String(createdAt.getFullYear()).slice(2)}`;

      const id = article.id.toString();
      let fournisseurId = this.fournisseur.id.toString();

      if (
        id.startsWith(fournisseurId) && 
        id.endsWith(date)
      ) {
        return `${fournisseurId}-${id.slice(fournisseurId.length, -date.length)}-${date}`;
      }
      return id;
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    },
    print(){
      window.open(`/generate-barcode/${this.fournisseur.id}/${this.printDate}`, '_blank');
    },
    async updateIsPaid(article) {
      //change article.isPaid to 0 or 1
      article.isPaid = article.isPaid ? 1 : 0;
      console.log(article.isPaid);
      const response = await axios.put(route('article.updateIsPaid', article.id), {
        article
      });
      if (response.data.success) {
        useToast().success('Le statut de paiement a été mis à jour.');
      }
    },
    deleteFournisseur() {
      if (confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')) {
        // Si l'utilisateur confirme, envoyer la requête DELETE avec Inertia
        this.$inertia.delete(route('fournisseur.destroy', this.fournisseur.id));
      }
    },
    generateFicheClient() { 
      if (this.tempConditionGenerale == null || (this.dateDepot == null && this.statusDate == null)) {
        return;
      }      
      if (this.ficheChoice == 2) {        
        window.open(`/fiche-fournisseur-vente/${this.fournisseur.id}/${this.statusDate}/${encodeURIComponent(this.tempConditionGenerale)}`, '_blank');
      }
      if (this.ficheChoice == 1) {
        window.open(`/fiche-fournisseur-depot/${this.fournisseur.id}/${this.dateDepot}/${encodeURIComponent(this.tempConditionGenerale)}`, '_blank');
      }
      else {
        window.open(`/fiche-fournisseur/${this.fournisseur.id}/${encodeURIComponent(this.tempConditionGenerale)}`, '_blank');
      }
    },
    changePage(page) {
        if (page < 1 || page > this.totalPages) return; // Limiter la page
        this.currentPage = page;
        this.paginatedArticles();
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