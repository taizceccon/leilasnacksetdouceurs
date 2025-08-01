<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Repository\UserRepository;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormError;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    // #[Route('/register', name: 'app_register')]
    // public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    // {
    //     $email = $request->query->get('email', '');
    //     $user = new User();
    //     if ($email) {
    //         $user->setEmail($email);
    //     }
    //     $form = $this->createForm(RegistrationForm::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         // Vérifier si l'email est déjà pris
    //         $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);
    //         if ($existingUser) {
    //             $this->addFlash('error', 'Cette adresse e-mail est déjà utilisée.');
    //             return $this->redirectToRoute('app_register');
    //         }

    //         /** @var string $plainPassword */
    //         $plainPassword = $form->get('plainPassword')->getData();

    //         $user->setPassword(
    //             $userPasswordHasher->hashPassword($user, $plainPassword)
    //         );

    //         $user->setIsVerified(true);
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         // Envoyer email de confirmation (optionnel selon vos besoins)
    //         $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
    //             (new TemplatedEmail())
    //                 ->from(new Address('tzlogicsolutions@gmail.com', 'leila sd'))
    //                 ->to($user->getEmail())
    //                 ->subject('Please Confirm your Email')
    //                 ->htmlTemplate('registration/confirmation_email.html.twig')
    //         );

    //         // Ajouter message flash de succès
    //         $this->addFlash('success', 'Votre compte a bien été créé. Vous pouvez maintenant vous connecter.');

    //         // Redirection vers la page de connexion
    //         return $this->redirectToRoute('app_login');
    //     }

    //     return $this->render('registration/register.html.twig', [
    //         'registrationForm' => $form,
    //     ]);
    // }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ValidatorInterface $validator // inject the validator
    ): Response {
        $email = $request->query->get('email', '');
        $user = new User();
        if ($email) {
            $user->setEmail($email);
        }
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Manually validate plainPassword here
            $plainPassword = $form->get('plainPassword')->getData();
            // $passwordErrors = $validator->validate($plainPassword, [
            //     new Assert\NotBlank(['message' => 'Please enter a password']),
            //     new Assert\Length(['min' => 6, 'minMessage' => 'Your password should be at least {{ limit }} characters']),
            // ]);

            // Add errors to the form if any
            // foreach ($passwordErrors as $error) {
            //     $form->get('plainPassword')->addError(new FormError($error->getMessage()));
            // }

            // Now check form validity including password validation
            if ($form->isValid()) {
                // Check if email already exists
                $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);
                if ($existingUser) {
                    $this->addFlash('error', 'Cette adresse e-mail est déjà utilisée.');
                    return $this->redirectToRoute('app_register');
                }

                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $plainPassword)
                );

                $user->setIsVerified(true);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('tzlogicsolutions@gmail.com', 'leila sd'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );

                $this->addFlash('success', 'Votre compte a bien été créé. Vous pouvez maintenant vous connecter.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_login');
    }
}