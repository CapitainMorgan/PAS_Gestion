@php
use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>Rappel : Fin de dépôt pour vos articles</title>
</head>
<body>
    <h1>Rappel : Fin de dépôt pour vos articles</h1>
    <p>Bonjour,</p>
    <p>Ceci est un rappel concernant la fin de dépôt des articles suivants :</p>
    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th>Article</th>
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
    <p>Merci,</p>
    <p>Prêt à Séduire<br>
    Rue du Liseron 9, 1006 Lausanne-Ouchy Tél: 021 601 32 29</p>
</body>
</html>
