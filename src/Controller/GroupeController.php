<?php


namespace App\Controller;


use App\Entity\Groupe;
use App\Form\GroupeType;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * IsGranted("ROLE_USER")
 */
class GroupeController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var GroupeRepository
     */
    private $groupeRepository;

    public function __construct(EntityManagerInterface  $entityManager, GroupeRepository $groupeRepository)
    {
        $this->entityManager = $entityManager;
        $this->groupeRepository = $groupeRepository;
    }

    /**
     * @Route("groupe/create", name="groupe_create")
     */
    public function createGroupe(Request $request){
        $groupe = new Groupe();
        $groupe->setUser($this->getUser());
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($groupe);
            $this->entityManager->flush();
        }
        return $this->render("groupe/create.html.twig", ['form'=>$form->createView()]);
    }

    /**
     * @Route("groupe/{id}", name="groupe_details", requirements={"id"="\d+"})
     */
    public function detailsGroupe(Request $request, int $id){
        $group = $this->getGroupe($id);
        return $this->render("groupe/details.html.twig", ['groupe'=>$group]);
    }

    /**
     * @Route("groupe", name="groupe_list")
     */
    public function listGroupe(Request $request){
        $groupes = $this->getUser()->getGroupes();
        return $this->render('groupe/list.html.twig', ['groupes'=>$groupes]);
    }

    private function getGroupe(int $id){
        $group = $this->groupeRepository->find($id);
        if(!$group){
            throw new NotFoundHttpException();
        }
        if($group->getUser() != $this->getUser()){
            throw new AccessDeniedHttpException();
        }
        return $group;
    }
}