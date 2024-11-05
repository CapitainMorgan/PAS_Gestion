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
    <Head title="Frais Sociétés" />
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Frais Sociétés {{ frais.id }}
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">

                    <form @submit.prevent="createFrais" class="form-grid">
                    <div class="form-group">
                        <InputLabel for="description" class="form-label">Description</InputLabel>
                        <TextInput
                        type="text"
                        class="form-control"
                        id="nom"
                        v-model="form.description"
                        required
                        />
                    </div>
                    
                    <div class="form-group">
                        <InputLabel for="prenom" class="form-label">Prix</InputLabel>
                        <TextInput
                        type="number"                        
                        step="0.01"
                        class="form-control"
                        id="Prix"
                        v-model="form.prix"
                        required
                        />
                    </div>
                    
                    <div class="form-group full-width">
                      <SecondaryButton type="submit" class="btn btn-primary">Modifier le frais</SecondaryButton>
                    </div>
                    <div class="form-group full-width">
                      <PrimaryButton class="btn btn-primary" @click="indexFrais()">Retour à la liste des frais</PrimaryButton>
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
    frais: Object,
  },
  data() {
    return {
      form: useForm({
        description: this.frais.description || null,
        prix: this.frais.prix || null,
      }),
    };
  },
  methods: {
    async createFrais() {
      this.form.put(route('frais.update', this.frais.id), {
        onSuccess: () => {
          form.reset();
        },
      });
    },
    indexFrais() {
      this.$inertia.visit(route('frais.index'));
    },
  },
};
</script>

<style>
@import "vue-select/dist/vue-select.css";
</style>