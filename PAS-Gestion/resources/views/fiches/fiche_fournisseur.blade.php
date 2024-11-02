<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Fournisseur</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; font-weight: bold; font-size: 18px; margin-bottom: 10px; }
        .info, .conditions { margin-bottom: 15px; }
        .info-table, .articles-table { width: 100%; border-collapse: collapse; }
        .info-table td, .articles-table td, .articles-table th { padding: 5px; border: 1px solid #ddd; }
        .articles-table th { text-align: left; background-color: #f2f2f2; }
        .signature { margin-top: 20px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        Prêt à Séduire<br>
        Rue du Liseron 9, 1006 Lausanne-Ouchy Tél: 021 601 32 29
    </div>

    <div class="info">
        <table class="info-table">
            <tr>
                <td>Prénom</td>
                <td>{{ $fournisseur->prenom }}</td>
                <td>N° téléphone</td>
                <td>{{ $fournisseur->telephone }}</td>
            </tr>
            <tr>
                <td>Nom</td>
                <td>{{ $fournisseur->nom }}</td>
                <td>N° Mobile</td>
                <td>{{ $fournisseur->mobile }}</td>
            </tr>
            <tr>
                <td>Adresse 1</td>
                <td>{{ $fournisseur->adresse }}</td>
                <td>N° Tél Prof.</td>
                <td>{{ $fournisseur->tel_prof }}</td>
            </tr>
            <tr>
                <td>NP / Ville</td>
                <td>{{ $fournisseur->ville }}</td>
                <td>No. Fournisseur</td>
                <td>{{ $fournisseur->id }}</td>
            </tr>
        </table>
    </div>

    <div class="articles">
        <table class="articles-table">
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th>Visa</th>
                    <th>Part Fournisseur</th>
                    <th>Prix Vente Min.</th>
                    <th>Prix Vente Max.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articles as $article)
                    <tr>
                        <td>{{ $article->description }}</td>
                        <td></td> <!-- Visa si nécessaire -->
                        <td>{{ $article->part_fournisseur }}</td>
                        <td>{{ $article->prix_vente_min }}</td>
                        <td>{{ $article->prix_vente_max }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="conditions">
        <strong>Conditions Vente :</strong>
        <ol>
            <li>Les vêtements et accessoires sont mis en dépôt pour une durée déterminée...</li>
            <li>Vous avez droit aux 50% du prix de vente.</li>
            <li>Après cette période convenue, la boutique peut disposer librement des articles invendus.</li>
        </ol>
        <p><strong>Délai du Dépôt :</strong> {{ $fournisseur->delai_depot }}</p>
    </div>

    <div class="signature">
        Signature: ________________________
    </div>
</body>
</html>
