<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
import TextInput from '@/components/TextInput.vue';
import InputLabel from '@/components/InputLabel.vue';
import { Head } from '@inertiajs/vue3';
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
                  <div>
                    <TextInput 
                      ref="barcodeInput" 
                      v-model="scannedBarcode" 
                      placeholder="Scannez un article..." 
                      class="barcode-input"
                      />
                      <div v-if="articles.length">
                        <h3>Articles Scannés</h3>
                        <table class="table-auto">
                          <thead>
                            <tr>
                              <th class="">ID</th>
                              <th class="">Description</th>
                              <th class="">Status</th>
                              <th class="">Localisation</th>
                              <th class="">Prix Vente</th>
                              <th class="">Prix Solde</th>
                              <th class="">Quantité disponible</th>
                              <th class="">Quantité</th>
                              <th class="">Total</th>
                              <th class="">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="article in articles" :key="article.id">
                              <td @click="showArticle(article.id)" class="border px-4 py-2">{{ formatIdArticle(article) }}</td>
                              <td @click="showArticle(article.id)" class="border px-4 py-2">{{ article.description }}</td>
                              <td @click="showArticle(article.id)" class="border px-4 py-2">{{ article.status }}</td>
                              <td @click="showArticle(article.id)" class="border px-4 py-2">{{ article.localisation }}</td>
                              <td @click="showArticle(article.id)" class="border px-4 py-2">{{ article.prixVente }} CHF</td>
                              <td class="border px-4 py-2"><TextInput v-model="article.prixSolde" type="number" class="form-control" style="width: 80px;" placeholder="Prix Solde" @change="updateCart" /> CHF</td>
                              <td @click="showArticle(article.id)" class="border px-4 py-2">{{ article.quantite }}</td>
                              <td class="border px-4 py-2"><TextInput v-model="article.quantiteVente" type="number" class="form-control" style="width: 80px;" placeholder="Quantité" @change="updateCart" /></td>
                              <td v-if="article.prixSolde == null || article.prixSolde == 0" class="border px-4 py-2">{{ article.prixVente * article.quantiteVente }} CHF</td>
                              <td v-else class="border ">{{ article.prixSolde * article.quantiteVente }} CHF</td>
                              <td class="border "><DangerButton @click="removeFromCart(article)">Supprimer</DangerButton></td>
                            </tr>
                            <tr>
                              <td colspan="8" class="border px-4 py-2 font-bold text-right">Total :</td>
                              <td class="border  font-bold">{{ totalPrice }} CHF</td>
                              <td class="border "></td>
                            </tr>
                          </tbody>
                        </table>

                        <div class="form-group">
                            <InputLabel for="vente_status" class="form-label">Vente Status</InputLabel>
                            <select v-model="vente_status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="Cash">Cash</option>
                                <option value="CB">Carte bancaire</option>
                            </select>
                        </div>

                        <div class="mt-4">
                          <PrimaryButton style="margin-right: 10px;" @click="changeStatus('Vendu')">Vendre</PrimaryButton>
                          <PrimaryButton style="margin-right: 10px;" @click="changeStatus('Rendu')">Rendre</PrimaryButton>
                          <PrimaryButton style="margin-right: 10px;" @click="changeStatus('Rendu défectueux')">Rendre défectueux</PrimaryButton>
                          <PrimaryButton style="margin-right: 10px;" @click="changeStatus('Donné')">Donné</PrimaryButton>
                          <PrimaryButton style="margin-right: 10px;" @click="clearCart()">Annuler</PrimaryButton>
                        </div>
                      </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
  </AuthenticatedLayout>
</template>
  
  <script>
  import { useToast } from 'vue-toastification'

  export default {
    data() {
      return {
        scannedBarcode: '',
        articles: [], // Détails de l'article scanné
        vente_status: 'CB',
        scanTimeout: null,
      };
    },
    computed: {
      totalPrice() {
        return this.articles.reduce((total, article) => {
          let price = 0;
          if (article.prixSolde != null && article.prixSolde != 0) {
            price = article.prixSolde;
          } else {
            price = article.prixVente;
          }
          
          const quantity = article.quantiteVente ?? 0;
          return total + price * quantity;
        }, 0);
      }
    },
    mounted() {
      // Place automatiquement le focus sur l'input
      this.$refs.barcodeInput.focus();
      this.loadCart();
       // Ajoute un écouteur d'événements global pour capturer les saisies
      window.addEventListener('keydown', this.handleBarcodeScan);
    },
    beforeDestroy() {
      // Nettoie l'écouteur d'événements
      window.removeEventListener('keydown', this.handleBarcodeScan);
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
      handleBarcodeScan(event) {
        // Vérifie si c'est une touche de caractère valide

        clearTimeout(this.scanTimeout);

        // Définir un délai pour considérer le scan comme terminé
        this.scanTimeout = setTimeout(() => {
          if (this.scannedBarcode) {
            this.processBarcode(); // Appeler la méthode pour traiter le code
          }
        }, 600); // Temps d'attente (ajuster si nécessaire)

      },
      async processBarcode() {
        try {
        const response = await axios.post(route('article.barcode', this.scannedBarcode));
        
        if (response.data && response.data.article) {

          // Ajout de la quantité de vente
          response.data.article.quantiteVente = 1;

          this.addToCart(response.data.article);

        } 
        this.scannedBarcode = '';
      }
      catch (error) {
        console.error(error);
        this.scannedBarcode = '';
        alert('Article non trouvé');        
      }
        
      },
      showArticle(id) {
        this.$inertia.visit(route('article.show', id));
      },
      async addToCart(article) {
        //check if article is already in cart
        const existingArticle = this.articles.find(a => a.id === article.id);
        if (existingArticle) {
          //check if we have enough quantity
          if (existingArticle.quantiteVente >= existingArticle.quantite) {
            alert('Pas assez de stock');
            return;
          }
          existingArticle.quantiteVente++;
        } else {
          const response = await axios.post('/api/cart/add', { article });
          this.articles = response.data.cart;
        }        
      },
      async removeFromCart(article) {
        let id = article.id;
        const response = await axios.post('/api/cart/remove', { id });
        this.articles = response.data.cart;
      },
      async updateCart() {
        const response = await axios.post('/api/cart/update', { articles: this.articles });
        this.articles = response.data.cart;
      },
      async loadCart() {
        const response = await axios.get('/api/cart');
        this.articles = response.data.cart;
      },
      async clearCart() {
        await axios.post('/api/cart/clear');
        this.articles = [];
      },
      async changeStatus(status) {
        //check if we have enough quantity
        for (const article of this.articles) {
          //convert to integer
          article.quantiteVente = parseInt(article.quantiteVente);
          article.quantite = parseInt(article.quantite);
          if (article.quantiteVente > article.quantite) {
            alert('Pas assez de stock');
            return;
          }
        }

        //check status is "En Stock"
        for (const article of this.articles) {
          if (article.status !== 'En Stock') {
            alert('Article pas en stock');
            return;
          }
        }

        console.log(this.articles);
        const response = await axios.post(route('article.updateStatus') , { articles: this.articles, status: status, vente_status: this.vente_status });

        const toast = useToast();

        if (response.data.message) {
          this.clearCart();
          toast.success(response.data.message);
        } else {
          toast.error('Erreur lors de la vente');
        }

      },
    },
  };
  </script>
  
  <style>
  .barcode-input {
    width: 100%;
    padding: 10px;
    font-size: 18px;
    margin-bottom: 20px;
  }
  </style>
  