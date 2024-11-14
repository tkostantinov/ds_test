<?php

namespace App\Controller;

use App\Service\Csv2Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class CsvToJsonController extends AbstractController
{
    #[Route('/csv2json', name: 'app_csv_to_json')]
    public function index(Request $request, Csv2Json $csv2Json, #[MapQueryParameter] string $_q = ''): JsonResponse
    {
        $file = $request->files->get('csv');

        $csv = file($file->getPathname());

        if($csv === false) {
            return $this->json(["BAD REQUEST"]);
        }

        if($_q !== '') {
            return $this->json($csv2Json->convertFiltered($csv, $_q));
        }

        return $this->json($csv2Json->convert($csv));
    }

}
