<?php

namespace App\Service;

use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use function symfony\component\string\u;

class Csv2Json
{
    public function convert(array $csv): array
    {
        $camelCaseHeaders = $this->getCamelCaseHeaders($csv[0]);

        $flatHierarchy = $this->createFlatHierarchy($camelCaseHeaders, $csv);

        $rootNode = $this->getRootNode($flatHierarchy);

        $rootNode['teams'] = $this->getChildTeams($flatHierarchy, $rootNode['team']);

        return [$rootNode['team'] => $rootNode];
    }


    public function convertFiltered(array $csv, string $team): array
    {
        $camelCaseHeaders = $this->getCamelCaseHeaders($csv[0]);

        $flatHierarchy = $this->createFlatHierarchy($camelCaseHeaders, $csv);

        $parentTeams = [$team];
        $parentTeam = $team;

        do {
            $parentTeam = $this->getParentTeam($flatHierarchy, $parentTeam);

            if ($parentTeam !== '') {
                $parentTeams[] = $parentTeam;
            }
        } while ($parentTeam !== '');

        $parentTeamsReversed = array_reverse($parentTeams);

        $rootNode = [];
        $rootTeamName = array_shift($parentTeamsReversed);
        $node = $this->getTeamNode($flatHierarchy, $rootTeamName);
        $rootNode[$rootTeamName] = $node;

        $rootNode[$rootTeamName]['teams'] = $this->getChildTeamsFiltered($flatHierarchy, $parentTeamsReversed);

        return $rootNode;
    }

    /**
     * @param mixed $flatHierarchy
     *
     * @return mixed|void
     */
    private function getRootNode(array $flatHierarchy): array
    {
        foreach ($flatHierarchy as $department) {
            if ($department['parentTeam'] === "") {
                return $department;
            }
        }
    }

    private function getChildTeams(mixed $flatHierarchy, string $team): array
    {
        $childTeams = [];
        foreach ($flatHierarchy as $department) {
            if ($department['parentTeam'] === $team) {
                $childTeams[$department['team']] = $department;
            }
        }

        foreach ($childTeams as &$childTeam) {
            $childTeam["teams"] = $this->getChildTeams($flatHierarchy, $childTeam['team']);
        }

        return $childTeams;
    }

    private function getChildTeamsFiltered(mixed $flatHierarchy, array $parentsHierarchy): array
    {
        $childTeams = [];
        $current = array_shift($parentsHierarchy);

        foreach ($flatHierarchy as $department) {
            if ($department['team'] === $current) {
                $childTeams[$current] = $department;
                $childTeams[$current]["teams"] = $this->getChildTeamsFiltered($flatHierarchy, $parentsHierarchy);
            }
        }

        return $childTeams;
    }

    /**
     * @param array $camelCaseHeaders
     * @param array $csv
     *
     * @return mixed
     */
    public function createFlatHierarchy(array $camelCaseHeaders, array $csv): mixed
    {
        $csv[0] = implode(',', $camelCaseHeaders) . "\n";

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        return $serializer->decode(implode($csv), 'csv');
    }

    private function getTeamNode(array $flatHierarchy, string $team): array
    {
        foreach ($flatHierarchy as $department) {
            if ($department['team'] === $team) {
                return $department;
            }
        }
    }

    private function getParentTeam(array $flatHierarchy, string $team): string
    {
        foreach ($flatHierarchy as $department) {
            if ($department['team'] === $team) {
                return $department['parentTeam'];
            }
        }

        return '';
    }

    /**
     * @param $csv
     *
     * @return array
     */
    private function getCamelCaseHeaders($csv): array
    {
        $headers = explode(",", $csv);

        $camelCaseHeaders = [];

        foreach ($headers as $header) {
            $camelCaseHeaders[] = (string)u($header)->camel();
        }

        return $camelCaseHeaders;
    }
}
