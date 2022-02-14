<?php

namespace App\Controller;

use App\Entity\Boat;
use App\Repository\BoatRepository;
use App\Service\MapManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/boat")
 */
class BoatController extends AbstractController
{
    /**
     * Move the boat to coord x,y
     * @Route("/move/{x}/{y}", name="moveBoat", requirements={"x"="\d+", "y"="\d+"}))
     */
    public function moveBoat(int $x, int $y, BoatRepository $boatRepository, EntityManagerInterface $em): RedirectResponse
    {
        $boat = $boatRepository->findOneBy([]);
        $boat->setCoordX($x);
        $boat->setCoordY($y);
        $em->flush();

        return $this->redirectToRoute('map');
    }

    /**
     * @Route("/direction/{direction}", name="moveBoatDirection", requirements={"direction"="[NSEW]{1}"}))
     */
    public function moveDirection(string $direction, BoatRepository $boatRepository, EntityManagerInterface $em, MapManager $mapManager): RedirectResponse
    {
        $boat = $boatRepository->findOneBy([]);

        switch ($direction) {
            case 'N':
                $boat->setCoordY($boat->getCoordY() - 1);
                break;
            case 'S':
                $boat->setCoordY($boat->getCoordY() + 1);
                break;
            case 'E':
                $boat->setCoordX($boat->getCoordX() + 1);
                break;
            case 'W':
                $boat->setCoordX($boat->getCoordX() - 1);
                break;
        }

        if ($mapManager->tileExists($boat->getCoordX(), $boat->getCoordY())) {
            $em->flush();
        } else {
            $this->addFlash('danger', 'La tuile n\'existe pas');
        }

        if ($mapManager->checkTreasure($boat)) {
            $this->addFlash('success', 'Treasure found !');
        }

        return $this->redirectToRoute('map');
    }
}
