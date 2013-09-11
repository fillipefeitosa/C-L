<?php
include 'estruturas.php';
include_once 'auxiliar_algoritmo.php';
session_start();
?>

<html>
    <head>
        <title>Algoritmo de Gera&ccedil;&atilde;o de Ontologias</title>
        <style>

        </style>
    </head>
    <body>
        <?php

        function verify_consistency() {
            return TRUE;
        }

        function compare_arrays($array1, $array2) {

            if (count($array1) != count($array2)) {
                return FALSE;
            }

            foreach ($array1 as $key => $elem) {
                if ($elem->verbo != $array2[$key]->verbo) {
                    return FALSE;
                }
            }
            return TRUE;
        }

        /*
          Scenario:	Mounts hierarchy.
          Objective:	Mounts hierarchy os the concepts.
          Context:	organize the ontology in progress.
          Actors:
          Resources:	System, concept, list of subconcepts, list of concepts.
          Episodes:
          - For each subconcept
         * Look for your key in the concept list.
         * Add a key as a subconcept of a concept.
         */

        function mount_hierarchy($concept, $nova_lista, $list) {
            foreach ($nova_lista as $subcon) {
                $key = concept_exists($subcon, $list);
                $concept->subconceitos[] = $subcon;
            }
        }

        /*
          Scenario:	Translate the terms of the lexicon classified as subject and object.
          Objective:	Translate the terms of the lexicon classified as subject and object.
          Context:	Translation algorithm initialized.
          Actors:	User.
          Resources:	System, list of subjects and objects, list of concepts, list of relations.
          Episodes:
          - For each element of the subject and object list
         * Create a new concept whith the same name and the description.
         * For each impact of the element
          . Verify whith the user the existence of the impact in the relations list.
          . If it doesnt exists, include this impact in the relation list.
          . Include this relation in the relation list of the concept.
          . Discover
         * Include a concept in the concept list.
         * Verify consistency.
         */

        function translate_subject_object($subject_and_object_list, $concepts, $relations, $axioms) {

            for (; $_SESSION["index1"] < count($subject_and_object_list); ++$_SESSION["index1"]) {

                $suj = $subject_and_object_list[$_SESSION["index1"]];

                if (!isset($_SESSION["conceito"])) {
                    $_SESSION["salvar"] = "TRUE";
                    $_SESSION["conceito"] = new conceito($suj->nome, $suj->nocao);
                    $_SESSION["conceito"]->namespace = "proprio";
                } else {
                    $_SESSION["salvar"] = "FALSE";
                }


                for (; $_SESSION["index2"] < count($suj->impacto); ++$_SESSION["index2"]) {

                    $imp = $suj->impacto[$_SESSION["index2"]];

                    if (trim($imp) == "")
                        continue;

                    if (!isset($_SESSION["verbos_selecionados"]))
                        $_SESSION["verbos_selecionados"] = array();

                    if (!isset($_SESSION["impact"])) {
                        $_SESSION["impact"] = array();
                        $_SESSION["finish_insert"] = FALSE;
                    }
                    while (!$_SESSION["finish_insert"]) {
                        if (!isset($_SESSION["exist"])) {
                            asort($relations);
                            $_SESSION["lista"] = $relations;
                            $_SESSION["nome1"] = $imp;
                            $_SESSION["nome2"] = $suj;
                            $_SESSION["job"] = "exist";
                            ?>
                            <SCRIPT language='javascript'>
                                document.location = "auxiliar_interface.php";
                            </SCRIPT>



                            <?php
                            exit();
                        }



                        if ($_POST["existe"] == "FALSE") {

                            $nome = strtolower($_POST["nome"]);
                            session_unregister("exist");
                            if ((count($_SESSION["verbos_selecionados"]) != 0) && (array_search($nome, $_SESSION["verbos_selecionados"]) !== null)) {
                                continue;
                            }
                            $_SESSION["verbos_selecionados"][] = $nome;
                            $i = array_search($nome, $relations);
                            if ($i === false) {
                                $_SESSION["impact"][] = (array_push($relations, $nome) - 1);
                            } else {
                                $_SESSION["impact"][] = $i;
                            }
                        } else if ($_POST["indice"] != -1) {
                            session_unregister("exist");
                            if ((count($_SESSION["verbos_selecionados"]) != 0) && array_search($relations[$_POST["indice"]], $_SESSION["verbos_selecionados"]) !== false) {
                                continue;
                            }
                            $_SESSION["verbos_selecionados"][] = $relations[$_POST["indice"]];
                            $_SESSION["impact"][] = $_POST["indice"];
                        } else {
                            $_SESSION["finish_insert"] = TRUE;
                        }
                    }

                    if (!isset($_SESSION["ind"])) {
                        $_SESSION["ind"] = 0;
                    }

                    $_SESSION["verbos_selecionados"] = array();

                    for (; $_SESSION["ind"] < count($_SESSION["impact"]); ++$_SESSION["ind"]) {

                        if (!isset($_SESSION["predicados_selecionados"]))
                            $_SESSION["predicados_selecionados"] = array();

                        $indice = $_SESSION["impact"][$_SESSION["ind"]];
                        $_SESSION["finish_relation"] = FALSE;
                        while (!$_SESSION["finish_relation"]) {
                            if (!isset($_SESSION["insert_relation"])) {
                                asort($concepts);
                                $_SESSION["lista"] = $concepts;
                                $_SESSION["nome1"] = $relations[$indice];
                                $_SESSION["nome2"] = $suj->nome;
                                $_SESSION["nome3"] = $imp;
                                $_SESSION["job"] = "insert_relation";
                                ?>
                                <SCRIPT language='javascript'>
                                    document.location = "auxiliar_interface.php";
                                </SCRIPT>
                                <?php
                                exit();
                            } else if (isset($_SESSION["nome2"])) {

                                session_unregister("nome2");
                                session_unregister("nome3");
                                session_unregister("insert_relation");


                                if ($_POST["existe"] == "FALSE") {
                                    $concept = strtolower($_POST["nome"]);

                                    if ((count($_SESSION["predicados_selecionados"]) != 0) && (array_search($concept, $_SESSION["predicados_selecionados"]) !== null)) {
                                        continue;
                                    }
                                    $_SESSION["predicados_selecionados"][] = $concept;

                                    if (concept_exists($concept, $_SESSION['lista_de_conceitos']) == -1) {
                                        if (concept_exists($concept, $subject_and_object_list) == -1) {
                                            $nconc = new conceito($concept, "");
                                            $nconc->namespace = $_POST['namespace'];
                                            $_SESSION['lista_de_conceitos'][] = $nconc;
                                        }
                                    }

                                    $ind_rel = existe_relacao($_SESSION['nome1'], $_SESSION['conceito']->relacoes);
                                    if ($ind_rel != -1) {
                                        if (array_search($concept, $_SESSION["conceito"]->relacoes[$ind_rel]->predicados) === false)
                                            $_SESSION["conceito"]->relacoes[$ind_rel]->predicados[] = $concept;
                                    }
                                    else {
                                        $_SESSION["conceito"]->relacoes[] = new relacao_entre_conceitos($concept, $_SESSION["nome1"]);
                                    }
                                } else if ($_POST["indice"] != "-1") {
                                    $concept = $concepts[$_POST["indice"]]->nome;
                                    if ((count($_SESSION["predicados_selecionados"]) != 0) && (array_search($concept, $_SESSION["predicados_selecionados"]) !== null)) {
                                        continue;
                                    }

                                    $_SESSION["predicados_selecionados"][] = $concept;

                                    $ind_rel = existe_relacao($_SESSION['nome1'], $_SESSION['conceito']->relacoes);
                                    if ($ind_rel != -1) {
                                        if (array_search($concept, $_SESSION["conceito"]->relacoes[$ind_rel]->predicados) === false)
                                            $_SESSION["conceito"]->relacoes[$ind_rel]->predicados[] = $concept;
                                    }
                                    else {
                                        $_SESSION["conceito"]->relacoes[] = new relacao_entre_conceitos($concept, $_SESSION["nome1"]);
                                    }
                                } else {
                                    $_SESSION["finish_relation"] = TRUE;
                                }
                            }
                        }
                        $_SESSION["predicados_selecionados"] = array();
                    }


                    /* Unregister a global variable from the current session */
                    session_unregister("exist");
                    session_unregister("impact");
                    session_unregister("ind");
                    session_unregister("insert_relation");
                    session_unregister("insert");
                    session_unregister("verbos_selecionados");
                    session_unregister("predicados_selecionados");
                }

                $finish_disjoint = FALSE;
                while (!$finish_disjoint) {
                    if (!isset($_SESSION["axiomas_selecionados"]))
                        $_SESSION["axiomas_selecionados"] = array();

                    if (!isset($_SESSION["disjoint"])) {
                        $_SESSION["lista"] = $concepts;
                        $_SESSION["nome1"] = $_SESSION["conceito"]->nome;
                        $_SESSION["job"] = "disjoint";
                        ?>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                        <?php
                        exit();
                    }
                    if ($_POST["existe"] == "TRUE") {
                        $axioma = $_SESSION["conceito"]->nome . " disjoint " . strtolower($_POST["nome"]);
                        if (array_search($axioma, $axioms) === false) {
                            $axioms[] = $axioma;
                            $_SESSION["axiomas_selecionados"][] = $axioma;
                        }
                        session_unregister("disjoint");
                    } else {
                        $finish_disjoint = TRUE;
                    }
                }
                $_SESSION["axiomas_selecionados"] = array();

                $concepts[] = $_SESSION["conceito"];
                asort($concepts);

                if (!verify_consistency()) {
                    exit();
                }

                session_unregister("insert");
                session_unregister("disjoint");
                session_unregister("exist");
                session_unregister("insert_relation");
                session_unregister("conceito");
                $_SESSION["index2"] = 0;
            }
            $_SESSION["index1"] = 0;
            session_unregister("finish_insert");
            session_unregister("finish_relation");
        }

        /*
          Cenario:	Traduzir os termos do lexico classificados como verbo.
          Objetivo:	Traduzir os termos do lexico classificados como verbo.
          Contexto:	Algoritmo de tradu��o iniciado.
          Atores:		Usuario.
          Recursos:	Sistema, lista de verbo, lista de relacoes.
          Episodios:
          - Para cada elemento da lista de verbo
         * Verificar com o usuario a existencia do verbo na lista de relacoes.
         * Caso n�o exista, incluir este verbo na lista de relacoes.
         * Verificar consistencia.
         */

        function translate_verbs($verbos, $relations) {
            for (; $_SESSION["index3"] < count($verbos); ++$_SESSION["index3"]) {

                $verbo = $verbos[$_SESSION["index3"]];


                if (!isset($_SESSION["exist"])) {
                    $_SESSION["salvar"] = "TRUE";
                    asort($relations);
                    $_SESSION["lista"] = $relations;
                    $_SESSION["nome1"] = $verbo->nome;
                    $_SESSION["nome2"] = $verbo;
                    $_SESSION["job"] = "exist";
                    ?>
                    <SCRIPT language='javascript'>
                        document.location = "auxiliar_interface.php";
                    </SCRIPT>
                    <?php
                    exit();
                }

                if ($_POST["existe"] == "FALSE") {
                    $nome = strtolower($_POST["nome"]);
                    if (array_search($nome, $relations) === false)
                        array_push($relations, $nome);
                }


                //	$lista_de_relacoes = $_SESSION["lista"];

                if (!verify_consistency()) {
                    exit();
                }

                session_unregister("exist");
                session_unregister("insert");
            }
            $_SESSION["index3"] = 0;
        }

        /*
          Cenario:	Traduzir os termos do lexico classificados como estado.
          Objetivo:	Traduzir os termos do lexico classificados como estado.
          Contexto:	Algoritmo de traducao iniciado.
          Atores:		Usuario.
          Recursos:	Sistema, lista de estado, lista de conceitos, lista de relacoes, lista de axiomas.
          Episodios:
          - Para cada elemento da lista de estado
         * Para cada impacto do elemento
          . Descobrir
         * Verificar se o elemento possui importancia central na ontologia.
         * Caso tenha, traduza como se fosse um sujeito/objeto.
         * Caso contrario, traduza como se fosse um verbo.
         * Verificar consistencia.
         */

        function traduz_estados($estados, $concepts, $relations, $axioms) {
            for (; $_SESSION["index4"] < count($estados); ++$_SESSION["index4"]) {

                $estado = $estados[$_SESSION["index4"]];


                $aux = array($estado);

                if (!isset($_SESSION["main_subject"])) {

                    $_SESSION["nome1"] = $estado->nome;
                    $_SESSION["nome2"] = $estado;
                    $_SESSION["job"] = "main_subject";
                    ?>
                    <p>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                    <?php
                    exit();

                    //$rel = exist($verbo->nome, $lista_de_relacoes);
                }


                if (!isset($_SESSION["translate"])) {
                    if ($_POST["main_subject"] == "TRUE") {
                        $_SESSION["translate"] = 1;
                        translate_subject_object($aux, &$concepts, &$relations, &$axioms);
                    } else {
                        $_SESSION["translate"] = 2;
                        translate_verbs($aux, &$relations);
                    }
                } else if ($_SESSION["translate"] == 1) {
                    translate_subject_object($aux, &$concepts, &$relations);
                } else if ($_SESSION["translate"] == 2) {
                    translate_verbs($aux, &$relations);
                }



                if (!verify_consistency()) {
                    exit();
                }

                session_unregister("main_subject");
                session_unregister("translate");
            }
            $_SESSION["index4"] = 0;
        }

        /*
          Cenario:	Organizar ontologia.
          Objetivo:	Organizar ontologia.
          Contexto:	Listas de conceitos, relacoes e axiomas prontas.
          Atores:		Usuario.
          Recursos:	Sistema, lista de conceitos, lista de relacoes, lista de axiomas.
          Episodios:
          - Faz-se uma copia da lista de conceitos.
          - Para cada elemento x da lista de conceitos
         * Cria-se uma nova lista contendo o elemento x.
         * Para cada elemento subsequente y
          . Compara as relacoes dos elementos x e y.
          . Caso possuam as mesmas relacoes, adiciona-se o elemento y a nova lista que ja contem x.
          . Retira-se y da lista de conceitos.
         * Retira-se x da lista de conceitos.
         * Caso a nova lista tenha mais de dois elementos, ou seja, caso x compartilhe as mesmas
          relacoes com outro termo
          . Procura por um elemento na lista de conceitos que faca referencia a todos os elementos
          da nova lista.
          . Caso exista tal elemento, montar hierarquia.
          . Caso nao exista, descobrir.
         * Verificar consistencia.
          - Restaurar lista de conceitos.
         */

        function organizar_ontologia($concepts, $relations, $axioms) {
            $_SESSION["salvar"] = "TRUE";
            /* for( ; $_SESSION["index5"] < count($concepts); ++$_SESSION["index5"] )
              {
              $_SESSION["salvar"] = "TRUE";

              $concept = $concepts[$_SESSION["index5"]];

              if( count($concept->subconceitos) > 0 )
              {
              if( $concept->subconceitos[0] == -1 )
              {
              array_splice($concept->subconceitos, 0, 1);
              continue;
              }
              }

              $concept->subconceitos[0] = -1;
              $key = $_SESSION["index5"];

              $nova_lista_de_conceitos = array($concept);

              for( $i = $key+1; $i < count($concepts); ++$i )
              {
              if (compare_arrays($concept->relacoes, $concepts[$i]->relacoes))
              {
              $concepts[$i]->subconceitos[0] = -1;
              $nova_lista_de_conceitos[] = $concepts[$i];
              }
              }
             */
            //if( count($nova_lista_de_conceitos) >= 2 )

            $finish_relation = FALSE;
            while (!$finish_relation) {
                $indice = 0;

                if (!isset($_SESSION["reference"])) {

                    $_SESSION["lista"] = $concepts; //array($conc1, $nconc);
                    //$_SESSION['nome1'] = $nova_lista_de_conceitos;//
                    $_SESSION["job"] = "reference";
                    ?>
                        <a href="auxiliar_interface.php">auxiliar_interface</a>
                        <SCRIPT language='javascript'>
                            document.location = "auxiliar_interface.php";
                        </SCRIPT>
                        <?php
                        exit();

                        //$rel = exist($verbo->nome, $lista_de_relacoes);
                    }

                    session_unregister("reference");

                    $achou = FALSE;

                    if (isset($_POST['pai'])) {
                        $pai_nome = $_POST['pai'];
                        $key2 = concept_exists($pai_nome, $concepts);
                        $filhos = array();
                        foreach ($concepts as $key3 => $filho) {
                            $filho_nome = trim($filho->nome);
                            if (isset($_POST[$key3])) {
                                $filhos[] = $filho_nome;
                            }
                        }
                        if (count($filhos) > 0) {
                            mount_hierarchy(&$concepts[$key2], $filhos, $concepts);
                            $achou = true;
                        }
                    } else {
                        $finish_relation = true;
                    }


                    if (!$achou) {
                        //tentar montar hierarquia pelo vocabulario minimo.
                    }
                }

                if (!verify_consistency()) {
                    exit();
                }
                //array_splice($concept->subconceitos, 0, 1);
                //}
                //$_SESSION["index5"] = 0;
            }

            /*
              Cenario:  	Traduzir L�xico para Ontologia.
              Objetivo: 	Traduzir L�xico para Ontologia.
              Contexto: 	Existem listas de elementos do l�xico organizadas por tipo, e estes elementos
              s�o consistentes.
              Atores:   	Usu�rio.
              Recursos: 	Sistema, listas de elementos do l�xico organizadas por tipo, listas de elementos
              da ontologia.
              Epis�dios:
              - Criar lista de conceitos vazia.
              - Criar lista de relacoes vazia.
              - Criar lista de axiomas vazia.
              - Traduzir os termos do lexico classificados como sujeito e objeto.
              - Traduzir os termos do lexico classificados como verbo.
              - Traduzir os termos do lexico classificados como estado.
              - Organizar a ontologia.

             */

            function traduz() {
                //Verifica se as listas foram iniciadas.
                if (isset($_SESSION["lista_de_sujeito"]) && isset($_SESSION["lista_de_objeto"]) &&
                        isset($_SESSION["lista_de_verbo"]) && isset($_SESSION["lista_de_estado"]) &&
                        isset($_SESSION["lista_de_conceitos"]) && isset($_SESSION["lista_de_relacoes"]) &&
                        isset($_SESSION["lista_de_axiomas"])) {
                    $sujeitos = $_SESSION["lista_de_sujeito"];
                    $objetos = $_SESSION["lista_de_objeto"];
                    $verbos = $_SESSION["lista_de_verbo"];
                    $estados = $_SESSION["lista_de_estado"];
                } else {
                    echo "ERRO! <br>";
                    exit();
                }

                $subject_and_object_list = array_merge($sujeitos, $objetos);
                sort($subject_and_object_list);
                $_SESSION['lista_de_sujeito_e_objeto'] = $subject_and_object_list;


                if ($_SESSION["funcao"] == "sujeito_objeto") {
                    translate_subject_object($subject_and_object_list, &$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "verbo";
                }

                if ($_SESSION["funcao"] == "verbo") {
                    translate_verbs($verbos, &$_SESSION["lista_de_relacoes"]);
                    $_SESSION["funcao"] = "estado";
                }

                if ($_SESSION["funcao"] == "estado") {
                    traduz_estados($estados, &$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "organiza";
                }

                if ($_SESSION["funcao"] == "organiza") {
                    organizar_ontologia(&$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
                    $_SESSION["funcao"] = "fim";
                }


                //Imprime Resultados
                /*
                  print("CONCEITOS: <br>");
                  foreach( $_SESSION["lista_de_conceitos"] as $con)
                  {
                  echo "$con->nome --> $con->descricao ";
                  foreach($con->relacoes as $rel)
                  {

                  }
                  echo "<br>";
                  }

                  print("RELACOES: <br>");
                  print_r($_SESSION["lista_de_relacoes"]);
                  echo "<br>";

                  print("AXIOMAS: <br>");
                  print_r($_SESSION["lista_de_axiomas"]);
                  echo "<br>";
                 */
                echo 'O processo de gera��o de Ontologias foi conclu�do com sucesso!<br>
	N�o esque�a de clicar em Salvar.';
                ?>
            <p>
            <form method="POST" action="auxiliar_bd.php">
                <input type="hidden" value="TRUE" name="save" size="20" >
                <input type="submit" value="SALVAR">
            </form>
        </p>
                <?php
            }

            traduz();
            ?>


</body>
</html>