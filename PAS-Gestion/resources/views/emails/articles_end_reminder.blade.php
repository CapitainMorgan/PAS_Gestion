@php
use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Rappel : Fin de dépôt de vos articles</title>
</head>
<body>
    <h1>Rappel : Fin de dépôt de vos articles</h1>
    <p>Bonjour,</p>
    <p>Le dépôt des articles suivants est arrivé à échéance :</p>
    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th>Article(s)</th>
                <th>Date de fin</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($articles as $article)
                <tr>
                    <td>{{ $article->description }}</td>
                    <td>{{ Carbon::parse($article->dateEcheance)->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p>Merci de venir récupérer vos articles, munie d'un sac, dans les 5 jours à dater du présent mail.<br>
    Passé ce délai nous serons contraintes de les débarasser. </p>
    <p>Merci d'avance de votre compréhension,</p>
    <p>La Boutique Prêt à Séduire<br>
    Rue du Liseron 9, 1006 Lausanne-Ouchy Tél: 021 601 32 29</p>
</body>
</html>
