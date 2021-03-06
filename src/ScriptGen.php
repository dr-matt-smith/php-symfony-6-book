<?php

namespace Mattsmithdev;

class ScriptGen
{
    const SCRIPT = '
        "updateme": "php updateComposerJson.php",
    ';

    const NAMESPACE = '
      "autoload": {
    "psr-4": {
      "Mattsmithdev\\": "src"
    }
  }';

    const PDF =[
        'pandoc  --pdf-engine=xelatex  -H 01_front_material/preamble.tex   -V documentclass:book -V classoption:openright -V papersize:a4paper --bibliography=05_references/references.bib  01_front_material/1_title.md 01_front_material/4_acknowledgements.md 01_front_material/toc.md',

        '00_content/0_main_matter.md',

        '00_content/part01/part01.md',
        '00_content/part01/chapter01.md',
        '00_content/part01/chapter02.md',
        '00_content/part01/chapter03_twig.md',
        '00_content/part01/chapter04_classes.md',

        '00_content/part02/part02.md',
        '00_content/part02/chapter05_orm.md',
        '00_content/part02/chapter06_entity_classes.md',
        '00_content/part02/chapter07_crud.md',
        '00_content/part02/chapter08_fixtures.md',
        '00_content/part02/chapter08b_related_fixtures.md',
        '00_content/part02/chapter09_foundry_fixtures.md',

        '00_content/part03_forms/part03.md',
        '00_content/part03_forms/chapter09_diy_forms.md',
        '00_content/part03_forms/chapter10_forms_from_entities.md',
        '00_content/part03_forms/chapter11_bootstrap.md',
        '00_content/part03_forms/chapter12_customize_forms.md',

        '00_content/part04_crud_gen/part04.md',
        '00_content/part04_crud_gen/chapter12_crud_gen.md',

        '00_content/part05_formsForQueries/part05.md',
        '00_content/part05_formsForQueries/dbQuery01.md',
        '00_content/part05_formsForQueries/dbQuery02_helperMethods.md',
        '00_content/part05_formsForQueries/dbQuery03_form.md',

        '00_content/part06_sessions/part05.md',
        '00_content/part06_sessions/chapter13_sessionPrep.md',
        '00_content/part06_sessions/chapter14_simpleSessions.md',
        '00_content/part06_sessions/chapter15_basket.md',

        '00_content/part07_associations/part07.md',
        '00_content/part07_associations/chapter19.md',
        '00_content/part07_associations/chapter19b_linkedFixtures.md',
        '00_content/part07_associations/chapter20.md',
        '00_content/part07_associations/chapter21_user_as_author.md',

        '00_content/part08_security/part06.md',
        '00_content/part08_security/chapter15.md ',
        '00_content/part08_security/chapter16_userFixtures.md ',
        '00_content/part08_security/chapter17_customLogin.md ',
        '00_content/part08_security/chapter18_accessDenied.md ',
        '00_content/part08_security/chapter18_twigLogger.md',
        '00_content/part08_security/security6_twig.md ',
        '00_content/part08_security/chapter20_userCrud.md',

        // ROLE HIERACHY
//        '00_content/part08_security/security5_user_roles.md',

        '00_content/part08_testing/part08.md',
        '00_content/part08_testing/testing1.md',
        '00_content/part08_testing/testing3_web_controllers.md',
        '00_content/part08_testing/testing3_web2_links.md',
        '00_content/part08_testing/testing4_forms.md',
        '00_content/part08_testing/testing5_dbTesting.md',
        '00_content/part08_testing/testing6_userLogins.md',

        '00_content/part08_testing/testing2_coverage.md ',

        '00_content/part11_publish/part.md',
        '00_content/part11_publish/publish.md',
        '00_content/part11_publish/fortrabbit_setup.md',
        '00_content/part11_publish/fortrabbit.md',

        '04_appendices/0_appendices.md',
        '04_appendices/1_software_tools.md',
        '04_appendices/2_php_setup.md',
        '04_appendices/2_composer.md',
        '04_appendices/2b_symfonyCLI.md',
        '04_appendices/3_software_setup.md',
        '04_appendices/4_sf_demo.md',
        '04_appendices/problem_solving.md',
        '04_appendices/99_publishFortrabbit.md',
        '04_appendices/new_project.md',
        '04_appendices/from_git_to_webpage.md',
        '04_appendices/parameters_and_config.md',
        '04_appendices/db_mysql.md',
        '04_appendices/db_sqlite.md',
        '04_appendices/db_gui_adminer.md',
        '04_appendices/entity_names.md',
        '04_appendices/entity_gen_interactive.md',
        '04_appendices/osx_kill_php_process.md',
        '04_appendices/docker_symfony.md',
        '04_appendices/xdebug.md',

        '05_references/references.md',

        '-o _OUTPUT_PDF/_BOOK_current_draft.pdf'
    ];

    private array $crud = [
        'pandoc  --pdf-engine=xelatex  -H 01_front_material/preamble.tex   -V documentclass:book -V classoption:openright -V papersize:a4paper --bibliography=05_references/references.bib  04_appendices/crudQuickstart/crud1.md 04_appendices/crudQuickstart/crud1b.md 04_appendices/crudQuickstart/crud1c.md 04_appendices/crudQuickstart/crud2.md 04_appendices/crudQuickstart/crud3.md 04_appendices/crudQuickstart/crud4.md 04_appendices/crudQuickstart/crud5.md 04_appendices/crudQuickstart/crud6_foundry.md 04_appendices/crudQuickstart/crud7_homepage.md 04_appendices/0_appendices.md  04_appendices/1_software_tools.md   04_appendices/2_php_setup.md  04_appendices/2_composer.md 04_appendices/2b_symfonyCLI.md  04_appendices/3_software_setup.md  -o _OUTPUT_PDF/__CRUD_quickstart.pdf'
    ];

    private array $pdf2021 = [
        'pandoc  --pdf-engine=xelatex  -H 01_front_material/preamble.tex   -V documentclass:book -V classoption:openright -V papersize:a4paper --bibliography=05_references/references.bib  01_front_material/1_title.md 01_front_material/4_acknowledgements.md 01_front_material/toc.md 00_content/0_main_matter.md 00_content/part01/part01.md 00_content/part01/chapter01.md   00_content/part01/chapter02.md  00_content/part01/chapter03_twig.md  00_content/part01/chapter04_classes.md  00_content/part02/part02.md  00_content/part02/chapter05_orm.md  00_content/part02/chapter06_entity_classes.md  00_content/part02/chapter07_crud.md  00_content/part02/chapter08_fixtures.md  00_content/part02/chapter08b_related_fixtures.md   00_content/part02/chapter09_foundary_fixtures.md  00_content/part03_forms/part03.md  00_content/part03_forms/chapter09_diy_forms.md 00_content/part03_forms/chapter10_forms_from_entities.md  00_content/part03_forms/chapter11_bootstrap.md  00_content/part03_forms/chapter12_customize_forms.md  00_content/part03b_formsForQueries/part03b.md 00_content/part03b_formsForQueries/dbQuery01.md 00_content/part03b_formsForQueries/dbQuery02_form.md 00_content/part04/part04.md   00_content/part04/chapter12_crud_gen.md 00_content/part06_sessions/part05.md 00_content/part06_sessions/chapter14_simpleSessions.md  00_content/part06_sessions/chapter15_basket.md 00_content/part08_security/part06.md 00_content/part08_security/chapter15.md  00_content/part08_security/chapter16_userFixtures.md  00_content/part08_security/chapter17_customLogin.md  00_content/part08_security/chapter18_accessDenied.md  00_content/part08_security/chapter18_twigLogger.md 00_content/part08_security/security5_user_roles.md 00_content/part08_security/security6_twig.md  00_content/part08_security/chapter20_userCrud.md 00_content/part07_associations/part07.md 00_content/part07_associations/chapter19.md  00_content/part07_associations/chapter20.md  00_content/part07_associations/chapter21_user_as_author.md 00_content/part09_documentor/chapter.md 00_content/part10_codeception/part.md 00_content/part10_codeception/testing1.md  00_content/part10_codeception/testing3_acceptance.md 00_content/part10_codeception/testing4_forms.md 00_content/part10_codeception/testing5_db.md 00_content/part10_codeception/testing6_roles.md 00_content/part10_codeception/testing7_crudUsers.md 00_content/part11_publish/part.md 00_content/part11_publish/publish.md 00_content/part11_publish/fortrabbit_setup.md 00_content/part11_publish/fortrabbit.md 00_content/part08_testing/part08.md 00_content/part08_testing/testing1.md   00_content/part08_testing/testing2_coverage.md    00_content/part08_testing/testing3_controllers.md  00_content/part08_testing/testing4_forms.md   04_appendices/0_appendices.md  04_appendices/1_software_tools.md   04_appendices/2_php_setup.md  04_appendices/2_composer.md  04_appendices/2b_symfonyCLI.md  04_appendices/3_software_setup.md  04_appendices/4_sf_demo.md  04_appendices/problem_solving.md  04_appendices/99_publishFortrabbit.md  04_appendices/new_project.md  04_appendices/from_git_to_webpage.md  04_appendices/parameters_and_config.md   04_appendices/db_mysql.md 04_appendices/db_sqlite.md   04_appendices/db_gui_adminer.md   04_appendices/entity_names.md 04_appendices/entity_gen_interactive.md  04_appendices/osx_kill_php_process.md 04_appendices/docker_symfony.md  04_appendices/xdebug.md  05_references/references.md -o _OUTPUT_PDF/_BOOK_current_draft.pdf'
        ];


    const FILE_PATH = __DIR__ . '/../composer.json';

    public function __construct()
    {
        $data1 =
            [
                "scripts" => [
                    "updateme" => "php updateComposerJson.php",
                    "pdf" => $this->pdfToString()
                ],
                "autoload" => [
                    "psr-4" => [
                        'Mattsmithdev\\' => "src"
                    ]
                ]
            ];

        $json = json_encode($data1, JSON_PRETTY_PRINT);
        $json = str_replace('\/', '/', $json);
        print $json;
        file_put_contents(self::FILE_PATH, $json);
    }


    public function pdfToString(): string
    {
        return implode(' ',self::PDF);
    }

}