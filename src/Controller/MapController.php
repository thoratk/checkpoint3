<?php

namespace App\Controller;

use App\Repository\TileRepository;
use App\Service\MapManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tile;
use App\Repository\BoatRepository;

class MapController extends AbstractController
{
    /**
     * @Route("/map", name="map")
     */
    public function displayMap(BoatRepository $boatRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $tiles = $em->getRepository(Tile::class)->findAll();

        foreach ($tiles as $tile) {
            $map[$tile->getCoordX()][$tile->getCoordY()] = $tile;
        }

        $boat = $boatRepository->findOneBy([]);

        $tile = $map[$boat->getCoordX()][$boat->getCoordY()] ?? null;

        return $this->render('map/index.html.twig', [
            'map'  => $map ?? [],
            'boat' => $boat,
            'tile' => $tile,
        ]);
    }

    /**
     * @Route("/start", name="map.start")
     */
    public function start(BoatRepository $boatRepository, EntityManagerInterface $em, MapManager $mapManager, TileRepository $tileRepository): Response
    {
        // Reset boat cords
        $boat = $boatRepository->findOneBy([]);
        $boat->resetCoords();

        // put a treasyre on the map
        $islands = $tileRepository->findBy([
            'type' => 'island',
        ]);

        foreach ($islands as $island) {
            $island->setHasTreasure(false);
        }

        $randomIslandTile = $mapManager->getRandomIsland();
        $randomIslandTile->setHasTreasure(true);

        $em->flush();

        return $this->redirectToRoute('map');
    }
}
