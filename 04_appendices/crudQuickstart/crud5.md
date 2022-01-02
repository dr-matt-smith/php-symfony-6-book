# Add some login security

## Visit the user admin pages

Visit `localhost:8000/user` - you'll see our 2 users for the system. See Figure \ref{user_index}.

    - matt (password: smith), an **admin** user
    
    - john (password: doe), a normal user



![Screenshot of phone make CRUD pages.\label{user_index}](./03_figures/app_crud/user_list.png){ width=100% }

## Secure the user admin behind a firewall

Let's only allow logged in ROLE_ADMIN users to access the user CRUD pages.

We do this by adding a requirement that a user must be logged in and have the ROLE_ADMIN user role. This can all be achieved by adding a single line before the declaration of class file `src/Controller/UserController.php`:

![IsGranted security requirement added to the `UserController` class.](./03_figures/app_crud/is_granted.png){ width=100% }

## Login

If you visit `localhost:8000/user` again you'll now be asked to login. See Figure \ref{login}.

![Login form. \label{login}](./03_figures/app_crud/login_form.png){ width=100% }

If successfully logged in as an admnin user, you can now visit the user CRUD pages. See Figure \ref{user_matt}.
                                                                                  
![User pages logged in as `matt`. \label{user_matt}](./03_figures/app_crud/user_matt.png){ width=100% }

Clicking the user in the debug profiler web page footer gives details about the role(s) of the logged in user. See Figure \ref{user_matt_details}.

![Details of logged-ion user `matt`. \label{user_matt_details}](./03_figures/app_crud/user_matt_details.png){ width=100% }
                                                                                                                                                      
                                                                                                                                                      