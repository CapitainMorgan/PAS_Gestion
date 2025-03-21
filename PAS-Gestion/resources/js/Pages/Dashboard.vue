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
                      <div v-if="Object.keys(paginatedArticles).length > 0">
                        <div v-for="(group, fournisseurId) in paginatedArticles" :key="fournisseurId" class="mb-4">
                            <div class="bg-gray-200 p-4 cursor-pointer" style="border-radius: 5px; height: 100%;" @click="toggleDropdown(group.fournisseurId)">
                                <span :style="{color:  group.color }" @click="showFournisseur(group.fournisseurId)" class="font-bold">{{ group.fournisseurId }} {{ group.fournisseur }} <br v-if="group.remarque != '' & group.remarque != null"> {{ group.remarque }}</span>
                                <PrimaryButton style="float: right;" @click="sendAllMail(group.fournisseurId)">Envoyer tous les mails de rappel</PrimaryButton>
                                <PrimaryButton style="float: right;margin-right: 10px;" @click="sendSelectedMails(group.fournisseurId)">Envoyer les mails sélectionnés</PrimaryButton>
                            </div>

                            <div v-if="expanded[group.fournisseurId]" class="p-4 border">
                              <span style="margin-right: 10px;">Tout sélectionner</span>
                              <input type="checkbox" :checked="selectedArticles[group.fournisseurId].length === groupedArticles[group.fournisseurId].articles.length" @change="selectAllArticles(group.fournisseurId)" />
                              <PrimaryButton style="float: right;" @click="updateEcheanceOfSelectedArticles(group.fournisseurId)">
                                Mettre à jour les articles sélectionnés
                              </PrimaryButton>
                              <TextInput style="float: right;margin-right: 10px; margin-bottom: 10px;" type="date" v-model="dateEcheance" class="p-2 border rounded" />
                              

                              <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th class="border p-2">ID</th>
                                            <th class="border p-2">Article</th>
                                            <th class="border p-2">Échéance</th>
                                            <th class="border p-2">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="article in getPaginatedArticles(group.fournisseurId).paginatedItems" :key="article.id">
                                          <td>
                                            <input 
                                              type="checkbox" 
                                              v-model="selectedArticles[group.fournisseurId]" 
                                              :value="article.id"
                                            />
                                          </td>
                                            <td @click="showArticle(article.id)" class="border p-2">{{ formatIdArticle(article) }}</td>
                                            <td @click="showArticle(article.id)" class="border p-2">{{ article.description }}</td>
                                            <td class="border p-2">
                                                <TextInput type="date" v-model="article.dateEcheance" class="p-2 border rounded" />
                                            </td>
                                            <td class="border p-2">
                                                <PrimaryButton @click="updateEcheance(article )">
                                                    Mettre à jour
                                                </PrimaryButton>
                                                <PrimaryButton style="margin-left: 10px;" @click="sendMail(article)">
                                                    Envoyer un mail de rappel
                                                </PrimaryButton>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <nav aria-label="Page navigation">
                                  <ul class="pagination">
                                    <li class="page-item" :class="{ disabled: getPaginatedArticles(group.fournisseurId).currentPage === 1 }">
                                      <SecondaryButton class="page-link" @click="getPaginatedArticles(group.fournisseurId).prevPage">Précédent</SecondaryButton>
                                    </li>
                                    <li class="page-item" :class="{ disabled: getPaginatedArticles(group.fournisseurId).currentPage === getPaginatedArticles(group.fournisseurId).totalPages }">
                                      <SecondaryButton class="page-link" @click="getPaginatedArticles(group.fournisseurId).nextPage">Suivant</SecondaryButton>
                                    </li>
                                  </ul>
                                </nav>
                            </div>
                        </div>
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
                        Aucun article en fin d'échéance.
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
                    <PrimaryButton style="margin: 10px;" @click="excelFournisseur">Exporter les fournisseurs</PrimaryButton>
                    <PrimaryButton style="margin: 10px;" @click="excelArticle">Exporter les articles</PrimaryButton>     
                    <PrimaryButton style="margin: 10px;" @click="excelArticleTransit">Exporter les articles en transit</PrimaryButton>            
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<script>
  import { useToast } from 'vue-toastification'


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
        expanded: {},
        articlesPagination: {},
        selectedArticles: {}, 
        dateEcheance: '',       
      };
    },
    computed: {
      totalPages() {
        return Math.ceil(Object.keys(this.groupedArticles).length / this.pageSize);
      },
      paginatedArticles() {
        const articlesArray = Object.entries(this.groupedArticles).map(([fournisseurId, data]) => ({
            fournisseurId,
            ...data
        }));
        const start = (this.currentPage - 1) * this.pageSize;
        return articlesArray.slice(start, start + this.pageSize);
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
      groupedArticles() {
        const grouped = {};
        this.articles.forEach(article => {
            if (!grouped[article.fournisseur_id]) {
                grouped[article.fournisseur_id] = { 
                    fournisseur: article.fournisseur.prenom + ' ' + article.fournisseur.nom, 
                    color: article.fournisseur.color,
                    remarque: article.fournisseur.remarque,
                    articles: [] 
                };
                this.expanded[article.fournisseur_id] = false;
                this.selectedArticles[article.fournisseur_id] = [];
            }
            grouped[article.fournisseur_id].articles.push(article);
        });
        return grouped;
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
      getPaginatedArticles(fournisseurId) {
        if (!this.articlesPagination[fournisseurId]) {
            this.articlesPagination[fournisseurId] = { page: 1 };
        }
        const totalPages = Math.ceil(this.groupedArticles[fournisseurId].articles.length / this.pageSize);
        const start = (this.articlesPagination[fournisseurId].page - 1) * this.pageSize;
        return {
            paginatedItems: this.groupedArticles[fournisseurId].articles.slice(start, start + this.pageSize),
            currentPage: this.articlesPagination[fournisseurId].page,
            totalPages,
            nextPage: () => { if (this.articlesPagination[fournisseurId].page < totalPages) this.articlesPagination[fournisseurId].page++; },
            prevPage: () => { if (this.articlesPagination[fournisseurId].page > 1) this.articlesPagination[fournisseurId].page--; }
        };
      },
      toggleDropdown(id) {
        this.expanded[id] = !this.expanded[id];
      },
      changePage(page) {
        if (page < 1 || page > this.totalPages) return; // Limiter la page
        this.currentPage = page;
      },
      async updateEcheance(article) {
        const response = await axios.put(route('article.updateIsPaid', article.id), {
          article
        });
        if (response.data.success) {
          useToast().success('Date d\'échéance mise à jour avec succès.');
        }
        else {
          useToast().error('Erreur lors de la mise à jour de la date d\'échéance.');
        }
      },
      async updateEcheanceOfSelectedArticles(fournisseurId) {
        const toast = useToast();
        const selectedArticleIds = this.selectedArticles[fournisseurId];

        if (selectedArticleIds.length === 0) {
          toast.error('Aucun article sélectionné.');
          return;
        }

        try {          
          for (const articleId of selectedArticleIds) {
            let article = this.groupedArticles[fournisseurId].articles.find((a) => a.id === articleId);
            article.dateEcheance = this.dateEcheance;
            const response = await axios.put(route('article.updateIsPaid', articleId), {
              article
            });
          }
                    

          toast.success('Date d\'échéance mise à jour avec succès.');
          this.$inertia.reload();
        } catch (error) {
          console.error(error);
          toast.error('Erreur lors de la mise à jour de la date d\'échéance.');
        }
      },
      selectAllArticles(fournisseurId) {
        if (this.selectedArticles[fournisseurId].length === this.groupedArticles[fournisseurId].articles.length) {
          this.selectedArticles[fournisseurId] = [];
        } else {
          this.selectedArticles[fournisseurId] = this.groupedArticles[fournisseurId].articles.map((a) => a.id);
        }
      },
      async sendSelectedMails(fournisseurId) {
        if(confirm('Envoyer les mails de rappel pour les articles sélectionnés ?'))
        {
          const toast = useToast();
          const selectedArticleIds = this.selectedArticles[fournisseurId];

          if (selectedArticleIds.length === 0) {
            toast.error('Aucun article sélectionné.');
            return;
          }

          try {
            // Envoi des emails pour les articles sélectionnés
            const response = await axios.post('/send-reminder/articles', {
              article_ids: selectedArticleIds, 
            });
            toast.success('Mails de rappel envoyés avec succès.');
            this.$inertia.reload();
          } catch (error) {
            toast.error('Erreur lors de l\'envoi des mails de rappel.');
          }
        }
      },
      async sendAllMail(fournisseur_id){
        if(confirm('Envoyer les mails de rappel pour tous les articles de ce fournisseur ?'))
        {
          const toast = useToast();
          let articles = this.groupedArticles[fournisseur_id].articles;
          try {
            // Envoyer un mail avec l'article
            const response = await axios.post('/send-reminder/articles', {
              article_ids: articles.map((a) => a.id), 
            })
            console.log(response);
            toast.success('Email de rappel envoyé avec succès');          
            this.$inertia.reload();          
          } catch (error) {
            console.error(error);
            toast.error('Erreur lors de l\'envoi du rappel (pas d\'email touvé pour ce fournisseur)');
          }
        }
      },
      async sendMail(article) {
        if (!confirm('Envoyer un mail de rappel pour cet article ?')) return;        
        const toast = useToast();
        try {
          // Envoyer un mail avec l'article
          const response = await axios.post('/send-reminder/articles', {
            article_ids: [article.id], 
          })
          console.log(response);
          toast.success('Email de rappel envoyé avec succès');
          // reload the page
          this.$inertia.reload();
          
          
        } catch (error) {
          console.error(error);
          toast.error('Erreur lors de l\'envoi du rappel (pas d\'email touvé pour ce fournisseur)');
        }          
      },
      showArticle(id) {
        this.$inertia.visit(route('article.show', id));
      },
      showFournisseur(id) {
        this.$inertia.visit(route('fournisseur.show', id));
      },
      excelExport() {
        if(this.start_date == '' || this.end_date == '') {
          const toast = useToast();
          toast.error('Veuillez renseigner les dates de début et de fin');
          return;
        }

        const url = route('export.articles', {
          start_date: this.start_date,
          end_date: this.end_date,
        });

        console.log(url);

        // Ouvre la route dans une nouvelle fenêtre
        window.open(url, '_blank');
      },
      excelFournisseur() {
        const url = route('export.all.fournisseurs');

        window.open(url, '_blank');
      },
      excelArticle() {
        const url = route('export.all.articles');

        window.open(url, '_blank');
      },
      excelArticleTransit() {
        const url = route('export.all.articles.transit');
 
        window.open(url, '_blank');
      },
    },
};

</script>