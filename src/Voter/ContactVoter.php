<?php


namespace App\Voter;


use App\Entity\Contact;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ContactVoter extends Voter
{
    protected function supports(string $attribute, $subject)
    {
        if($subject instanceof Contact){
            return true;
        }
        return false;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        return $subject->getGroupes()[0]->getUser() == $user;
    }
}