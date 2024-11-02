<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head } from '@inertiajs/vue3';
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
                        <p><strong>Adresse:</strong> {{ fournisseur.rue }}, {{ fournisseur.ville }}, {{ fournisseur.npa }}, {{ fournisseur.pays }}</p>                        
                        <p><strong>Remarque:</strong> {{ fournisseur.remarque ?? 'N/A' }}</p>
                        <p><strong>Date de Création:</strong> {{ formatDate(fournisseur.dateCreation) }}</p>
                    </div>
                    </div>

                    <h2>Articles du Fournisseur</h2>
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
                        <tr v-for="article in fournisseur.articles" :key="article.id">
                            <td><a class="link" :href="route('article.show', article.id)">{{ article.id }}</a></td>
                            <td><a class="link" :href="route('article.show', article.id)">{{ article.description }}</a></td>
                            <td><a class="link" :href="route('article.show', article.id)">{{ article.taille ?? 'N/A' }}</a></td>
                            <td><a class="link" :href="route('article.show', article.id)">{{ article.quantite ?? 'N/A' }}</a></td>
                            <td><a class="link" :href="route('article.show', article.id)">{{ article.localisation ?? 'N/A' }}</a></td>
                            <td><a class="link" :href="route('article.show', article.id)">{{ article.prixVente ?? 'N/A' }}</a></td>
                            <td><a class="link" :href="route('article.show', article.id)">{{ article.prixClient ?? 'N/A' }}</a></td>
                            <td><a class="link" :href="route('article.show', article.id)">{{ article.prixSolde ?? 'N/A' }}</a></td>
                            <td><a class="link" :href="route('article.show', article.id)">{{ article.depot_id ?? 'N/A' }}</a></td>
                        </tr>
                    </tbody>
                    </table>

                    <div v-if="!fournisseur.articles.length" class="alert alert-info">
                    Aucun article trouvé pour ce fournisseur.
                    </div>
                    <PrimaryButton @click="showModalFiche = true">Générer Fiche Client</PrimaryButton>

                    <!-- Utilisation du composant Modal -->
                    <Modal v-show="showModalFiche" @close="closeModal">
                      <template v-slot:header>
                        Générer Fiche Client
                      </template> 

                      <template v-slot:body>
                        <form @submit.prevent="generateFicheClient">
                          <!-- Choix du depot_id -->
                          <InputLabel for="depot_id">Choisir un dépot</InputLabel>
                          <select v-model="depot_id" required>
                            <option v-for="depot in depots_id" :key="depot" :value="depot">{{ depot }}</option>
                          </select>
                          <InputLabel for="depotDate">Depuis le ...</InputLabel>
                          <TextInput type="date" v-model="statusDate" required />
                        </form>
                      </template>

                      <template v-slot:footer>
                        <PrimaryButton type="button" class="btn btn-secondary" @click="closeModal">Annuler</PrimaryButton>
                        <PrimaryButton type="submit" class="btn btn-primary" @click="generateFicheClient">Générer</PrimaryButton>
                      </template>
                    </Modal>



                    <PrimaryButton><a :href="route('fournisseur.edit', fournisseur.id)">Modifier le Fournisseur</a></PrimaryButton>
                    <PrimaryButton><a :href="route('depot.create', fournisseur.id)">Faire un nouveau dépot</a></PrimaryButton>
                    <PrimaryButton><a :href="route('fournisseur.index')">Retour à la liste des Fournisseurs</a></PrimaryButton>
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
  },
  data() {
    return {
      showModalFiche: false,
      statusDate: null,
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
        window.open(`/fiche-fournisseur-vente/${this.fournisseur.id}/${this.statusDate}`, '_blank');
      }
      if (this.depot_id) {
        window.open(`/fiche-fournisseur-depot/${this.fournisseur.id}/${this.depot_id}`, '_blank');
      }
    },
    closeModal() {
      this.showModalFiche = false;
    }
  },
};
</script>