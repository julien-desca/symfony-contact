<?php


namespace App\Controller;


use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class MassCreationController extends AbstractController
{
    private $lastNames = ['Smith', 'Doe', 'Waterson', 'Sanchez', 'Wayne', 'Wilson'];
    private $firstNames = ['John', 'Bruce', 'Rick', 'Beth', 'Jane', 'Lucy'];
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("mass/{number}")
     */
    public function foo(int $number){
        $groups = $this->getUser()->getGroupes()[0];
        for($i = 0 ; $i < $number ; $i++){
            $iFirstName = array_rand($this->firstNames,1);
            $firstName = $this->firstNames[$iFirstName];
            $iLastName = array_rand($this->lastNames,1);
            $lastName = $this->lastNames[$iLastName];
            $contact = new Contact();
            $contact->setNom($lastName);
            $contact->setPrenom($firstName);
            $contact->addGroupe($groups);
            $this->entityManager->persist($contact);
        }
        $this->entityManager->flush();
        return $this->redirectToRoute('list_contact');
    }
}