

# Simplifying roles and adding secure User CRUD (project `security09`)

## User CRUD PROBLEM 1: an array of ROLES

If we try to generate CRUD for our system `User` we'll hit a problem when trying to enter a String for the ROLE, since the default Symfony `User` stores roles in an array.

Try it:

- generate CRUD for entity `User`
- try to create a new user
- you'll get an error about string/array mismatch when you enter something like `ROLE_ADMIN` for the new `User` role

## User CRUD PROBLEM 1: a solution

I find the simplest solution is to use a role hierarchy (see previous chapter), so we only need to store a single ROLE string for a `User`.

However, to meet the requirements for the Symfony security system, the `User` class must have a `getRoles()` method that returns an array.

The solution is pretty straightforward:

1. add a new String `role` property to the `User` entity

    - HINT: use `make:entity User` and add the new property
    
1. change the `getRoles()` method to simply return the string `role` wrapped in an array:

    ```php
    public function getRoles(): array
    {
        return [$this->role];
    }
    ```

1. delete the `roles` property and the `setRoles(...)` method from entity `User`

1. update your `UserFixtures` fixtures to set the `role` property - not using arrays ...

```php
    public function load(ObjectManager $manager)
    {
        // create objects
        $userUser = $this->createUser('user@user.com', 'user');
        $userAdmin = $this->createUser('admin@admin.com', 'admin', 'ROLE_ADMIN');
        $userMatt = $this->createUser('matt.smith@smith.com', 'smith', 'ROLE_SUPER_ADMIN');

        // add to DB queue
        $manager->persist($userUser);
        $manager->persist($userAdmin);
        $manager->persist($userMatt);

        // send query to DB
        $manager->flush();

    }

    private function createUser($username, $plainPassword, $role = 'ROLE_USER'):User
    {
        $user = new User();
        $user->setEmail($username);
        $user->setRole($role);
```

1. migrate the DB 

1. load the fixtures

1. delete the old CRUD and create new CRUD

## User CRUD PROBLEM 2: plain test password stored in DB

The default CRUD generation will store in the DB whatever plain text password is entered in the form.

But, the Symfony security systems expects a **hashed** password to be stored in the DB, so we have 2 problems:

1. we should **never** store plain text passwords in the DB

1. we cannot login, since the security system will think the stored text is a bad bash

## User CRUD PROBLEM 2: a solution

We can solve this problem the same way we encoded passwords in our `UserFixtures` - by adding a password encoder, and hashing the plain text password before the object's contents is persisted to the DB.

Do the following to our CRUD controller class `UserController`:

1. add a `use` statement for class `UserPasswordEncoderInterface`
    
    ```php
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    ```
1. for the `new` route we need to add a `$passwordEncoder` to the method arguments (the Symfony param-converter will magically create the object for us to use), and then we can encode the plaintext password and use the `setPassword(...)` method to ensure that it is the **hashed** password stored in the DB:
    
    ```php
    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
    
            // encode password
            $plainPassword = $user->getPassword();
            $encodedPassword = $passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
    
            $entityManager->persist($user);
            $entityManager->flush();
    ```

1. do the same for the **edit** route:

    ```php
    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
    
            // encode password
            $plainPassword = $user->getPassword();
            $encodedPassword = $passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
    
            $this->getDoctrine()->getManager()->flush();
    ```

See Figure \ref{userCrud} to see new user `test@test.com` with correctly stored hashed password.

![Screenshot of Admin User CRUD with stored hased passwords. \label{userCrud}](./03_figures/part06_security/20_userCrud.png)


## Securing the `User` CRUD for `ROLE_ADMIN` only

We can secure all routes in our CRUD `UserController` by:

- adding a `use` statement for the `IsGranted` class

- adding an `@IsGranted` annotation comment immdiately **before** the class delcaration

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
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
    
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
   
    
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/user")
     */
    class UserController extends AbstractController
    {
    ``` 

## Further steps

Rather than typing in text like`ROLE_USER` and `ROLE_ADMIN`, it would be nice to choose them from a dropdown list - via an associated `Role` entity ...