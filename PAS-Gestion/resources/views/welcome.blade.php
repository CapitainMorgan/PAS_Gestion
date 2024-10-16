<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Application</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <!-- Menu de navigation -->
        <menu-component @navigate="handleNavigation"></menu-component>

        <!-- Page de garde -->
        <home-component v-if="currentPage === 'home'"></home-component>

        <!-- Autres pages (caisse, produit, etc.) -->
        <div v-if="currentPage === 'caisse'">
            <h2>Caisse</h2>
            <!-- Ici vous inclurez le composant caisse -->
        </div>

        <div v-if="currentPage === 'article'">
            <h2>Article</h2>
            <!-- Ici vous inclurez le composant article -->
        </div>

        <div v-if="currentPage === 'fournisseur'">
            <h2>Fournisseur</h2>
            <!-- Ici vous inclurez le composant fournisseur -->
        </div>

        <div v-if="currentPage === 'frais'">
            <h2>Frais</h2>
            <!-- Ici vous inclurez le composant frais -->
        </div>

        <div v-if="currentPage === 'parametre'">
            <h2>Paramètre</h2>
            <!-- Ici vous inclurez le composant parametre -->
        </div>

    <script src="{{ mix('js/app.js') }}"></script>

    <script>
    new Vue({
        el: '#app',
        data: {
            currentPage: 'home'
        },
        methods: {
            handleNavigation(page) {
                this.currentPage = page;
            }
        }
    });
    </script>
</body>
</html>
