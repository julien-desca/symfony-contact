<?php


namespace App\Form;


use App\Entity\Groupe;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ContactType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groups = $options['groupes'];
        $builder->add('prenom', TextType::class, [
            'label'=>'prénom',
        ]);
        $builder->add('nom', TextType::class, ['required'=>false]);
        $builder->add('groupes', EntityType::class, [
            'class'=>Groupe::class,
            'choices'=>$groups,
            'choice_label'=>'nom',
            'multiple' => true,
            'expanded'=>true,
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('groupes', []);
    }


}