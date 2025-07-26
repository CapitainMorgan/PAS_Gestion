<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import InputLabel from '@/components/InputLabel.vue';
import { Head } from '@inertiajs/vue3';
import vSelect from 'vue-select';
</script>


<template>
  <AuthenticatedLayout>
    <Head title="Articles" />
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Modifier l'Article
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">

                    <h1>Modifier l'article</h1>

                    <div v-if="form.errors" class="error">
                        <ul>
                            <li v-for="(error, key) in form.errors" :key="key">
                            {{ error }}
                            </li>
                        </ul>
                    </div>

                    <form @submit.prevent="updateArticle" class="form-grid">
                    <!-- Description -->
                    <div class="form-group full-width">
                        <InputLabel for="description" class="form-label">Description</InputLabel>
                        <TextInput v-model="form.description" type="text" class="form-control" placeholder="Description" required />
                    </div>

                    <!-- Taille -->
                    <div class="form-group">
                        <InputLabel for="taille" class="form-label">Taille</InputLabel>
                        <TextInput v-model="form.taille" type="text" class="form-control" placeholder="Taille" />
                    </div>

                    <!-- Prix de Vente -->
                    <div class="form-group">
                        <InputLabel for="prixVente" class="form-label">Prix de Vente</InputLabel>
                        <TextInput v-model="form.prixVente" type="number" class="form-control" placeholder="Prix de Vente" />
                    </div>

                    <!-- Prix Client -->
                    <div class="form-group">
                        <InputLabel for="prixClient" class="form-label">Prix Client</InputLabel>
                        <TextInput v-model="form.prixClient" type="number" class="form-control" placeholder="Prix Client" />
                    </div>

                    <!-- Prix Solde -->
                    <div class="form-group">
                        <InputLabel for="prixSolde" class="form-label">Prix Solde</InputLabel>
                        <TextInput v-model="form.prixSolde" type="number" class="form-control" placeholder="Prix Solde" />
                    </div>

                    <div class="form-group">
                        <InputLabel for="quantite" class="form-label">Quantité</InputLabel>
                        <TextInput v-model="form.quantite" type="number" class="form-control" placeholder="Quantité" />
                    </div>

                    <div class="form-group">
                        <InputLabel for="localisation" class="form-label">Localisation</InputLabel>
                        <TextInput v-model="form.localisation" type="text" class="form-control" placeholder="Localisation" />
                    </div>

                    <div class="form-group">
                        <InputLabel for="Date de vente/rendu" class="form-label">Date de vente/rendu</InputLabel>
                        <TextInput v-model="form.dateStatus" type="date" class="form-control" placeholder="Localisation" />
                    </div>

                    <!-- Color -->
                    <div class="form-group">
                        <InputLabel for="color" class="form-label">Couleur</InputLabel>
                        <select v-model="form.color" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option style="color: #000000;" value="#000000">Noir</option>
                            <option style="color: #ff0000;" value="#ff0000">Rouge</option>
                            <option style="color: #00ff2a;" value="#00ff2a">Vert</option>
                            <option style="color: #0027ff;" value="#0027ff">Bleu</option>
                        </select>                 
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <InputLabel for="status" class="form-label">Status</InputLabel>
                        <select v-model="form.status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="En Stock">En Stock</option>
                            <option value="Vendu">Vendu</option>
                            <option value="Rendu">Rendu</option>
                            <option value="Rendu défectueux">Rendu défectueux</option>
                            <option value="Donné">Donné</option>
                        </select>                 
                    </div>    

                    <!-- Vente Status -->
                    <div class="form-group">
                        <InputLabel for="vente_status" class="form-label">Vente Status</InputLabel>
                        <select v-model="form.vente_status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Cash">Cash</option>
                            <option value="CB">Carte bancaire</option>
                            <option value="Cash+CB">Cash + Carte bancaire</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                      <SecondaryButton type="submit" class="btn btn-primary">Modifier l'article</SecondaryButton>
                    </div>
                    </form>


                </div>

            </div>
        </div>
        </div>
    </AuthenticatedLayout>
</template>


<script>
import { useForm } from '@inertiajs/inertia-vue3';

export default {
  props: {
    article: Object,
  },
  data() {
    return {
      form: useForm({
        description: this.article.description,
        taille: this.article.taille,
        prixVente: this.article.prixVente,
        prixClient: this.article.prixClient,
        prixSolde: this.article.prixSolde,
        quantite: this.article.quantite,
        localisation: this.article.localisation,
        status: this.article.status,
        dateStatus: this.article.dateStatus,
        vente_status: this.article.vente_status,
        color: this.article.color,
      }),
    };
  },
  methods: {  
    updateArticle: function() {
        console.log(this.form);
      this.form.put(route('article.update', this.article.id), {
        onSuccess: () => {
          form.reset();
        },
      });
    }
  },
};
</script>