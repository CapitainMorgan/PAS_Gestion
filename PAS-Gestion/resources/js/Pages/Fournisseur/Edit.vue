<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import InputLabel from '@/components/InputLabel.vue';
import { Head } from '@inertiajs/vue3';
</script>


<template>
  <AuthenticatedLayout>
    <Head title="Fournisseurs" />
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Modifier le fournisseur
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">

                    <h1>Modifier le fournisseur</h1>

                    <div v-if="form.errors" class="error">
                        <ul>
                            <li v-for="(error, key) in form.errors" :key="key">
                            {{ error }}
                            </li>
                        </ul>
                    </div>

                <form @submit.prevent="updateFournisseur" class="form-grid">
                    <!-- Nom -->
                    <div class="form-group">
                        <InputLabel for="nom" class="form-label">Nom</InputLabel>
                        <TextInput v-model="form.nom" type="text" class="form-control" placeholder="Nom" required />
                    </div>
                    <div class="form-group">
                        <InputLabel for="prenom" class="form-label">Prénom</InputLabel>
                        <TextInput v-model="form.prenom" type="text" class="form-control" placeholder="Prénom" required />
                    </div>
                    <div class="form-group">
                        <InputLabel for="email" class="form-label">Email</InputLabel>
                        <TextInput v-model="form.email" type="email" class="form-control" placeholder="Email"  />
                    </div>
                    <div class="form-group">
                        <InputLabel for="mobile" class="form-label">Mobile</InputLabel>
                        <TextInput v-model="form.mobile" type="text" class="form-control" placeholder="Mobile"  />
                    </div>
                    <div class="form-group">
                        <InputLabel for="telephone" class="form-label">Téléphone</InputLabel>
                        <TextInput v-model="form.telephone" type="text" class="form-control" placeholder="Téléphone"  />
                    </div>
                    <div class="form-group">
                        <InputLabel for="numProf" class="form-label">Numéro Professionnel</InputLabel>
                        <TextInput v-model="form.numProf" type="text" class="form-control" placeholder="Numéro Professionnel"  />
                    </div>
                    <div class="form-group">
                        <InputLabel for="rue" class="form-label">Rue</InputLabel>
                        <TextInput v-model="form.rue" type="text" class="form-control" placeholder="Rue"  />
                    </div>
                    <div class="form-group">
                        <InputLabel for="ville" class="form-label">Ville</InputLabel>
                        <TextInput v-model="form.ville" type="text" class="form-control" placeholder="Ville"  />
                    </div>
                    <div class="form-group">
                        <InputLabel for="npa" class="form-label">NPA</InputLabel>
                        <TextInput v-model="form.npa" type="text" class="form-control" placeholder="NPA"  />
                    </div>
                    <div class="form-group">
                        <InputLabel for="pays" class="form-label">Pays</InputLabel>
                        <TextInput v-model="form.pays" type="text" class="form-control" placeholder="Pays"  />
                    </div>
                    <div class="form-group full-width">
                        <InputLabel for="remarque" class="form-label">Remarque</InputLabel>
                        <TextInput v-model="form.remarque" type="text" class="form-control" placeholder="Remarque"  />
                    </div>                  
                    <div class="form-group full-width">
                      <SecondaryButton type="submit" class="btn btn-primary">Modifier le fournisseur</SecondaryButton>
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
    fournisseur: Object,
  },
  data() {
    return {
      form: useForm({
        nom: this.fournisseur.nom,
        prenom: this.fournisseur.prenom,
        email: this.fournisseur.email,
        mobile: this.fournisseur.mobile,
        telephone: this.fournisseur.telephone,
        numProf: this.fournisseur.numProf,
        rue: this.fournisseur.rue,
        ville: this.fournisseur.ville,
        npa: this.fournisseur.npa,
        pays: this.fournisseur.pays,
        remarque: this.fournisseur.remarque,
      }),
    };
  },
  methods: {  
    updateFournisseur: function() {
      this.form.put(route('fournisseur.update', this.fournisseur.id), {
        onSuccess: () => {
          form.reset();
        },
      });
    }
  },
};
</script>