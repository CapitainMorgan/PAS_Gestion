<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
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
                  <div>
                    <TextInput 
                      ref="barcodeInput" 
                      v-model="scannedBarcode" 
                      @keyup.enter="processBarcode" 
                      placeholder="Scannez un article..." 
                      class="barcode-input"
                      />
                      <div v-if="articles.length">
                        <h3>Articles Scannés</h3>
                        <ul>
                          <li v-for="article in articles" :key="article.id">
                            {{ article.name }} - {{ article.price }} €
                          </li>
                        </ul>
                      </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
  </AuthenticatedLayout>
</template>
  
  <script>
  export default {
    data() {
      return {
        scannedBarcode: '',
        articles: [], // Détails de l'article scanné
      };
    },
    mounted() {
      // Place automatiquement le focus sur l'input
      this.$refs.barcodeInput.focus();
    },
    methods: {
      async processBarcode() {
        const response = await this.$inertia.post(
        route('article.barcode', this.scannedBarcode), 
        {
          onSuccess: (page) => {
            
            if (page.props.article) {
              console.log('Article trouvé', page.props.article);
              // Ajoute l'article à une liste existante
              this.articles.push(page.props.article);
            } else {
              console.error('Article non trouvé');
            }
            this.scannedBarcode = ''; // Réinitialise le champ
          },
          onError: (errors) => {
            console.error('Erreur lors de la recherche de l\'article', errors);
          },
        },
        );
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
  