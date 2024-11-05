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
                Frais Sociétés
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">
                    
                    <div>
                        <h1>Paramètres de l'application</h1>

                        <!-- Formulaire de mise à jour des paramètres -->
                        <form @submit.prevent="updateSettings" class="form-grid">
                        <div class="form-group">
                          <InputLabel for="tva">TVA :</InputLabel>
                          <TextInput v-model="form.tva" id="tva" type="number" min="0" max="100" step="0.01" class="form-control" required />
                        </div>
                        <div class="form-group">
                          <InputLabel for="newsletterEmail">Email de configuration de la Newsletter :</InputLabel>
                          <TextInput v-model="form.newsletterEmail" id="newsletterEmail" type="email" class="form-control" required />
                        </div>
                        <div class="form-group full-width">
                          <InputLabel for="conditionsGenerales">Conditions Générales :</InputLabel>
                          <textarea v-model="form.conditionsGenerales" id="conditionsGenerales" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4" required></textarea>
                        </div>

                        <!-- Bouton de mise à jour -->
                        <div class="form-group full-width">
                          <SecondaryButton type="submit" class="btn btn-primary">Mettre à jour</SecondaryButton>
                        </div>
                        </form>

                        <!-- Bouton visible uniquement pour les administrateurs -->
                        <div v-if="userIsAdmin" class="admin-section">
                        <PrimaryButton @click="goToUserManagement" class="btn btn-secondary">
                            Créer un nouvel utilisateur
                        </PrimaryButton>
                        </div>
                    </div>


                </div>
            </div>
          </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { useForm, usePage } from '@inertiajs/inertia-vue3';

export default {
  props: {
    settings: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
        form : useForm({
            tva: this.settings.tva,
            conditionsGenerales: this.settings.conditionsGenerales,
            newsletterEmail: this.settings.newsletterEmail,
        }),
        userIsAdmin : false,
    }
  },
  mounted() {
    // Vérifier si l'utilisateur est un administrateur
    this.userIsAdmin = true;
  },
  methods: {
    updateSettings() {
      this.form.put(route('parametre.update'), {
        onSuccess: () => {
            this.form.reset();  // Reset du formulaire après succès de la mise à jour
        },
      });
    },

    goToUserManagement() {
      // Rediriger vers la gestion des utilisateurs
      window.location.href = route('newUser');
    }
  },
}
</script>


<style>
@import "vue-select/dist/vue-select.css";

.btn-secondary {
  margin-top: 10px;
}
</style>