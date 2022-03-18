

# User CRUD roles and passwords (project `security07`)

## Generating CRUD for users

Let's generate CRUD for entity `User`:

```bash
        $ symfony console make:crud User
    Choose a name for your controller class (e.g. UserController) [UserController]""
    >        // hit <RETURN> for suggested class name UserController
    created: src/Controller/UserController.php
    created: src/Form/UserType.php
    created: templates/user/_delete_form.html.twig
    created: templates/user/_form.html.twig
    created: templates/user/edit.html.twig
    created: templates/user/index.html.twig
    created: templates/user/new.html.twig
    created: templates/user/show.html.twig
```

Now visit `/user` and you see a list of users, with links to show/edit/delete and create new users.

## User CRUD PROBLEM 1: "array to string" error

But - if you try to edit or create a new user you'll get an "array to string" error. This is related to the way users have an array of role strings.

## User CRUD SOLUTION 1: Refactoring the User form class

When you generate CRUD, one of the classes created is a form class for the entity, so for entity `User` a form class `/src/Form/UserType.php` is created.

It should look something like this:

```php
    class UserType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('email')
                ->add('roles')
                ->add('password')
            ;
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => User::class,
            ]);
        }
    }
```

We need to edit this form class, so a drop-down list of roles is offered, and converted into an array when stored in a `User` object.

First, add 2 `use` statements for classes we need:

```php
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\CallbackTransformer;
```

Next, change method `buildForm(...)` to this - which offers the drop-down (`ChoiceType`) list we want:

```php
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('Roles', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_TEACHER' => 'ROLE_TEACHER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                ],
            ])
            ->add('password')
        ;
    }
```

However, we then have to convert the choice to an array to eb stored in the object. So we need to add a second $bulder statement, with a callback function to do this conversion:

```php
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Roles', ChoiceType::class, [
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'choices' => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_TEACHER' => 'ROLE_TEACHER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                ],
            ])
            ->add('password')
        ;

        $builder->get('Roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    return [$rolesString];
                }
            ));
    }
```

That's it - you should now have an easy to use HTML drop-down menu of ROLES to choose from.

You need to edit the array of roles (from ROLE_USER / ROLE_ADMIN / ROLE_TEACHER) to whatever is suitable for **your** Symfony project.

See Figure \ref{security07} for a screenshot of the new/edit user form.

![User form allowing multiple-role selections. \label{security07}](./03_figures/part06_security/31_rolesDropdownForm.png){ width=50% }



## User CRUD PROBLEM 2: plain text password stored in DB

The default CRUD generation will store in the DB whatever plain text password is entered in the form.

See Figure \ref{security07b} for a screenshot of the new/edit user form.

![CRUD form passwords stored as plain text (not hashed!). \label{security07b}](./03_figures/part06_security/32_unhashedPasswords.png){ width=75% }



But, the Symfony security systems expects a **hashed** password to be stored in the DB, so we have 2 problems:

1. we should **never** store plain text passwords in the DB

1. we cannot login, since the security system will think the stored text is a bad bash

## User CRUD PROBLEM 2: a solution

We can solve this problem the same way we encoded passwords for our fixtures - by creating a password hasher object, and hashing the plain text password before the object's contents is persisted to the DB.

Do the following to our CRUD controller class `UserController`:

1. add a `use` statement for class `UserPasswordHasherInterface`
    
    ```php
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    ```

1. declare a private instance property `passwordHasher`, and initialise this in the constructor via the param-converter:

   ```php
        private UserPasswordHasherInterface $passwordHasher;

        public function __construct(UserPasswordHasherInterface $passwordHasher)
        {
            $this->passwordHasher = $passwordHasher;
        }
   ```


1. for the `new` route we need to add a `$passwordHasher` to the method arguments (the Symfony param-converter will magically create the object for us to use), and then we can encode the plaintext password and use the `setPassword(...)` method to ensure that it is the **hashed** password stored in the DB:
    
    ```php
        #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
        public function new(Request $request, UserRepository $userRepository): Response
        {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $plainPassword = $user->getPassword();
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $userRepository->add($user);
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('user/new.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        }
    ```

1. do the same for the **edit** route:

    ```php
        #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
        public function edit(
            Request $request,
            User $user,
            UserRepository $userRepository): Response
        {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $plainPassword = $user->getPassword();
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $userRepository->add($user);
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('user/edit.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        }
    ```

See Figure \ref{userCrud} to see new user `test@test.com` with correctly stored hashed password.

![Screenshot of Admin User CRUD with stored hashed passwords. \label{userCrud}](./03_figures/part06_security/20_userCrud.png)


## Securing the `User` CRUD for `ROLE_ADMIN` only

We can secure all routes in our CRUD `UserController` by:

- adding a `use` statement for the `IsGranted` class

- adding an `#[IsGranted]` attribute immediately **before** the class declaration

    ```php
        <?php

        namespace App\Controller;

        use App\Entity\User;
        use App\Form\UserType;
        use App\Repository\UserRepository;
        use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
        use Symfony\Component\HttpFoundation\Request;
        use Symfony\Component\HttpFoundation\Response;
        use Symfony\Component\Routing\Annotation\Route;

        use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
        use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

        #[IsGranted('ROLE_ADMIN')]
        #[Route('/user')]
        class UserController extends AbstractController
        {
    ``` 
ROLE_ADMIN`, it would be nice to choose them from a dropdown list - via an associated `Role` entity ...