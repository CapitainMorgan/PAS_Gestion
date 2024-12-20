<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Imprimer le Code-Barres</title>
    <style>
        /* Configuration du format de la page pour A7 en paysage */
        @page {
            size: 8.9cm 3.8cm; /* A7 en paysage */
            margin: 0; /* Pas de marge */
        }

        /* Styles pour centrer l'image */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .barcode {
            text-align: center;
            width: 60%;
        }

        .barcode-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 5px; /* Espacement entre le code-barres et le texte */
        }

        .barcode-text {
            font-size: 14px; /* Ajustez la taille du texte */
            font-weight: bold;
        }

        .text {
            font-size: 7px; /* Ajustez la taille du texte */
        }

        /* Cacher tous les autres éléments sauf l'image lors de l'impression */
        @media print {
            * { visibility: hidden; }
            .barcode img { visibility: visible; display: block; margin: auto; }
            .barcode-text { visibility: visible; display: block; margin: auto; }
            .text { visibility: visible; display: block; margin: auto; }
        }
    </style>
</head>
<body>
    <div class="barcode">
        <div class="text">Art : {{ $code }} Frs: {{ $article['prixVente'] }}</div>
        <!-- Affiche le code-barres -->
        <img class="barcode-image" src="{{ $barcodeImage }}" alt="Code-barres">
        <!-- Affiche le texte du code-barres en dessous -->
        <div class="barcode-text">{{ $code }}</div>
        <div class="text">{{ $article['description'] }} <br> Taille : {{ $article['taille'] }}</div>
    </div>

    <!-- JavaScript pour lancer l'impression automatiquement -->
    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>
