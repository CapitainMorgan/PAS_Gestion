<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
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
                  Fournisseur {{ fournisseur_id }}
              </h2>
          </template>
          <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
              <div
                      class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                  >
                  <div class="p-6 text-gray-900">
  
                        <form @submit.prevent="addArticle">
                            <h3>Article</h3>
                            <div class="mb-3">

                            <InputLabel for="description" class="form-label">Description</InputLabel>
                            <TextInput v-model="newArticle.description" type="text" class="form-control" placeholder="Description" required />
                            </div>

                            <!-- Taille -->
                            <div class="mb-3">
                                <InputLabel for="taille" class="form-label">Taille</InputLabel>
                                <TextInput v-model="newArticle.taille" type="text" class="form-control" placeholder="Taille" />
                            </div>

                            <!-- Prix de Vente -->
                            <div class="mb-3">
                                <InputLabel for="prixVente" class="form-label">Prix de Vente</InputLabel>
                                <TextInput v-model="newArticle.prixVente" type="number" class="form-control" placeholder="Prix de Vente" />
                            </div>

                            <!-- Prix Client -->
                            <div class="mb-3">
                                <InputLabel for="prixClient" class="form-label">Prix Client</InputLabel>
                                <TextInput v-model="newArticle.prixClient" type="number" class="form-control" placeholder="Prix Client" />
                            </div>

                            <!-- Prix Solde -->
                            <div class="mb-3">
                                <InputLabel for="prixSolde" class="form-label">Prix Solde</InputLabel>
                                <TextInput v-model="newArticle.prixSolde" type="number" class="form-control" placeholder="Prix Solde" />
                            </div>

                            <div class="mb-3">
                                <InputLabel for="quantite" class="form-label">Quantité</InputLabel>
                                <TextInput v-model="newArticle.quantite" type="number" class="form-control" placeholder="Quantité" />
                            </div>

                            <div class="mb-3">
                                <InputLabel for="localisation" class="form-label">Localisation</InputLabel>
                                <TextInput v-model="newArticle.localisation" type="text" class="form-control" placeholder="Localisation" />
                            </div>                            
                    
                            <SecondaryButton type="submit">Ajouter un autre article</SecondaryButton>
                        </form>

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

                        
                        <PrimaryButton @click="submitArticles">Créer le dépot</PrimaryButton>
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
        type: Object,
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
        // Par exemple, avec Inertia ou une requête Axios pour l'API
        try {
          await this.$inertia.post(route('depot.store'), { articles: this.articles });
          this.$toast.success('Articles créé avec succès');
        } catch (error) {
          console.error(error);
          this.$toast.error('Une erreur est survenue lors de la création des articles');
        }
      }
    },
  };
  </script>
  
  <style>
  /* Ajoutez ici votre style */
  .article-form {
    margin-bottom: 20px;
  }
  </style>
  