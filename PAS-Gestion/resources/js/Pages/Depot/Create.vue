<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
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
              >
                  Fournisseur {{ fournisseur_id }}
              </h2>
          </template>
          <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
              <div
                      class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                  >
                  <div class="p-6 text-gray-900">
  
                        <h3>Article</h3>
                        <form @submit.prevent="addArticle" class="form-grid">
                            <div class="form-group full-width">
                            <InputLabel for="description" class="form-label">Description</InputLabel>
                            <TextInput v-model="newArticle.description" type="text" class="form-control" placeholder="Description" required />
                            </div>

                            <!-- Taille -->
                            <div class="form-group">
                                <InputLabel for="taille" class="form-label">Taille</InputLabel>
                                <TextInput v-model="newArticle.taille" type="text" class="form-control" placeholder="Taille" />
                            </div>

                            <!-- Prix de Vente -->
                            <div class="form-group">
                                <InputLabel for="prixVente" class="form-label">Prix de Vente</InputLabel>
                                <TextInput v-model="newArticle.prixVente" type="number" class="form-control" placeholder="Prix de Vente" @input="newArticle.prixClient = newArticle.prixVente/2"/>
                            </div>

                            <!-- Prix Client -->
                            <div class="form-group">
                                <InputLabel for="prixClient" class="form-label">Prix Client</InputLabel>
                                <TextInput v-model="newArticle.prixClient" type="number" class="form-control" placeholder="Prix Client" />
                            </div>

                            <!-- Prix Solde -->
                            <div class="form-group">
                                <InputLabel for="prixSolde" class="form-label">Prix Solde</InputLabel>
                                <TextInput v-model="newArticle.prixSolde" type="number" class="form-control" placeholder="Prix Solde" />
                            </div>

                            <div class="form-group">
                                <InputLabel for="quantite" class="form-label">Quantité</InputLabel>
                                <TextInput v-model="newArticle.quantite" type="number" class="form-control" placeholder="Quantité" />
                            </div>

                            <div class="form-group">
                                <InputLabel for="localisation" class="form-label">Localisation</InputLabel>
                                <TextInput v-model="newArticle.localisation" type="text" class="form-control" placeholder="Localisation" />
                            </div>      
                            

                            <div class="form-group full-width">
                            <PrimaryButton type="submit" class="btn btn-primary">Ajouter un autre article</PrimaryButton>
                            </div>
                        </form>

                        
                        <div class="form-group" style="margin-top: 20px;margin-bottom: 20px;"> 
                          <InputLabel for="EcheanceDays" class="form-label">Nombre de jours avant la fin du dépôt</InputLabel>
                          <TextInput v-model="EcheanceDays" type="number" class="form-control" placeholder="Date d'échéance" />
                        </div>

                        <h2 v-if="articles.length > 0" >Articles Ajoutés</h2>
                        <table v-if="articles.length > 0">
                          <thead>
                            <tr>
                              <th>Description</th>
                              <th>Taille</th>
                              <th>Prix de Vente</th>
                              <th>Prix Client</th>
                              <th>Prix Solde</th>
                              <th>Quantité</th>
                              <th>Localisation</th>
                              <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(article, index) in articles" :key="index">
                              <td>{{ article.description }}</td>
                              <td>{{ article.taille }}</td>
                              <td>{{ article.prixVente }}</td>
                              <td>{{ article.prixClient }}</td>
                              <td>{{ article.prixSolde }}</td>
                              <td>{{ article.quantite }}</td>
                              <td>{{ article.localisation }}</td>                              
                              <td>
                                <DangerButton @click="removeArticle(index)">Supprimer</DangerButton>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <div class="form-group full-width">
                          <PrimaryButton class="btn btn-primary" @click="submitArticles">Créer le dépot</PrimaryButton>
                        </div>
                </div>
            </div>
          </div>
        </div>
    </AuthenticatedLayout>
</template>
  
  <script>
  
  export default {
    name: 'CreateMultipleArticles',
    props: {
      fournisseur_id: {
        type: String,
        required: true,
      },
    },
    data() {
    return {
      newArticle: {
        description: '',
        taille: null,
        prixVente: '',
        prixClient: null,
        prixSolde: null,
        quantite: null,
        localisation: '',
        fournisseur_id: this.fournisseur_id
      },
      EcheanceDays: 30,
      articles: [],
    };
  },
  methods: {  
      addArticle () {      

        this.articles.push({ ...this.newArticle });
        console.log(this.articles);
        // Réinitialisation du formulaire
        this.newArticle = { 
          description: '',
          taille: null,
          prixVente: '',
          prixClient: null,
          prixSolde: null,
          quantite: null,
          localisation: '',
          fournisseur_id: this.fournisseur_id,
       };
      },
  
      removeArticle (index) {
        this.articles.splice(index, 1);
      },
  
      async submitArticles() {
        // Utiliser une requête pour envoyer les données au backend
        axios.post(route('depot.store', this.EcheanceDays), { articles: this.articles })
        .then(response => {
          if (response.data.success) {
            const barcodeUrls = response.data.barcodeUrls;

            // Ouvrir chaque URL dans un nouvel onglet
            /*barcodeUrls.forEach((url, index) => {
              setTimeout(() => {
                window.open('/generate-barcode/' + url, '_blank');
              }, index * 300); 
            });*/

            // Rediriger vers la page des fournisseurs details
            this.$inertia.visit(route('fournisseur.show', this.fournisseur_id));
          }
        })
        .catch(error => {
          console.error('Erreur lors de la création des articles', error);
          this.$toast.error('Erreur lors de la création des articles.');
        });
      },
    },
  };
  </script>
  
<style scoped>
.btn {
  margin-top: 10px;
}
</style>