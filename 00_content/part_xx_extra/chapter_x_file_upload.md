# Searching

This controller code retrieves `needle` from POST data and uses it to saerch withing the `description` field:

```php

    /**
     * @Route("_process_search", name="process_search")
     *
     * @return Response
     */
    public function search(Request $request)
    {
        $needle = $request->request->get('needle');


//        $chocolates = $this->getDoctrine()
//            ->getRepository(Chocolate::class)
//            ->findBy();

        $em = $this->getDoctrine()->getManager();

        $chocolates = $em->getRepository(Chocolate::class)->createQueryBuilder('chocolate')
            ->where('chocolate.description LIKE :needle')
            ->setParameter('needle', "%$needle%")
            ->getQuery()
            ->getResult();



        return $this->render('chocolate/search_results.html.twig', [
            'chocolates' => $chocolates,
            'needle' => $needle
        ]);
    }
```

Here is a form that could provide the needle:

```twig
    {% extends 'base.html.twig' %}

    {% block title %}New Chocolate{% endblock %}

    {% block body %}

        <h1>Create new Chocolate</h1>

        <form
            action="{{ url('chocolate_process_search') }}"
            method="post"
        >
            search for:
            <input type="text" name="needle">

            <br>
            <input type="submit" value="search">

        </form>

    {% endblock %}
```