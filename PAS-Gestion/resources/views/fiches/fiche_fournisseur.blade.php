@php
use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Fournisseur</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; height: 100%; margin: 0; }
        .header { text-align: center; font-weight: bold; font-size: 18px; margin-bottom: 10px; }
        .info, .conditions { margin-bottom: 15px; }
        .info-table, .articles-table { width: 100%; border-collapse: collapse; }
        .info-table td, .articles-table td, .articles-table th { padding: 5px; border: 1px solid #ddd; }
        .articles-table th { text-align: left; background-color: #f2f2f2; }
        .signature { margin-top: 20px; text-align: right; }
    </style>
</head>
<body style="height: 100%; margin: 0;">
    <div class="content" style="min-height: calc(100vh - 60px); display: flex; flex-direction: column;">
        <div class="main" style="flex=1">
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
                        <td>{{ $fournisseur->rue }}</td>
                        <td>N° Tél Prof.</td>
                        <td>{{ $fournisseur->numProf }}</td>
                    </tr>
                    <tr>
                        <td>NP / Ville</td>
                        <td>{{$fournisseur->npa}} / {{ $fournisseur->ville }}</td>
                        <td>No. Fournisseur</td>
                        <td><b>{{ $fournisseur->id }}</b></td>
                    </tr>
                </table>
            </div>
            

            @if ($articles !== NULL)
            <div class="articles">
                <table class="articles-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Libellé</th>
                            <th>Moyen de paiement</th>
                            <th>Part Fournisseur</th>
                            <th>Prix Vente Solde</th>
                            <th>Prix Vente</th>
                            <th>Quantité</th>
                            <th>Delai du Dépôt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $num = 1;
                        @endphp
                        @foreach ($articles as $article)
                            <tr>
                                <td>{{ $num++ }}</td>
                                <td>{{ $article->description }}</td>
                                <td>@if ($article->vente !== NULL) {{ $article->vente->status }} @endif</td> <!-- Visa si nécessaire -->
                                <td>{{ $article->prixClient }}</td>
                                <td>{{ $article->prixSolde }}</td>
                                <td>{{ $article->prixVente }}</td>
                                <td>{{ $article->quantite }}</td>
                                <td>{{ Carbon::parse($article->dateEcheance)->format('d.m.Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="conditions" style="margin-top:10px; border: 1px solid #ddd; padding:5px;">
                <strong>Conditions Vente :</strong>
                <ol>
                    @foreach ($conditionsArray as $condition)
                        <li>{{ $condition }}</li>
                    @endforeach
                </ol>
            </div>

            <div class="signature">
                Signature: ________________________
            </div>
        </div>

        <div class="footer" style="margin-top:50px;padding:10px;background-color: #7d7d7d;">
            <h1 style="display: inline-block; margin-right: 10px;">Fiche client</h1> 
            <h1 style="float: right;">{{ $fournisseur->id }}-{{ date('d.m.Y') }}</h1>
            <div style="clear: both;"></div>
            <b>Le {{ date('d.m.Y') }} </b>
        </div>
    </div>
</body>
</html>
