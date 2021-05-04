<?php

namespace App\Services;

class PaginatorService
{

    public function paginator($request, $donnees, $paginatorInterface)
    {
        $tabPagin = $paginatorInterface->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
        return $tabPagin;
    }
}
