<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
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

                    <form @submit.prevent="updateArticle">
                    <!-- Description -->
                    <div class="mb-3">
                        <InputLabel for="description" class="form-label">Description</InputLabel>
                        <TextInput v-model="form.description" type="text" class="form-control" placeholder="Description" required />
                    </div>

                    <!-- Taille -->
                    <div class="mb-3">
                        <InputLabel for="taille" class="form-label">Taille</InputLabel>
                        <TextInput v-model="form.taille" type="text" class="form-control" placeholder="Taille" />
                    </div>

                    <!-- Prix de Vente -->
                    <div class="mb-3">
                        <InputLabel for="prixVente" class="form-label">Prix de Vente</InputLabel>
                        <TextInput v-model="form.prixVente" type="number" class="form-control" placeholder="Prix de Vente" />
                    </div>

                    <!-- Prix Client -->
                    <div class="mb-3">
                        <InputLabel for="prixClient" class="form-label">Prix Client</InputLabel>
                        <TextInput v-model="form.prixClient" type="number" class="form-control" placeholder="Prix Client" />
                    </div>

                    <!-- Prix Solde -->
                    <div class="mb-3">
                        <InputLabel for="prixSolde" class="form-label">Prix Solde</InputLabel>
                        <TextInput v-model="form.prixSolde" type="number" class="form-control" placeholder="Prix Solde" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="quantite" class="form-label">Quantité</InputLabel>
                        <TextInput v-model="form.quantite" type="number" class="form-control" placeholder="Quantité" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="localisation" class="form-label">Localisation</InputLabel>
                        <TextInput v-model="form.localisation" type="text" class="form-control" placeholder="Localisation" />
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <InputLabel for="status" class="form-label">Status</InputLabel>
                        <select v-model="form.status" class="form-select">
                            <option value="En Stock">En Stock</option>
                            <option value="Vendu">Vendu</option>
                            <option value="Rendu">Rendu</option>
                            <option value="Donné">Donné</option>
                        </select>                 
                    </div>    

                    <!-- Vente Status -->
                    <div class="mb-3">
                        <InputLabel for="vente_status" class="form-label">Vente Status</InputLabel>
                        <select v-model="form.vente_status" class="form-select">
                            <option value="Cash">Cash</option>
                            <option value="CB">Carte bancaire</option>
                        </select>
                    </div>

                    <SecondaryButton type="submit" class="btn btn-primary">Modifier l'article</SecondaryButton>
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
        vente_status: this.article.vente_status,
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