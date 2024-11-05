<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Imprimer le Code-Barres</title>
    <style>
        /* Configuration du format de la page pour A7 en paysage */
        @page {
            size: 105mm 74mm; /* A7 en paysage */
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
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            width: 100vw;
        }

        .barcode img {
            max-height: 60%; /* Ajuste la hauteur de l'image pour s'adapter en paysage */
        }

        /* Cacher tous les autres éléments sauf l'image lors de l'impression */
        @media print {
            * { visibility: hidden; }
            .barcode img { visibility: visible; display: block; margin: auto; }
        }
    </style>
</head>
<body>
    <div class="barcode">
        <img src="{{ $barcodeImage }}" alt="Code-barres">
    </div>

    <!-- JavaScript pour lancer l'impression automatiquement -->
    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>
