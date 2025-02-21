<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HeatMapController extends AbstractController
{
    private array $vendas_2024 = [
        'Fiat' => 215,
        'Volkswagen' => 165, 
        'Chevrolet' => 136,
        'Toyota' => 87,
        'Hyundai' => 70,
        'Jeep' => 53,
        'Nissan' => 44,
        'Renault' => 38,
        'Honda' => 41,
        'Ford' => 28,
    ];

    private array $vendas_2023 = [
        'Fiat' => 221,
        'Volkswagen' => 156, 
        'Chevrolet' => 145,
        'Toyota' => 85,
        'Hyundai' => 67,
        'Jeep' => 42,
        'Nissan' => 46,
        'Renault' => 38,
        'Honda' => 42,
        'Ford' => 35,
    ];

    #[Route('/heatmap', name: 'app_heatmap')]
    public function index(): Response
    {
        $totalVendas = array_sum($this->vendas_2024);
        $percentuais = [];

        foreach ($this->vendas_2024 as $marca => $vendas) {
            $percentuais[$marca] = ($vendas / $totalVendas) * 100;
        } 

        return $this->render('heat_map/index.html.twig', [
            'vendas_2024' => $this->vendas_2024,
            'vendas_2023' => $this->vendas_2023,
            'percentuais' => $percentuais,
            'total_vendas' => $totalVendas,
        ]);
    }

    #[Route('/heatmap/update', name: 'app_heatmap_update')]
    public function update(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if(!isset($data['marca']) || !isset($data['vendas'])) {
            return new JsonResponse(['error' => 'Dados inválidos'], 400);
        }

        $this->vendas_2024[$data['marca']] = (int) $data['vendas'];

        return new JsonResponse([
            'vendas' => $this->vendas_2024
        ]);
    }
}
