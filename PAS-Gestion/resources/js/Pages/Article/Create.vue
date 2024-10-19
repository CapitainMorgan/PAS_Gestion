<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
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
                Articles
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">

                    <form @submit.prevent="createArticle">
                    <div class="mb-3">
                        <InputLabel for="description" class="form-label">Description</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="description"
                        v-model="form.description"
                        required
                        />
                    </div>
                    
                    <div class="mb-3">
                        <InputLabel for="taille" class="form-label">Taille</InputLabel>
                        <TextInput
                        type="number"
                        class="form-control"
                        id="taille"
                        v-model="form.taille"
                        placeholder="Entrez la taille (facultatif)"
                        />
                    </div>
                    
                    <div class="mb-3">
                        <InputLabel for="prixVente" class="form-label">Prix Vente</InputLabel>
                        <TextInput
                        type="number"
                        class="form-control"
                        id="prixVente"
                        v-model="form.prixVente"
                        required
                        step="0.01"
                        />
                    </div>
                    
                    <div class="mb-3">
                        <InputLabel for="prixClient" class="form-label">Prix Client</InputLabel>
                        <TextInput
                        type="number"
                        class="form-control"
                        id="prixClient"
                        v-model="form.prixClient"
                        step="0.01"
                        placeholder="Prix Client (facultatif)"
                        />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="prixSolde" class="form-label">Prix Solde</InputLabel>
                        <TextInput
                        type="number"
                        class="form-control"
                        id="prixSolde"
                        v-model="form.prixSolde"
                        step="0.01"
                        placeholder="Prix Solde (facultatif)"
                        />
                    </div>

                    <div class="mb-3">
                        <label for="fournisseur" class="form-label">Fournisseur</label>
                        <v-select
                        :options="fournisseurs"
                        v-model="selectedFournisseur"
                        :reduce="fournisseur => fournisseur.id"
                        label="nom"
                        placeholder="Sélectionner un fournisseur"
                        search-placeholder="Rechercher un fournisseur..."
                        />
                    </div>

                    <SecondaryButton type="submit" class="btn btn-primary">Créer l'Article</SecondaryButton>
                    </form>

                    <PrimaryButton><a :href="route('article.index')">Retour à la liste des Articles</a></PrimaryButton>


                </div>
            </div>
          </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
  props: {
    fournisseurs: Array,
  },
  data() {
    return {
      form: {
        description: '',
        taille: null,
        prixVente: '',
        prixClient: null,
        prixSolde: null,
        fournisseur_id: '',
      },
      selectedFournisseur: null,
    };
  },
  computed: {
    fullNameFournisseurs() {
      return this.fournisseurs.map(fournisseur => ({
        ...fournisseur,
        fullName: `${fournisseur.nom} ${fournisseur.prenom}`,
      }));
    }
  },
  watch: {
    selectedFournisseur(newVal) {
      this.form.fournisseur_id = newVal;
    }
  },
  methods: {
    async createArticle() {
      try {
        await this.$inertia.post(route('article.store'), this.form);
        this.$toast.success('Article créé avec succès');
      } catch (error) {
        console.error(error);
        this.$toast.error('Une erreur est survenue lors de la création de l\'article');
      }
    },
  },
};
</script>

<style>
@import "vue-select/dist/vue-select.css";
</style>