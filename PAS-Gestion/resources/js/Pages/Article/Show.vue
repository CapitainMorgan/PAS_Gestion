<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
import Modal from '@/components/Modal.vue';
import TextInput from '@/components/TextInput.vue';
import InputLabel from '@/components/InputLabel.vue';
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
                            <h1 style="font-size: 30px" :style="{color:  article.color }">{{ article.description }}</h1>
                            <p @click="showFournisseur(article.fournisseur.id)"><strong>ID Fournisseur:</strong> {{ article.fournisseur.id ?? 'N/A' }}</p>
                            <p @click="showFournisseur(article.fournisseur.id)"><strong>Appartient à:</strong> {{ article.fournisseur.nom }} {{ article.fournisseur.prenom }}</p>
                            <p @click="showFournisseur(article.fournisseur.id)"><strong>Email du Fournisseur:</strong> {{ article.fournisseur.email }}</p>

                            <p><strong>ID Article:</strong> {{ formatIdArticle(article) }}</p>
                            <p><strong>Taille:</strong> {{ article.taille ?? 'N/A' }}</p>
                            <p><strong>Prix Vente:</strong> {{ article.prixVente ?? 'N/A' }}</p>
                            <p><strong>Prix Client:</strong> {{ article.prixClient ?? 'N/A' }}</p>
                            <p><strong>Prix Solde:</strong> {{ article.prixSolde ?? 'N/A' }}</p>
                            <p><strong>Quantité:</strong> {{ article.quantite ?? 'N/A' }}</p>
                            <p><strong>Localisation:</strong> {{ article.localisation ?? 'N/A' }}</p>
                            <p><strong>Status</strong> {{ article.status }}</p>
                            <p><strong>Créé le:</strong> {{ formatDate(article.created_at) }}</p>
                            <p><strong>Est payé</strong> {{ article.isPaid ? "Oui" : "Non" }}</p>
                            <p><strong>Date d'échéance:</strong> {{ formatDate(article.dateEcheance) }}</p> 
                            <div v-if="new Date(article.dateEcheance) < new Date()">
                              <p v-if="article.statusMail == 0" style="color: red;"> Article en fin d'écheance</p> 
                              <p v-if="article.statusMail == 1" >Mail envoyé</p>
                              <p v-if="article.statusMail == 0" >Mail non envoyé</p>
                            </div>
                            <p><strong>Modifié le:</strong> {{ formatDate(article.dateStatus) }}</p>                          
                            <p><strong>Crée par :</strong> {{ article.user.name }} {{ article.user.email }}</p>
                            <p @click="showFournisseur(article.fournisseur_id_transit)"><strong>En transit chez : </strong> {{ article.fournisseur_id_transit }}</p>

                        </div>
                    </div>

                    <SecondaryButton @click="update()">Modifier l'article</SecondaryButton>

                    <div v-if="article.frais.length > 0">
                      <h2 style="margin-top: 10px;">Frais associés</h2>
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Description</th>
                            <th>Prix</th>
                            <th v-if="isAdmin">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="frais in article.frais" :key="frais.id">
                            <td @click="showModal(frais)">{{ frais.description }}</td>
                            <td @click="showModal(frais)">{{ frais.prix }}</td>
                            <td v-if="isAdmin">
                              <div class="form-group full-width">
                                <DangerButton class="btn btn-primary" @click="deleteFrais(frais.id)">Supprimer le frais</DangerButton>
                              </div>
                            </td>
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
                    <div v-if="isAdmin" class="form-group full-width">
                      <DangerButton class="btn btn-primary" @click="deleteArticle()">Supprimer l'article</DangerButton>
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
      isAdmin: false,
    };
  },
  mounted() {
    // Vérifier si l'utilisateur est un administrateur
    this.isAdmin = this.$page.props.auth.user.role  === 'admin';
  },
  methods: {
    formatIdArticle(article) {
      const createdAt = new Date(article.created_at);

      // Format en "dmy" (jour-mois-année)
      const date = `${String(createdAt.getDate()).padStart(2, '0')}${String(createdAt.getMonth() + 1).padStart(2, '0')}${String(createdAt.getFullYear()).slice(2)}`;

      const id = article.id.toString();
      let fournisseurId = article.fournisseur_id.toString();

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
    async addFrais() {
      const toast = useToast();
      try {
        await this.$inertia.post(route('fraisArticle.store', this.article.id), this.newFrais);
        toast.success('Frais ajouté avec succès');
        this.newFrais.description = '';
        this.newFrais.prix = null;
      } catch (error) {
        console.error(error);
        toast.error("Une erreur est survenue lors de l'ajout du frais.");
      }
    },    
    update() {
      this.$inertia.visit(route('article.edit', this.article.id));
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
    deleteArticle() {
      if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        // Si l'utilisateur confirme, envoyer la requête DELETE avec Inertia
        this.$inertia.delete(route('article.destroy', this.article.id));
      }
    },
    deleteFrais(fraisId) {
      if (confirm('Êtes-vous sûr de vouloir supprimer ce frais ?')) {
        // Si l'utilisateur confirme, envoyer la requête DELETE avec Inertia
        this.$inertia.delete(route('frais.destroy', fraisId))    
      }
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