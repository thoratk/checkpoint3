<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Boat;
use App\Entity\Tile;
use App\Repository\TileRepository;

class MapManager
{
    private $tileRepository;

    public function __construct(TileRepository $tileRepository)
    {
        $this->tileRepository = $tileRepository;
    }

    public function tileExists(int $x, int $y): bool
    {
        return (bool) $this->tileRepository->findOneBy([
            'coordX' => $x,
            'coordY' => $y,
        ]);
    }

    public function getRandomIsland(): Tile
    {
        $islandTiles = $this->tileRepository->findBy([
            'type' => 'island',
        ]);

        $randomKey = array_rand($islandTiles);

        return $islandTiles[$randomKey];
    }

    public function checkTreasure(Boat $boat): bool
    {
        $boatTile = $this->tileRepository->findOneBy([
            'coordX' => $boat->getCoordX(),
            'coordY' => $boat->getCoordY(),
        ]);

        if (!$boatTile) {
            throw new \Exception('Tuile does not exist !!');
        }

        return $boatTile->hasTreasure();
    }
}
