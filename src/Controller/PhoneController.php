<?php


namespace App\Controller;


use App\Repository\TelephoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var TelephoneRepository
     */
    private $telephoneRepository;

    public function __construct(EntityManagerInterface $entityManager, TelephoneRepository $telephoneRepository)
    {
        $this->entityManager = $entityManager;
        $this->telephoneRepository = $telephoneRepository;
    }

    /**
     * @Route("phone/{id}/delete", name="phone_delete")
     */
    public function deletePhone(Request $request, int $id){
        $token = $request->query->get('token');
        $phone = $this->getPhone($id);
        $contact = $phone->getContact();
        /*
         * https://symfony.com/doc/current/security/csrf.html
         */
        if($this->isCsrfTokenValid('delete-form',$token)){
            $this->entityManager->remove($phone);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('details_contact', ['id'=>$contact->getId()]);
    }

    private function getPhone(int $id){
        $phone = $this->telephoneRepository->find($id);
        if(!$phone){
            throw new NotFoundHttpException();
        }
        if($phone->getContact()->getGroupes()[0]->getUser() != $this->getUser()){
            throw new AccessDeniedHttpException();
        }
        return $phone;
    }

}