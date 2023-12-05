---
type:               "post"
title:              "Ansible - Les playbooks"
date:               "2023-12-05"
lastModified:       ~
tableOfContent:     true
description:        "Découverte des playbooks, élément essentiel d'Ansible qui va nous permettre d'organiser et structure nos tâches !"
thumbnail:          "content/images/blog/thumbnails/ansible-playbooks.jpg"
tags:               ["cours", "ansible", "automation", "playbook"]
categories:         ["cours"]
authors:            ["gfaivre"]
---

## Préambule

Ce cours est utilisé dans le cadre de TP au sein de l'IUT Lyon 1. Il est notamment dispensé à des étudiants peu ou pas familiers avec les stratégies d'automatisation et de déploiement des infrastructures.
Bien que très axé débutants il peut également représenté une possibilité de monter « rapidement » pour certaines équipes sur les principes fondamentaux d'Ansible afin de disposer du bagage minimal nécessaire à son utilisation.

Il s'agit bien évidemment de supports à vocation pédagogique qui **ne sont pas toujours transposables** à une activité professionnelle.

## Pré-requis

Disposer d'un **environnement de travail Ansible fonctionnel**, si ça n'est pas encore le cas vous pouvez jeter un oeil [ici](/blog/cours/ansible/ansible-premiers-pas) !

## Introduction

Un playbook Ansible est un élément central pour Ansible puisque c'est dans ce fichier de configuration (écrit en YAML toujours), que l'on va organiser et indiquer quelles actions nous souhaitons faire à Ansible. 

Les playbooks décrivent des « tâches » à exécuter sur des groupes de machines décritent dans [les inventaires](/blog/cours/ansible/ansible-les-inventaires-statiques), ils sont écrit à l'aide du format [YAML](https://yaml.org/).

!!! info "La syntaxe Yaml"
    Yaml est un langage ou plutôt un format qui permet de sérialiser des données afin qu'elles restent lisiblent pour nous. Les données sont organisées sous la forme de paire clé/valeur dans des listes ordonnées, il est de plus en plus utilisé notamment pour les fichiers de configuration.

## Mise en route

Afin de rentrer dans le vif du sujet nous allons écrire un playbook relativement simple utilisant un module que nous avons vu précédemment, le module **ping**. Nous avons en effet vu que les modules Ansible peuvent être utiliser en ligne de commande pour exécuter une tâche ponctuelle mais rappelons que le but d'Ansible est en premier lieu, d'automatiser les choses et donc de les rendres réutilisables !

### Structure générale d'un playbook

Créons un nouveau fichier que nous appelerons `ping.yml` dans notre répertoire de travail (pour rappel de mon côté `workspace/ansible`) contenant:

```yaml
---
- hosts: web

  tasks:
    - name: Check if host is alive # Description of the task
      ansible.builtin.ping: ~
```

La structure YAML permet une compréhension relativement aisée du contenu, on identifiera ainsi:

- Une clé principale appelée `hosts` permettant de définir les « hôtes » (machines distantes) concernées par les tâches qui vont suivre;
- Une sous clé `tasks` permettant de définir les différentes « tâches » ou actions que nous souhaitons déclencher vers nos machines distantes.

Rappelez-vous nous pouvons traduire les instructions suivantes sous forme de tableaux:

```php
array (
    'hosts' => 'web',
    'tasks' => 
        array (
            0 => 
                array (
                    'name' => 'Check if host is alive',
                    'ansible.builtin.ping' => NULL,
                ),
        ),
  )
```

On constate encore plus aisément sous cette forme que la clé `tasks` permet de définir un nombre indéfini de « tâches » à exécuter, celles-ci ayant toujours au moins pour structure:

- Un nom (clé `name`);
- L'appel à un module ansible (ici nous faisons appel au module `ping` utilisé précédemment) chaque module acceptant ou non des paramètres.

!!! info "Les modules Ansible"
    Un module pour Ansible est un composant logiciel qui encapsule une tâche spécifique. 
    Pour résumer, il s'agit d'un **bloc de code** qui est exécuté par Ansible pour accomplir une **action donnée** sur un **système cible**. On trouve notamment des modules capables de **gérer des fichiers**, d'**exécuter des commandes** ou encore de **gérer des services**. 
    Ils peuvent être écrits dans divers langages de programmation, mais la plupart sont écrits en **Python**. Ansible vient avec une large gamme de modules intégrés pour gérer différents aspects des systèmes, des applications et des infrastructures.

### La commande ansible-playbook

Nous avons déjà vu les commandes `ansible` et `ansible-inventory` au tour de `ansible-playbook`

En exécutant cette commande `ansible-playbook ping.yml -i inventories` vous devriez obtenir la sortie suivante ou équivalente (si tout se passe bien).

<figure>
    <img src="content/images/blog/2023/ansible/ansible-playbook/ansible-playbook-ping.png">
    <figcaption>
      <span class="figure__legend">Utilisation du module ping dans un playbook.</span>
    </figcaption>
</figure>

Nous venons donc de faire appel au module ping mais à l'intérieur d'un playbook. Ce que nous avons en retour et une sortie type d'Ansible sur laquelle on peut remarquer quelques informations toujours intéressantes (tout en bas) avec notamment:

- `ok`: Le nombre de tâches (tasks) qui se sont correctement exécutées;
- `changed`: Le nombre de changements opérés sur nos machines cibles;
- `unreachable`: Le nombre de machine injoignable (par soucis de réseau par exemple);
- `failed`: Le nombre de tâches en erreur;
- `skipped`: Le nombre de tâches qui ont été ignorées (par exemple si une tâches ne remplie pas certaines conditions prédéfinies).

Il est bon de noté également qu'un playbook peut contenir plusieurs « plays » nous pouvons donc avoir des tâches attribuées à différents groupe cible comme ceci: 

```yaml
# in file ping.yml
---
- hosts: webservers

  tasks:
    - name: Check if host is alive # Description of the task
      ansible.builtin.ping:

- hosts: dbservers

  tasks:
    - name: Get stats of a file
      ansible.builtin.stat:
        path: /etc/hosts
```

Nous avons dans ce cas de figure deux groupes concernés chacun par une « tâche »:

- Le groupe `webservers` (contenant donc 2 machines) sur lequel nous exécutons le module ping;
- Le groupe `dbservers` (contenant également 2 machines) sur lequel nous exécutons le module `stat`.

!!! info "Le module stat"
    Le module stat permet d'exécuter la commande système `stat` sur un fichier du serveur cible. Il prends différents paramètres que l'on peut retrouver sur la [documentation officielle](https://docs.ansible.com/ansible/latest/collections/ansible/builtin/stat_module.html) dont le paramètre `path` qui lui est obligatoire.

Rappelez-vous également qu'un « plays » peut avoir à exécuter plusieurs tâches les unes à la suite des autres, exemple: 

```yaml
# in file ping.yml
---
- hosts: webservers

  tasks:
    - name: Check if host is alive # Description of the task
      ansible.builtin.ping:
    - name: Execute a simple command
      ansible.builtin.shell: echo "Ansible was here !" > ansible.txt
        
- hosts: dbservers

  tasks:
    - name: Get stats of a file
      ansible.builtin.stat:
        path: /etc/hosts
```

Vous devriez, en jouant votre playbook (`ansible-playbook ping -i inventories`) obtenir une sortie équivalente à:


## Aller plus loin avec les sources

- https://docs.ansible.com/ansible/latest/playbook_guide/playbooks_intro.html
- https://docs.ansible.com/ansible/2.9/modules/list_of_all_modules.html
