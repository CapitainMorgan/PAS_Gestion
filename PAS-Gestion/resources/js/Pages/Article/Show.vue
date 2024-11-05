<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head } from '@inertiajs/vue3';
</script>


<template>
  <AuthenticatedLayout>
    <Head title="Articles" />
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Détails de l'Article
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
                            <h5 class="card-title">{{ article.description }}</h5>
                            <p><strong>ID:</strong> {{ article.id }}</p>
                            <p><strong>Taille:</strong> {{ article.taille ?? 'N/A' }}</p>
                            <p><strong>Prix Vente:</strong> {{ article.prixVente ?? 'N/A' }}</p>
                            <p><strong>Prix Client:</strong> {{ article.prixClient ?? 'N/A' }}</p>
                            <p><strong>Prix Solde:</strong> {{ article.prixSolde ?? 'N/A' }}</p>
                            <p><strong>Quantité:</strong> {{ article.quantite ?? 'N/A' }}</p>
                            <p><strong>Localisation:</strong> {{ article.localisation ?? 'N/A' }}</p>
                            <p><strong>Status</strong> {{ article.status }}</p>
                            <p><strong>ID Dépot:</strong> {{ article.depot_id ?? 'N/A' }}</p>
                            <p @click="showFournisseur(article.fournisseur[0].id)"><strong>Appartient à:</strong> {{ article.fournisseur[0].nom }} {{ article.fournisseur[0].prenom }}</p>
                            <p @click="showFournisseur(article.fournisseur[0].id)"><strong>Email du Fournisseur:</strong> {{ article.fournisseur[0].email }}</p>
                        </div>
                    </div>

                    <SecondaryButton><a class="link" :href="route('article.edit', article.id)">Modifier l'article</a></SecondaryButton>

                    <div v-if="article.frais.length > 0">
                      <h2 style="margin-top: 10px;">Frais associés</h2>
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Description</th>
                            <th>Prix</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="frais in article.frais" :key="frais.id">
                            <td @click="showModal(frais)">{{ frais.description }}</td>
                            <td @click="showModal(frais)">{{ frais.prix }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <!-- Utilisation du composant Modal -->
                    <Modal v-show="showModalFrais" @close="closeModal">
                      <template v-slot:header>
                        Modifier frais
                      </template> 

                      <template v-slot:body>
                        <form @submit.prevent="updateFrais">
                          <!-- Choix du depot_id -->
                          <InputLabel for="description" class="label">Description</InputLabel>                          
                          <TextInput style="width:100%" type="text" v-model="description" required />
                          <InputLabel for="prix" class="label">Depuis le ...</InputLabel>
                          <TextInput style="width:100%" type="number" step=".01" v-model="prix" required />                                                   
                        </form>
                      </template>

                      <template v-slot:footer>
                        <PrimaryButton type="submit" class="btn btn-primary" @click="updateFrais">Modifier</PrimaryButton>
                      </template>
                    </Modal>

                    <h3 style="margin-top: 10px;" >Ajouter un frais</h3>
                    <form @submit.prevent="addFrais" class="form-grid">
                      <div class="form-group">
                        <InputLabel for="description" class="form-label">Description</InputLabel>
                        <TextInput type="text" v-model="newFrais.description" class="form-control" placeholder="Description" required />
                      </div>
                      <div class="form-group">
                        <InputLabel for="prix" class="form-label">Prix</InputLabel>
                        <TextInput type="number" v-model="newFrais.prix" class="form-control" placeholder="Prix" required />
                      </div>
                      <div class="form-group full-width">
                        <PrimaryButton type="submit" class="btn btn-primary">Ajouter le frais</PrimaryButton>
                      </div>
                    </form>
                    <div class="form-group full-width">
                      <PrimaryButton class="btn btn-primary" @click="generateBarcode">Générer Code-barres</PrimaryButton>
                    </div>
                    <div class="form-group full-width">
                      <PrimaryButton class="btn btn-primary" @click="indexArticle()">Retour à la liste des Articles</PrimaryButton>
                    </div>
                    </div>

                </div>
            </div>
          </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      newFrais: {
        description: '',
        prix: null,
      },
      description: '',
      prix: null,
      id_frais: null,
      showModalFrais: false,
    };
  },
  methods: {
    async addFrais() {
      try {
        await this.$inertia.post(route('fraisArticle.store', this.article.id), this.newFrais);
        this.$toast.success('Frais ajouté avec succès');
        this.newFrais.description = '';
        this.newFrais.prix = null;
      } catch (error) {
        console.error(error);
        this.$toast.error("Une erreur est survenue lors de l'ajout du frais.");
      }
    },    
    generateBarcode() {
      // Utiliser l'ID de l'article pour générer le code-barres
      const articleId = this.article.id;

      // Rediriger vers la route pour générer le code-barres
      window.open(`/generate-barcode/${articleId}`, '_blank');
    },
    showFournisseur(fournisseurId) {
      this.$inertia.visit(route('fournisseur.show', fournisseurId));
    },
    indexArticle() {
      this.$inertia.visit(route('article.index'));
    },
    showModal(frais) {
      this.description = frais.description;
      this.prix = frais.prix;
      this.id_frais = frais.id;
      this.showModalFrais = true;
    },
    closeModal() {
      this.showModalFrais = false;
    },
    async updateFrais() {
      try {
        await this.$inertia.put(route('fraisArticle.update', this.id_frais), {
          description: this.description,
          prix: this.prix,
        });
        this.closeModal();
      } catch (error) {
        console.error(error);
        this.$toast.error("Une erreur est survenue lors de la mise à jour du frais.");
      }
    },
  },
};
</script>

<style scoped>
.btn {
  margin-top: 5px;
}
</style>