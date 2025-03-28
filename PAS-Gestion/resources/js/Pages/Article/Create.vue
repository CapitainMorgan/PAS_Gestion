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
                Articles
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">

                    <form @submit.prevent="createArticle" class="form-grid">
                    <div class="form-group full-width">
                        <InputLabel for="description" class="form-label">Description</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="description"
                        v-model="form.description"
                        required
                        />
                    </div>
                    
                    <div class="form-group">
                        <InputLabel for="taille" class="form-label">Taille</InputLabel>
                        <TextInput
                        type="number"
                        class="form-control"
                        id="taille"
                        v-model="form.taille"
                        placeholder="Entrez la taille (facultatif)"
                        />
                    </div>
                    
                    <div class="form-group">
                        <InputLabel for="prixVente" class="form-label">Prix Vente</InputLabel>
                        <TextInput
                        type="number"
                        class="form-control"
                        id="prixVente"
                        v-model="form.prixVente"
                        required
                        step="0.01"
                        @input="form.prixClient = form.prixVente/2"
                        />
                    </div>
                    
                    <div class="form-group">
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

                    <div class="form-group">
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

                    <div class="form-group">
                        <InputLabel for="quantite" class="form-label">Quantité</InputLabel>
                        <TextInput
                        type="number"
                        class="form-control"
                        id="quantite"
                        v-model="form.quantite"
                        placeholder="Quantité"
                        />
                    </div>

                    <div class="form-group">
                        <InputLabel for="localisation" class="form-label">Localisation</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="localisation"
                        v-model="form.localisation"
                        placeholder="Localisation"
                        />
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

                    <div class="form-group">
                        <InputLabel for="EchanceDays" class="form-label">Nombre de jours avant la fin du dépôt</InputLabel>
                        <TextInput
                        type="number"
                        class="form-control"
                        id="EchanceDays"
                        v-model="EchanceDays"
                        placeholder="Nombre de jours avant la fin du dépôt"
                        />

                    </div>

                    <div class="form-group full-width">
                        <InputLabel for="fournisseur" class="form-label">Fournisseur</InputLabel>
                        <v-select
                        :options="fullNameFournisseurs"
                        v-model="selectedFournisseur"
                        :reduce="fournisseur => fournisseur.id"
                        label="fullName"
                        placeholder="Sélectionner un fournisseur"
                        search-placeholder="Rechercher un fournisseur..."
                        required
                        />
                    </div>
                    <div class="form-group full-width">
                      <PrimaryButton type="submit" class="btn btn-primary">Créer l'Article</PrimaryButton>
                    </div>
                    <div class="form-group full-width">
                      <SecondaryButton class="btn btn-primary" @click="indexArticle()">Retour à la liste des Articles</SecondaryButton>
                    </div>
                    </form>
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
        quantite: null,
        localisation: '',
        fournisseur_id: '',
        color: '#000000',
      },
      EchanceDays: 30,
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
      if (!this.form.fournisseur_id) {
        alert('Veuillez sélectionner un fournisseur');
        return;
      }
      const toast = useToast();
      try {
        await this.$inertia.post(route('article.store', this.EchanceDays), this.form);
        toast.success('Article créé avec succès');
      } catch (error) {
        console.error(error);
        toast.error('Une erreur est survenue lors de la création de l\'article');
      }
    },
    indexArticle() {
      this.$inertia.visit(route('article.index'));
    }
  },
};
</script>

<style>
@import "vue-select/dist/vue-select.css";
</style>