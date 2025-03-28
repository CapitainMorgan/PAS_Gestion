@php
use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Fournisseur de @if ($status === "vente") Vente @else Dépôt @endif</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; height: 100%; margin: 0; }
        .header { text-align: center; font-weight: bold; font-size: 18px; margin-bottom: 10px; }
        .info, .conditions { margin-bottom: 15px; }
        .info-table, .articles-table { width: 100%; border-collapse: collapse; }
        .info-table td, .articles-table td, .articles-table th { padding: 5px; border: 1px solid #ddd; }
        .articles-table th { text-align: left; background-color: #f2f2f2; }
        .signature { margin-top: 20px; text-align: right; }
        .main {
            margin-bottom: 200px;
        }
        .conditions,
        .signature {
            page-break-inside: avoid; /* Empêche les divs d'être coupées */
        }
    </style>
</head>
<body style="height: 100%; margin: 0;">
    <div class="footer" style="
    position: fixed;
    bottom: 0; 
    left: 0; 
    width: 100%; 
    margin-bottom: 80px;
    page">
    <div class="gray" style="background-color: #e9e9e9; padding: 10px;">
        <h1 style="display: inline-block; margin-right: 10px;">Fiche client</h1> 
        <h1 style="float: right;">{{ $fournisseur->id }}- @if ($status === "depot") {{ Carbon::parse($date_depot)->format('d.m.Y') }} @else {{ date('d.m.Y') }} @endif</h1>
        <div style="clear: both;"></div>
        <div><b>Le @if ($status === "depot") {{ Carbon::parse($date_depot)->format('d.m.Y') }} @else {{ date('d.m.Y') }} @endif </b></div>
    </div>
    <p>HORAIRES : Du lundi au samedi 10h00 - 18h00 non stop DIMANCHE : FERME</p>
</div>
    <div class="content" style="min-height: calc(100vh - 60px); display: flex; flex-direction: column;">
        <div class="main" style="flex=1">
            <div class="header" style="padding:10px;background-color: #e9e9e9;">
                Fiche fournisseur de @if ($status === "vente") vente @else dépôt @endif<br>
                Prêt à Séduire<br>
                Rue du Liseron 9, 1006 Lausanne-Ouchy Tél: 021 601 39 29
            </div>

            <div class="info">
                <table class="info-table">
                    <tr>
                        <td>Prénom</td>
                        <td>{{ $fournisseur->prenom }}</td>
                        <td>N° téléphone</td>
                        <td>{{ preg_replace('/(\d{3})(\d{3})(\d{2})(\d{2})/', '$1 $2 $3 $4', $fournisseur->telephone) }}</td>
                    </tr>
                    <tr>
                        <td>Nom</td>
                        <td>{{ $fournisseur->nom }}</td>
                        <td>N° Mobile</td>
                        <td>{{ preg_replace('/(\d{3})(\d{3})(\d{2})(\d{2})/', '$1 $2 $3 $4', $fournisseur->mobile) }}</td>
                    </tr>
                    <tr>
                        <td>Adresse 1</td>
                        <td>{{ $fournisseur->rue }}</td>
                        <td>N° Tél Prof.</td>
                        <td>{{ preg_replace('/(\d{3})(\d{3})(\d{2})(\d{2})/', '$1 $2 $3 $4', $fournisseur->numProf) }}</td>
                    </tr>
                    <tr>
                        <td>NP / Ville</td>
                        <td>{{$fournisseur->npa}} / {{ $fournisseur->ville }}</td>
                        <td>No. Fournisseur</td>
                        <td><b>{{ $fournisseur->id }}</b></td>
                    </tr>					
					<tr>
						<td>Email</td>
						<td>{{$fournisseur->email}}</td>
						<td></td>
						<td></td>
					</tr>
                </table>
            </div>
            

            @if ($ventes !== NULL)
            <div class="articles">
                <table class="articles-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Libellé</th>
                            <th>Part Fournisseur prévue</th>
                            <th>Prix Vente prévu</th>
                            <th>Prix Vente</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Date de vente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                        @endphp
                        @foreach ($ventes as $vente)
                            <tr>                                
                                <td>{{ substr($vente->article->id, strlen($vente->article->fournisseur->id), -strlen($vente->article->created_at->format('dmy'))) }}</td>
                                <td>{{ $vente->article->description }}</td>
                                <td>{{ $vente->article->prixClient }}</td>
                                <td>{{ $vente->article->prixVente }}</td>
                                <td>{{ $vente->prix_unitaire }}</td>
                                <td>{{ $vente->quantite }}</td>
                                <td>{{ $vente->prix_unitaire * $vente->quantite }}</td>
                                <td>{{ Carbon::parse($vente->created_at)->format('d.m.Y') }}</td>
                                @php
                                    $total += $vente->prix_unitaire * $vente->quantite;
                                @endphp
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="6" style="text-align: right;"><b>Total</b></td>
                            <td><b>{{ $total }}</b></td>
                            <td></td>
                        </tr>
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

        
    </div>    
</body>
</html>
