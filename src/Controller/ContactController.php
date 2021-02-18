<?php


namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Telephone;
use App\Form\ContactType;
use App\Form\DeleteForm;
use App\Form\TelephoneType;
use App\Repository\ContactRepository;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class ContactController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ContactRepository
     */
    private $contactRepository;
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(EntityManagerInterface $entityManager, ContactRepository $contactRepository, PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->contactRepository = $contactRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("contact/create", name="create_contact")
     */
    public function create(Request $request)
    {
        $contact = new Contact();
        $user = $this->getUser();
        $groups = $user->getGroupes();
        $form = $this->createForm(ContactType::class, $contact, ['groupes' => $groups]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($contact);
            $this->entityManager->flush();
            return $this->redirectToRoute("details_contact", ['id' => $contact->getId()]);
        }
        return $this->render("contact/create.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("contact/{id}", name="details_contact", requirements={"id"="\d+"})
     */
    public function details(Request $request, int $id)
    {
        $contact = $this->getContact($id);
        $this->denyAccessUnlessGranted('', $contact);
        $telephone = new Telephone();
        $telephone->setContact($contact);
        $form = $this->createForm(TelephoneType::class, $telephone);
        $form->handleRequest($request);

        $deleteForm = $this->createForm(DeleteForm::class);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $this->entityManager->remove($contact);
            $this->entityManager->flush();
            return $this->redirectToRoute('list_contact');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($telephone);
            $this->entityManager->flush();
            return $this->redirectToRoute('details_contact', ["id" => $id]);
        }
        return $this->render("contact/details.html.twig", [
            "contact" => $contact,
            'form' => $form->createView(),
            'deleteForm' => $deleteForm->createView()
        ]);
    }

    private function getContact(int $id)
    {
        $contact = $this->contactRepository->find($id);
        if ($contact->getGroupes()[0]->getUser() != $this->getUser()) {
            throw new AccessDeniedHttpException();
        }
        if (!$contact) {
            throw new NotFoundHttpException("Contact introuvable");
        }
        return $contact;
    }

    /**
     * @Route("contact", name="list_contact")
     */
    public function listContact(Request $request)
    {
        $list = $this->contactRepository->getContactByUser($this->getUser());
        $pagination = $this->paginator->paginate(
            $list,
            $request->query->get('page', 1),
            10
        );
        return $this->render("contact/list.html.twig", ['contacts' => $pagination]);
    }

    /**
     * @Route("contact_dt", name="list_contact_dt")
     */
    public function listContactDatatable(Request $request)
    {
        $list = $this->contactRepository->getContactByUser($this->getUser());
        return $this->render("contact/list_datatable.html.twig", ['contacts' => $list]);
    }

    /**
     * @Route("search", name="search_contact")
     */
    public function search(Request $request){
        $searchTerm = $request->query->get('search');
        $list = $this->contactRepository->search($searchTerm);
        return $this->render("contact/list_datatable.html.twig", ['contacts' => $list]);
    }


    /**
     * @Route("contact/{id}/update", name="update_contact", requirements={"id"="\d+"})
     */
    public function updateContact(Request $request, int $id)
    {
        $contact = $this->getContact($id);
        $user = $this->getUser();
        $groups = $user->getGroupes();
        $form = $this->createForm(ContactType::class, $contact, ['groupes' => $groups]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($contact);
            $this->entityManager->flush();
            return $this->redirectToRoute("details_contact", ['id' => $contact->getId()]);
        }
        return $this->render("contact/create.html.twig", ['form' => $form->createView(), 'contact' => $contact]);
    }
}