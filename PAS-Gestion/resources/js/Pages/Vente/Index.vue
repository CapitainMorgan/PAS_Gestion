<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
import TextInput from '@/components/TextInput.vue';
import InputLabel from '@/components/InputLabel.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
</script>


<template>
  <AuthenticatedLayout>
    <Head title="Caisse" />
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Caisse
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">

                    <TextInput tyle="width:100%" type="date" v-model="date" @change="fetchVentes()"/>

                    <table class="table-auto">
                        <thead>
                            <tr>
                                <th>ID Article</th>
                                <th>Description</th>
                                <th>Fournisseur</th>
                                <th>Quantité</th>
                                <th>Prix Vente Unitaire</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="vente in ventes" :key="vente.id">
                                <td>{{ formatIdArticle(vente.article) }}</td>
                                <td @click="showArticle(vente.article.id)">{{ vente.article.description }}</td>
                                <td @click="showFournisseur(vente.article.fournisseur.id)">{{ vente.article.fournisseur.nom + " " + vente.article.fournisseur.prenom }}</td>
                                <td>{{ vente.quantite }}</td>
                                <td>{{ vente.prix_unitaire }}</td>
                                <td>{{ vente.prix_unitaire * vente.quantite }}</td>
                            </tr>
                        </tbody>
                    </table>

                     <!-- Pagination -->
                  <nav aria-label="Page navigation">
                    <ul class="pagination">
                      <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <SecondaryButton class="page-link" @click="fetchVentes(currentPage - 1)">Précédent</SecondaryButton>
                      </li>
                      <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: currentPage === page }">
                        <SecondaryButton class="page-link" @click="fetchVentes(page)">{{ page }}</SecondaryButton>
                      </li>
                      <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <SecondaryButton class="page-link" @click="fetchVentes(currentPage + 1)">Suivant</SecondaryButton>
                      </li>
                    </ul>
                  </nav>

                </div>
            </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
    props: {
    },
    data() {
        return {
            date: new Date().toISOString().split('T')[0],
            currentPage: 1,
            totalPages: 0,
            pageSize: 10,
            ventes: []
        }
    },
    mounted() {
        this.fetchVentes(1);
    },
    computed: {
      visiblePages() {
        const pages = [];
        const start = Math.max(1, this.currentPage - 1);
        const end = Math.min(this.totalPages, this.currentPage + 1);

        for (let i = start; i <= end; i++) {
          pages.push(i);
        }

        // Si la première page est affichée, on peut ajouter la page 2
        if (start > 1) {
          pages.unshift(start - 1); // Ajouter la page précédente si elle n'est pas affichée
        }

        // Si la dernière page est affichée, on peut ajouter la page juste avant la dernière
        if (end < this.totalPages) {
          pages.push(end + 1); // Ajouter la page suivante si elle n'est pas affichée
        }

        return pages;
      },
    },
    methods: {
      formatIdArticle(article) {
        const createdAt = new Date(article.created_at);

        // Format en "dmy" (jour-mois-année)
        const date = `${String(createdAt.getDate()).padStart(2, '0')}${String(createdAt.getMonth() + 1).padStart(2, '0')}${String(createdAt.getFullYear()).slice(2)}`;

        const id = article.id.toString();
        let fournisseurId = article.fournisseur_id.toString();

        if (
          id.startsWith(fournisseurId) && 
          id.endsWith(date)
        ) {
          return `${fournisseurId}-${id.slice(fournisseurId.length, -date.length)}-${date}`;
        }
        return id;
      },
        formatDate(date) {
            return new Date(date).toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        },
        fetchVentes(page = 1) {
            axios.get('/ventes/search', { params: { page, date: this.date } })
                .then(response => {
                    this.ventes = response.data.data;
                    this.currentPage = response.data.current_page;
                    this.totalPages = response.data.last_page;

                })
                .catch(error => {
                    console.log(error);
                });
        },
        showFournisseur(id){
            this.$inertia.visit(route('fournisseur.show', id));
        },
        showArticle(id){
            this.$inertia.visit(route('article.show', id));
        }
    }
};
</script>