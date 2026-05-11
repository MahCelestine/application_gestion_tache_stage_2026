<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Http;

class EvolizSyncController extends Controller
{
    // public function sync()
    // {
    //     $companyId = env('EVOLIZ_COMPANY_ID');

    //     $response = Http::withHeaders(['Accept' => 'application/json'])
    //         ->withBasicAuth(env('EVOLIZ_PUBLIC_KEY'), env('EVOLIZ_SECRET_KEY'))
    //         ->withoutVerifying()
    //         ->get("https://www.evoliz.io/api/v1/companies/{$companyId}/quotes");

    //     if ($response->failed()) {
    //         return back()->with('error', 'Erreur API Evoliz : ' . $response->body());
    //     }

    //     $quotes = $response->json()['data'] ?? [];
    //     $linesToProcess = [];

    //     foreach ($quotes as $quote) {
    //         foreach ($quote['items'] as $item) {
    //             $linesToProcess[] = [
    //                 'evoliz_item_id' => $item['itemid'],
    //                 'evoliz_quote_id' => $quote['quoteid'],
    //                 'label' => $item['designation_clean'] ?? $item['designation'],
    //                 'quote_number' => $quote['document_number'],
    //                 'client_name' => $quote['client']['name'],
    //             ];
    //         }
    //     }

    //     $existingIds = Task::whereNotNull('evoliz_item_id')->pluck('evoliz_item_id')->toArray();
    //     $pending = collect($linesToProcess)->whereNotIn('evoliz_iten_id', $existingIds)->values()->all();

    //     if (empty($pending)) {
    //         return back()->with('info', 'Tout est à jour');
    //     }

    //     session(['pending_evoliz_lines' => $pending]);

    //     return redirect()->route('tasks.create-evoliz');
    // }

    // public function sync()
    // {
    //     $publicKey = env('EVOLIZ_PUBLIC_KEY');
    //     $secretKey = env('EVOLIZ_SECRET_KEY');

    //     // --- ÉTAPE 1 : LOGIN ---
    //     $loginResponse = Http::withoutVerifying()
    //         ->withHeaders(['Accept' => 'application/json'])
    //         ->post("https://www.evoliz.io/api/login", [
    //             'public_key' => $publicKey,
    //             'secret_key' => $secretKey,
    //         ]);

    //     if ($loginResponse->failed()) {
    //         dd("Erreur Login : " . $loginResponse->status(), $loginResponse->json());
    //     }

    //     $accessToken = $loginResponse->json()['access_token'];

    //     // --- ÉTAPE 2 : RÉCUPÉRATION DES DEVIS ---
    //     // Note : La doc dit que si on a le token, l'ID compagnie est facultatif, 
    //     // mais on peut le garder pour être sûr.
    //     $response = Http::withToken($accessToken) // Utilise "Bearer YOUR_ACCESS_TOKEN"
    //         ->withHeaders(['Accept' => 'application/json'])
    //         ->withoutVerifying()
    //         ->get("https://www.evoliz.io/api/v1/quotes");

    //     if ($response->failed()) {
    //         dd("Erreur Devis : " . $response->status(), $response->json());
    //     }

    //     // Si ça marche, on affiche les devis !
    //     dd("Succès !", $response->json());
    // }

    public function sync()
    {
        $publicKey = env('EVOLIZ_PUBLIC_KEY');
        $secretKey = env('EVOLIZ_SECRET_KEY');

        // 1. LOGIN : Récupération du Token (Obligatoire)
        $loginResponse = Http::withoutVerifying()
            ->withHeaders(['Accept' => 'application/json'])
            ->post("https://www.evoliz.io/api/login", [
                'public_key' => $publicKey,
                'secret_key' => $secretKey,
            ]);

        if ($loginResponse->failed()) {
            return back()->with('error', 'Erreur Login API : ' . $loginResponse->body());
        }

        $accessToken = $loginResponse->json()['access_token'];

        // 2. RÉCUPÉRATION DES DEVIS
        $response = Http::withToken($accessToken)
            ->withoutVerifying()
            ->withHeaders(['Accept' => 'application/json'])
            ->get("https://www.evoliz.io/api/v1/quotes");

        if ($response->failed()) {
            return back()->with('error', 'Erreur Récupération Devis : ' . $response->body());
        }

        $quotes = $response->json()['data'] ?? [];
        $linesToProcess = [];

        // 3. TRAITEMENT DES LIGNES (Items)
        foreach ($quotes as $quote) {
            // Attention : Vérifie si 'items' existe dans le JSON d'Evoliz
            if (isset($quote['items'])) {
                foreach ($quote['items'] as $item) {
                    $linesToProcess[] = [
                        'evoliz_item_id' => $item['itemid'],
                        'evoliz_quote_id' => $quote['quoteid'],
                        'label' => $item['designation_clean'] ?? $item['designation'],
                        'quote_number' => $quote['document_number'],
                        'client_name' => $quote['client']['name'] ?? 'Client Inconnu',
                    ];
                }
            }
        }

        // 4. FILTRAGE DES DOUBLONS
        // On récupère les IDs déjà présents en BDD pour ne pas les proposer à nouveau
        $existingIds = \App\Models\Task::whereNotNull('evoliz_item_id')
            ->pluck('evoliz_item_id')
            ->toArray();

        $pending = collect($linesToProcess)
            ->whereNotIn('evoliz_item_id', $existingIds) // Correction de la faute de frappe 'iten_id'
            ->values()
            ->all();

        if (empty($pending)) {
            return back()->with('info', 'Toutes les lignes Evoliz sont déjà synchronisées.');
        }

        // 5. MISE EN SESSION ET REDIRECTION
        session(['pending_evoliz_lines' => $pending]);

        return redirect()->route('tasks.create-evoliz');
    }


}
