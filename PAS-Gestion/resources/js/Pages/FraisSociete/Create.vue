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
    <Head title="Frais Sociétés" />
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Frais Sociétés
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">

                    <form @submit.prevent="createFrais">
                    <div class="mb-3">
                        <InputLabel for="description" class="form-label">Description</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="nom"
                        v-model="form.description"
                        required
                        />
                    </div>
                    
                    <div class="mb-3">
                        <InputLabel for="prenom" class="form-label">Prix</InputLabel>
                        <TextInput
                        type="number"
                        class="form-control"
                        id="Prix"
                        v-model="form.prix"
                        required
                        />
                    </div>
                    
                    <SecondaryButton type="submit" class="btn btn-primary">Créer le frais</SecondaryButton>
                    </form>

                    <PrimaryButton><a :href="route('frais.index')">Retour à la liste des frais</a></PrimaryButton>


                </div>
            </div>
          </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
  props: {
    frais: Array,
  },
  data() {
    return {
      form: {
        description: '',
        prix: null,
      },
    };
  },
  computed: {
  },
  watch: {
  },
  methods: {
    async createFrais() {
      try {
        await this.$inertia.post(route('frais.store'), this.form);
        this.$toast.success('Frais créé avec succès');
      } catch (error) {
        console.error(error);
        this.$toast.error('Une erreur est survenue lors de la création du frais');
      }
    },
  },
};
</script>

<style>
@import "vue-select/dist/vue-select.css";
</style>