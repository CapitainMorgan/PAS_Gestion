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
            min-height: 100%
            margin: 0;
            padding: 0;
        }

        .page {
            page-break-after: always;
            padding-top: 10px;
            height: 100%;
        }

        .barcode {
            text-align: center;
            width: 100%;
        }

        .barcode-image {
            max-width: 80%;
            height: auto;
            margin-bottom: 5px; /* Espacement entre le code-barres et le texte */
        }

        .barcode-text {
            font-size: 18px; /* Ajustez la taille du texte */
            font-weight: bold;
        }

        .text {
            font-size: 12px; /* Ajustez la taille du texte */
        }
		
		.text-prix {
			font-size:14px;
		}

        /* Cacher tous les autres éléments sauf l'image lors de l'impression */
        @media print {
            * { visibility: hidden; }
            .barcode img { visibility: visible; display: block; margin: auto; }
            .barcode-text { visibility: visible; display: block; margin: auto; }
            .text { visibility: visible; display: block; margin: auto; }
			.text-prix { visibility: visible; display: block; margin: auto; }
        }
    </style>
</head>
<body>
    <div class="barcode">
        @foreach ($barcodes as $barcode)
        <div class="page">
            <div class="text" style="margin-bottom:15px;float:left;margin-left:10%;">Art : {{ $barcode['code'] }} </div><div class="text-prix" style="float:right;margin-right:10%;"> Frs: {{ $barcode['article']['prixVente'] }}</div>
            <!-- Affiche le code-barres -->
            <img class="barcode-image" src="{{ $barcode['barcodeImage'] }}" alt="Code-barres">
            <!-- Affiche le texte du code-barres en dessous -->
            <div class="barcode-text">{{ $barcode['code'] }}</div>
            <div class="text">{{ $barcode['article']['description'] }} <br> Taille : {{ $barcode['article']['taille'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- JavaScript pour lancer l'impression automatiquement -->
    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>
