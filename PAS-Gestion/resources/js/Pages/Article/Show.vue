<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
</script>


<template>
  <AuthenticatedLayout>
    <Head title="Articles" />
    <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Détails de l'Article
            </h2>
        </template>
        <div class="py-12">
          <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                <div class="p-6 text-gray-900">

                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">{{ article.description }}</h5>
                            <p><strong>ID:</strong> {{ article.id }}</p>
                            <p><strong>Taille:</strong> {{ article.taille ?? 'N/A' }}</p>
                            <p><strong>Prix Vente:</strong> {{ article.prixVente ?? 'N/A' }}</p>
                            <p><strong>Prix Client:</strong> {{ article.prixClient ?? 'N/A' }}</p>
                            <p><strong>Prix Solde:</strong> {{ article.prixSolde ?? 'N/A' }}</p>
                            <p><strong>Quantité:</strong> {{ article.quantite ?? 'N/A' }}</p>
                            <p><strong>Localisation:</strong> {{ article.localisation ?? 'N/A' }}</p>
                            <p><strong>Status</strong> {{ article.status }}</p>
                            <p><strong>ID Dépot:</strong> {{ article.depot_id ?? 'N/A' }}</p>
                            <p><strong>Appartient à:</strong> {{ article.fournisseur[0].nom }} {{ article.fournisseur[0].prenom }}</p>
                            <p><strong>Email du Fournisseur:</strong> {{ article.fournisseur[0].email }}</p>
                        </div>
                    </div>
                        

                        <PrimaryButton><a class="btn btn-secondary mt-3" :href="route('article.index')">Retour à la liste des Articles</a></PrimaryButton>
                    </div>

                </div>
            </div>
          </div>
    </AuthenticatedLayout>
</template>

<script>
export default {
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
};
</script>