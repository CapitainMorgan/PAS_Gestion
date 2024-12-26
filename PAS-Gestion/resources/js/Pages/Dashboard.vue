<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import InputLabel from '@/components/InputLabel.vue';
import TextInput from '@/components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
import { start } from '@popperjs/core';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                      <div v-if="articles.length > 0">
                        <!--Table des articles en fin d'echeance-->
                        <h2 class="text-lg font-semibold text-gray-800">
                            Table des articles en fin d'echeance
                        </h2>

                        <table>
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Description</th>
                                    <th class="px-4 py-2">Localisation</th>
                                    <th class="px-4 py-2">Date d'expiration</th>
                                    <th class="px-4 py-2">Fournisseur</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="article in paginatedArticles" :key="article.id">        
                                    <td @click="showArticle(article.id)">{{ article.description }}</td>
                                    <td @click="showArticle(article.id)">{{ article.localisation }}</td>
                                    <td @click="showArticle(article.id)">{{ article.dateEcheance }}</td>
                                    <td @click="showArticle(article.id)">{{ article.fournisseur.prenom + " " + article.fournisseur.nom }}</td>
                                    <td>
                                        <SecondaryButton @click="sendMail(article)">Envoyer le mail de rappel</SecondaryButton>
                                    </td>
                                </tr>
                            </tbody>                            
                        </table>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                                <ul class="pagination">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <SecondaryButton class="page-link" @click="changePage(currentPage - 1)">Précédent</SecondaryButton>
                                </li>
                                <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: currentPage === page }">
                                    <SecondaryButton class="page-link" @click="changePage(page)">{{ page }}</SecondaryButton>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <SecondaryButton class="page-link" @click="changePage(currentPage + 1)">Suivant</SecondaryButton>
                                </li>
                                </ul>
                            </nav>

                      </div>
                      <div v-else>
                        <p>Aucun article en fin d'échéance</p>
                      </div>  
                    </div>
                    <form @submit.prevent="excelExport" class="form-grid" style="padding: 10px;">
                      <div class="form-group">
                        <InputLabel for="start_date">Date de début :</InputLabel>
                        <TextInput type="date" name="start_date" v-model="start_date" required/>
                      </div>
                      <div class="form-group">
                        <InputLabel for="end_date">Date de fin :</InputLabel>
                        <TextInput type="date" name="end_date" v-model="end_date" required/>
                      </div>
                    </form>                    
                    <PrimaryButton style="margin: 10px;" @click="excelExport">Exporter en Excel</PrimaryButton>
                    
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<script>


  export default {
    props: {
      articles: {
        type: Array,
        default: () => [], // Assurez-vous que c'est un tableau par défaut
      },
    },
    data() {
      return {
        searchTerm: '',
        currentPage: 1,
        pageSize: 10,
        start_date: '',
        end_date: '',
      };
    },
    computed: {
      totalPages() {
        return Math.ceil(this.articles.length / this.pageSize);
      },
      paginatedArticles() {
        const start = (this.currentPage - 1) * this.pageSize;
        return this.articles.slice(start, start + this.pageSize);
      },
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
      changePage(page) {
        if (page < 1 || page > this.totalPages) return; // Limiter la page
        this.currentPage = page;
      },
      sendMail(article) {
        // verifier si le fournisseur a d'autres articles dans la liste
        let fournisseur = article.fournisseur;
        let articles = this.articles.filter((a) => a.fournisseur.id === fournisseur.id);
        if (articles.length > 1) {
          if(confirm('D\'autres articles de se fournissuer ont été trouvé en fin d\'écheance. Voulez-vous envoyer un mail à ' + fournisseur.prenom + ' ' + fournisseur.nom + ' avec tous les articles ?'))
          {
            // Envoyer un mail avec tout les articles
            this.$inertia.post('/send-reminder/articles', {
              article_ids: articles.map((a) => a.id), 
            }).then(() => {
              this.$toast.success('Email de rappel envoyé avec succès');
            }).catch((error) => {
              console.error(error);
              this.$toast.error('Erreur lors de l\'envoi du rappel');
            });
          }
          else if (confirm('Voulez-vous envoyer un mail à ' + fournisseur.prenom + ' ' + fournisseur.nom + ' avec cet article uniquement ?'))
          {
            // Envoyer un mail avec l'article
            this.$inertia.post('/send-reminder/articles', {
              article_ids: [article.id], 
            }).then(() => {
              this.$toast.success('Email de rappel envoyé avec succès');
            }).catch((error) => {
              console.error(error);
              this.$toast.error('Erreur lors de l\'envoi du rappel');
            });
          }
          
        } else {
          if(confirm('Voulez-vous envoyer un mail à ' + fournisseur.prenom + ' ' + fournisseur.nom + ' ?'))
          {
            // Envoyer un mail avec l'article
            this.$inertia.post('/send-reminder/articles', {
              article_ids: [article.id], 
            }).then(() => {
              this.$toast.success('Email de rappel envoyé avec succès');
            }).catch((error) => {
              console.error(error);
              this.$toast.error('Erreur lors de l\'envoi du rappel');
            });
          }
        }
      },
      showArticle(id) {
        this.$inertia.visit(route('article.show', id));
      },
      excelExport() {
        if(this.start_date == '' || this.end_date == '') {
          alert('Veuillez renseigner les dates de début et de fin');
          return;
        }

        const url = route('export.articles', {
          start_date: this.start_date,
          end_date: this.end_date,
        });

        // Ouvre la route dans une nouvelle fenêtre
        window.open(url, '_blank');
      }
    },
};

</script>