<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Status</th>
            <th>Date Status</th>
            <th>Vente ID</th>
            <th>Quantité Vendue</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($articles as $article)
            <tr>
                <td>{{ $article->id }}</td>
                <td>{{ $article->description }}</td>
                <td>{{ $article->status }}</td>
                <td>{{ $article->dateStatus }}</td>
                <td>{{ $article->vente?->id ?? 'N/A' }}</td>
                <td>{{ $article->vente?->quantite ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
