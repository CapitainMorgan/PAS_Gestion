<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import Modal from '@/components/Modal.vue';
import DangerButton from '@/components/DangerButton.vue';
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

                        <div v-if="userIsAdmin" class="table-users">
                        <h2>Liste des utilisateurs</h2>
                          <table class="table">
                              <thead>
                              <tr>
                                  <th>Nom</th>
                                  <th>Email</th>
                                  <th>Email de recuperation</th>
                                  <th>Role</th>
                                  <th>Actions</th>
                              </tr>
                              </thead>
                              <tbody>
                              <tr v-for="user in users" :key="user.id">
                                  <td @click="showModalUpdateUser = true; this.user = user">{{ user.name }}</td>
                                  <td @click="showModalUpdateUser = true; this.user = user">{{ user.email }}</td>
                                  <td @click="showModalUpdateUser = true; this.user = user">{{ user.recovery_email }}</td>
                                  <td @click="showModalUpdateUser = true; this.user = user">{{ user.role }}</td>
                                  <td>
                                    <PrimaryButton @click="showModalUpdatePassword = true; this.user = user" class="btn btn-secondary" style="margin-right: 10px;">Modifier le mot de passe</PrimaryButton>
                                    <DangerButton @click="DeleteUser(user.id)" class="btn btn-danger">Supprimer</DangerButton>
                                  </td>
                              </tr>
                              </tbody>
                          </table>
                        </div>

                        <Modal v-show="showModalUpdatePassword" @close="closeModal">
                          <template v-slot:header>
                            Modifier le mot de passe <br>  
                            de {{ user.name }}
                          </template> 

                          <template v-slot:body>
                            <form @submit.prevent="updatePassword()">
                              <!-- Choix du depot_id -->
                              <InputLabel for="userpassword" class="label">Mot de passe</InputLabel>                          
                              <TextInput style="width:100%" type="password" v-model="userpassword" required />
                              <InputLabel for="repeteduserpassword" class="label">Repeter le mot de passe</InputLabel>
                              <TextInput style="width:100%" type="password" step=".01" v-model="repeteduserpassword" required />                                                   
                            </form>
                          </template>

                          <template v-slot:footer>
                            <PrimaryButton type="submit" class="btn btn-primary" @click="updatePassword">Modifier</PrimaryButton>
                          </template>
                        </Modal>

                        <Modal v-show="showModalUpdateUser" @close="closeModal">
                          <template v-slot:header>
                            Modifier l'utilisateur <br>  
                            {{ user.name }}
                          </template> 

                          <template v-slot:body>
                            <form @submit.prevent="updateUser()">
                              <!-- Choix du depot_id -->
                              <InputLabel for="name" class="label">Nom de l'utilisateur</InputLabel>
                              <TextInput style="width:100%" type="text" v-model="user.name" required />
                              <InputLabel for="email" class="label">Email de l'utilisateur</InputLabel>
                              <TextInput style="width:100%" type="email" v-model="user.email" required />
                              <InputLabel for="recovery_email" class="label">Email de recuperation</InputLabel>
                              <TextInput style="width:100%" type="email" v-model="user.recovery_email" required />
                              <InputLabel for="role" class="label">Role de l'utilisateur</InputLabel>
                              <v-select v-model="user.role" :options="['admin', 'user']" required />                                               
                            </form>
                          </template>

                          <template v-slot:footer>
                            <PrimaryButton type="submit" class="btn btn-primary" @click="updateUser">Modifier</PrimaryButton>
                          </template>
                        </Modal>

                    </div>


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
    settings: {
      type: Object,
      required: true,
    },
    users: {
      type: Array,
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
        user: {
          id: '',
          name: '',
          email: '',
          recovery_email: '',
          role: '',
        },
        showModalUpdatePassword: false,
        showModalUpdateUser: false,
        userpassword: '',
        repeteduserpassword: '',
    }
  },
  mounted() {
    // Vérifier si l'utilisateur est un administrateur en laravel vuejs
    this.userIsAdmin = this.$page.props.auth.user.role  === 'admin';
  },
  methods: {
    updateSettings() {
      this.form.put(route('parametre.update'), {
        onSuccess: () => {
            this.form.reset();  // Reset du formulaire après succès de la mise à jour
        },
      });
    },
    DeleteUser(id) {
      // Supprimer un utilisateur
      if (confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) {
        this.$inertia.delete(route('user.destroy', id));
      }
    },
    closeModal() {
      this.showModalUpdatePassword = false;
      this.showModalUpdateUser = false;
    },
    updateUser() {
      // Mettre à jour un utilisateur
      this.$inertia.put(route('user.update', this.user.id), this.user);

      this.closeModal();
    },
    updatePassword() {
      if (this.userpassword != this.repeteduserpassword) {
        alert('Les mots de passe ne correspondent pas');
        this.userpassword = '';
        this.repeteduserpassword = '';
        return;
      }

      // Mettre à jour le mot de passe d'un utilisateur
      this.$inertia.put(route('user.update-password', this.user.id), this.userpassword);

      this.userpassword = '';
      this.repeteduserpassword = '';

      this.closeModal();
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