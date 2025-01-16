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
    <Head title="Fournisseurs" />
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Fournisseurs
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">

                    <form @submit.prevent="createFournisseur" class="form-grid">
                    <div class="form-group">
                        <InputLabel for="nom" class="form-label">Nom</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="nom"
                        v-model="form.nom"
                        required
                        />
                    </div>
                    
                    <div class="form-group">
                        <InputLabel for="prenom" class="form-label">Prénom</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="prenom"
                        v-model="form.prenom"
                        required
                        />
                    </div>

                    <div class="form-group">
                        <InputLabel for="email" class="form-label">Email</InputLabel>
                        <TextInput
                        type="email"
                        class="form-control"
                        id="email"
                        v-model="form.email"
                        placeholder="Entrez l'email"                        
                        />
                    </div>

                    <div class="form-group">
                        <InputLabel for="mobile" class="form-label">Mobile</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="mobile"
                        v-model="form.mobile"
                        placeholder="Entrez le mobile"
                        required
                        />                        
                    </div>

                    <div class="form-group">
                        <InputLabel for="telephone" class="form-label">Téléphone</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="telephone"
                        v-model="form.telephone"
                        placeholder="Entrez le téléphone"
                        
                        />
                    </div>

                    <div class="form-group">
                        <InputLabel for="numProf" class="form-label">Numéro Professionnel</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="numProf"
                        v-model="form.numProf"
                        placeholder="Entrez le numéro professionnel"
                        />
                    </div>

                    <div class="form-group">
                        <InputLabel for="rue" class="form-label">Rue</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="rue"
                        v-model="form.rue"
                        placeholder="Entrez la rue"
                        required
                        />
                    </div>


                    <div class="form-group">
                        <InputLabel for="ville" class="form-label">Ville</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="ville"
                        v-model="form.ville"
                        placeholder="Entrez la ville"
                        required
                        />
                    </div>

                    <div class="form-group">
                        <InputLabel for="npa" class="form-label">NPA</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="npa"
                        v-model="form.npa"
                        placeholder="Entrez le NPA"
                        required
                        />
                    </div>

                    <div class="form-group">
                        <InputLabel for="pays" class="form-label">Pays</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="pays"
                        v-model="form.pays"
                        placeholder="Entrez le pays"
                        required
                        />
                    </div>

                    <div class="form-group full-width">
                        <InputLabel for="remarque" class="form-label">Remarque</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="remarque"
                        v-model="form.remarque"
                        placeholder="Entrez la remarque (facultatif)"
                        />
                    </div>
                    <div class="form-group full-width">
                      <PrimaryButton type="submit" class="btn btn-primary">Créer le fournisseur</PrimaryButton>
                    </div>

                    <div class="form-group full-width">
                      <SecondaryButton class="btn btn-primary" @click="indexFournisseur()">Retour à la liste des fournisseurs</SecondaryButton>
                    </div>
                    </form>



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
        quantite: null,
        localisation: '',
      },
    };
  },
  computed: {
  },
  watch: {
  },
  methods: {
    async createFournisseur() {
      try {
        await this.$inertia.post(route('fournisseur.store'), this.form);
        this.$toast.success('Fournisseur créé avec succès');
      } catch (error) {
        console.error(error);
        this.$toast.error('Une erreur est survenue lors de la création du fournisseur');
      }
    },
    indexFournisseur() {
      this.$inertia.visit(route('fournisseur.index'));
    },
  },
};
</script>

<style>
@import "vue-select/dist/vue-select.css";
</style>